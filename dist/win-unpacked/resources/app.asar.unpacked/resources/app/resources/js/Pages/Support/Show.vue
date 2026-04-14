<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, watch, nextTick, computed } from 'vue';
import { 
    Send, 
    ArrowLeft, 
    User, 
    Calendar,
    CheckCircle2,
    Lock,
    ShieldCheck,
    Clock
} from 'lucide-vue-next';

const props = defineProps({
    ticket: Object
});

const page = usePage();

const form = useForm({
    message: '',
});

const messagesContainer = ref(null);
const pendingMessages = ref([]);

// Combinar mensajes reales del servidor con los pendientes
const allMessages = computed(() => {
    return [...props.ticket.messages, ...pendingMessages.value];
});

const scrollToBottom = (behavior = 'auto') => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTo({
            top: messagesContainer.value.scrollHeight,
            behavior: behavior
        });
    }
};

const submitReply = () => {
    if (form.processing || !form.message.trim()) return;

    const originalMessage = form.message;
    const tempId = 'temp_' + Date.now();
    const currentUser = page.props.auth.user;
    
    // Añadir a mensajes pendientes al instante
    pendingMessages.value.push({
        id: tempId,
        user_id: props.ticket.user_id === currentUser.id ? props.ticket.user_id : currentUser.id, 
        user: currentUser,
        message: originalMessage,
        created_at: new Date().toISOString(),
        isPending: true // Marcador visual opcional
    });
    
    form.message = '';
    form.clearErrors();
    form.processing = true; // Para no hacer spam de Enters
    
    nextTick(() => scrollToBottom('auto')); // Instantáneo, sin slide lento

    // Petición de fondo
    window.axios.post(route('support.reply', props.ticket.id), {
        message: originalMessage
    }).then(() => {
        form.processing = false;
        // Forzar al servidor a enviarnos los mensajes reales para reescribir
        router.reload({ only: ['ticket'], preserveScroll: true });
    }).catch((error) => {
        form.processing = false;
        if (error.response?.data?.errors?.message) {
            form.errors.message = error.response.data.errors.message[0];
        } else {
            form.errors.message = 'Error de conexión.';
        }
        form.message = originalMessage; 
        pendingMessages.value = pendingMessages.value.filter(m => m.id !== tempId); 
    });
};

const onKeydown = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        submitReply();
    }
};

const closeTicket = () => {
    if (confirm('¿Estás seguro de cerrar este ticket? Esta acción lo marcará como resuelto.')) {
        useForm({}).patch(route('support.close', props.ticket.id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('es-CO', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Polling for real-time updates
let interval = null;
let stopInertiaListener = null;

const fetchLatestMessages = () => {
    if (!form.processing && document.visibilityState === 'visible') {
        router.reload({ 
            only: ['ticket'],
            preserveState: true,
            preserveScroll: true
        });
    }
};

const handleVisibilityChange = () => {
    if (document.visibilityState === 'visible') {
        fetchLatestMessages();
    }
};

onMounted(() => {
    scrollToBottom('auto');
    
    // Al salir a otro módulo, matar el polling instantáneamente para evitar choque de cancelaciones
    stopInertiaListener = router.on('before', () => {
        if (interval) clearInterval(interval);
    });

    // Actualizar AL INSTANTE al enfocar ventana
    document.addEventListener('visibilitychange', handleVisibilityChange);

    interval = setInterval(fetchLatestMessages, 3000); 
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
    if (stopInertiaListener) stopInertiaListener();
    document.removeEventListener('visibilitychange', handleVisibilityChange);
});

// Limpiar pendientes cuando el servidor manda la actualización fresca
watch(() => props.ticket.messages, (newMessages) => {
    if (pendingMessages.value.length > 0) {
        // Obtenemos los textos de los mensajes reales del usuario actual
        const realTexts = newMessages
                            .filter(m => m.user_id === page.props.auth.user.id)
                            .map(m => m.message);
        
        // Removemos de pendientes aquellos que ya llegaron reales del servidor
        pendingMessages.value = pendingMessages.value.filter(pending => !realTexts.includes(pending.message));
    }
    nextTick(() => scrollToBottom('smooth'));
}, { deep: true });

</script>

<template>
    <Head :title="'Ticket #' + ticket.id + ' - ' + ticket.subject" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('support.index')" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-sky-600 hover:border-sky-200 transition-all">
                        <ArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Ticket #{{ ticket.id }}</h2>
                            <span v-if="ticket.status === 'closed'" class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-200 ml-2">Resuelto</span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">{{ ticket.subject }}</p>
                    </div>
                </div>
                
                <button 
                    v-if="ticket.status !== 'closed'"
                    @click="closeTicket"
                    class="flex items-center gap-2 px-5 py-2.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border border-emerald-200 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all"
                >
                    <CheckCircle2 class="w-4 h-4" />
                    Cerrar Ticket
                </button>
            </div>
        </template>

        <div class="max-w-4xl mx-auto flex flex-col h-[75vh] min-h-[500px]">
            <!-- Messages Conversation -->
            <div ref="messagesContainer" class="flex-1 overflow-y-auto pr-4 space-y-6 no-scrollbar pb-6 mt-4">
                <div 
                    v-for="(msg, index) in allMessages" 
                    :key="msg.id"
                    class="flex flex-col"
                    :class="[msg.user_id === $page.props.auth.user.id ? 'items-end' : 'items-start']"
                >
                    <div class="flex items-center gap-2 mb-1.5 px-2">
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ formatDate(msg.created_at) }}</span>
                        <span v-if="msg.user?.role === 'super_admin'" class="flex items-center gap-1 px-2 py-0.5 bg-sky-100 text-sky-700 rounded-md text-[8px] font-black uppercase tracking-widest">
                            <ShieldCheck class="w-2.5 h-2.5" />
                            Algorah Team
                        </span>
                        <span v-else class="text-[9px] font-black uppercase tracking-widest text-slate-600">{{ msg.user?.name || 'Usuario' }}</span>
                    </div>

                    <div 
                        class="max-w-[85%] px-5 py-3.5 rounded-[1.25rem] text-sm leading-relaxed"
                        :class="[
                            msg.user_id === $page.props.auth.user.id 
                                ? 'bg-sky-600 text-white rounded-br-sm shadow-md shadow-sky-100/50' 
                                : 'bg-white text-slate-700 border border-slate-100 shadow-sm rounded-bl-sm'
                        ]"
                    >
                        {{ msg.message }}
                    </div>
                </div>
            </div>

            <!-- Reply Box -->
            <div v-if="ticket.status !== 'closed'" class="bg-white rounded-[2rem] p-4 shadow-xl shadow-slate-200/50 border border-slate-100 shrink-0 mb-4 mx-2">
                <form @submit.prevent="submitReply" class="flex items-end gap-3 shrink-0">
                    <div class="flex-1">
                        <textarea 
                            v-model="form.message"
                            @keydown="onKeydown"
                            rows="1"
                            style="min-height: 52px; max-height: 120px;"
                            placeholder="Escribe un mensaje (Enter para enviar)..."
                            class="w-full px-5 py-3.5 bg-slate-50 border-transparent rounded-[1.5rem] focus:bg-white focus:ring-2 focus:ring-sky-100 focus:border-sky-500 transition-all outline-none font-medium text-slate-700 text-sm leading-tight leading-5"
                            :class="{'border-rose-500 ring-rose-100': form.errors.message}"
                            required
                        ></textarea>
                        <p v-if="form.errors.message" class="text-[9px] font-black text-rose-500 uppercase tracking-widest mt-1 ml-4">{{ form.errors.message }}</p>
                    </div>
                    <button 
                        :disabled="form.processing && !form.message"
                        type="submit"
                        class="w-12 h-12 shrink-0 bg-sky-600 hover:bg-sky-700 text-white rounded-[1.25rem] flex items-center justify-center shadow-md shadow-sky-100 transition-all active:scale-95 disabled:opacity-50"
                    >
                        <Send class="w-5 h-5 ml-1" />
                    </button>
                </form>
            </div>

            <!-- Closed Notice -->
            <div v-else class="bg-slate-100/50 rounded-[2rem] p-6 text-center border-2 border-dashed border-slate-200 mt-4 shrink-0 mx-2">
                <div class="flex items-center justify-center gap-3 text-slate-400">
                    <Lock class="w-5 h-5" />
                    <span class="text-xs font-black uppercase tracking-[0.2em]">Ticket cerrado y archivado</span>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
