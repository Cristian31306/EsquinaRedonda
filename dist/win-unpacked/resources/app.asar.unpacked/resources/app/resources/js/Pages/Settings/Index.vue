<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object,
    tenant: Object
});

const form = useForm({
    business_name: props.settings.business_name || '',
    business_nit: props.settings.business_nit || '',
    business_address: props.settings.business_address || '',
    business_phone: props.settings.business_phone || '',
    is_iva_responsible: props.settings.is_iva_responsible || 'NO',
    business_schedule: props.settings.business_schedule || 'Lunes a Sábado - 24 Horas',
    ticket_footer: props.settings.ticket_footer || '¡Gracias por su visita!',
});

const submit = () => {
    form.post(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => alert('Configuración guardada correctamente'),
    });
};

const isRegenerating = ref(false);

const regenerateToken = () => {
    if (!confirm('¿Estás seguro de que deseas generar un nuevo token? Si ya tienes terminales vinculadas, perderán la conexión hasta que actualices el token en ellas.')) {
        return;
    }
    
    isRegenerating.value = true;
    router.post(route('settings.token.regenerate'), {}, {
        preserveScroll: true,
        onFinish: () => isRegenerating.value = false
    });
};

const copyToClipboard = (text) => {
    if (!navigator.clipboard) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Token copiado al portapapeles');
        } catch (err) {
            console.error('Error al copiar:', err);
        }
        document.body.removeChild(textArea);
        return;
    }
    navigator.clipboard.writeText(text).then(() => {
        alert('Token copiado al portapapeles');
    }).catch(err => {
        console.error('Error al copiar:', err);
    });
};
</script>

<template>

    <Head title="Identidad del Negocio" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-widest">
                Identidad del Negocio
            </h2>
        </template>

        <div class="max-w-5xl mx-auto space-y-8 py-6">
            <div
                class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100 transition-all duration-300 hover:shadow-2xl">
                <div class="p-10">
                    <div class="flex items-center gap-6 mb-12">
                        <div
                            class="w-16 h-16 bg-white border border-slate-100 rounded-3xl flex items-center justify-center p-3 shadow-sm">
                            <img src="/favicon.png" alt="Logo" class="w-full h-full object-contain" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-widest">Información
                                Corporativa</h3>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Configura los
                                datos
                                legales que aparecerán en tus tickets</p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-12">
                        <!-- Identidad Legal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Nombre
                                    del
                                    Negocio</label>
                                <input v-model="form.business_name" type="text" placeholder="Ej: Esquina Redonda S.A.S"
                                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner" />
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">NIT
                                    /
                                    Identificación</label>
                                <input v-model="form.business_nit" type="text" placeholder="Ej: 901.234.567-8"
                                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner" />
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2 font-black">Responsable
                                    de IVA</label>
                                <div class="flex bg-slate-100 p-1.5 rounded-2xl">
                                    <button type="button" @click="form.is_iva_responsible = 'SI'"
                                        :class="form.is_iva_responsible === 'SI' ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-500 hover:text-slate-700'"
                                        class="flex-1 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                        SÍ ES
                                    </button>
                                    <button type="button" @click="form.is_iva_responsible = 'NO'"
                                        :class="form.is_iva_responsible === 'NO' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                        class="flex-1 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                        NO ES
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Ubicación y Operación -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Dirección
                                    Física</label>
                                <input v-model="form.business_address" type="text" placeholder="Ej: Calle 10 # 5-20"
                                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner" />
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Teléfono
                                    de
                                    contacto</label>
                                <input v-model="form.business_phone" type="text" placeholder="Ej: +57 321 456 7890"
                                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Horario
                                de
                                Atención</label>
                            <input v-model="form.business_schedule" type="text"
                                placeholder="Ej: Lunes a Domingo - 24 Horas"
                                class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner" />
                        </div>

                        <!-- Personalización de Ticket -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Mensaje
                                al Pie
                                del Ticket</label>
                            <textarea v-model="form.ticket_footer" rows="3"
                                placeholder="Ej: ¡Gracias por su confianza! Favor no dejar objetos de valor."
                                class="w-full bg-slate-50 border-none rounded-[2rem] p-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner resize-none"></textarea>
                        </div>

                        <div class="bg-indigo-50/50 rounded-[2.5rem] p-8 border border-indigo-100">
                            <div class="flex gap-6 items-start">
                                <div
                                    class="w-10 h-10 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                    </svg>
                                </div>
                                <div
                                    class="text-xs font-bold text-indigo-900/70 leading-relaxed uppercase tracking-[0.05em]">
                                    Esta información será utilizada para la generación de tickets y reportes
                                    administrativos.
                                    Los cambios se reflejarán inmediatamente en la próxima impresión.
                                </div>
                            </div>
                        </div>

                        <!-- Sincronización de Terminales (Escritorio) -->
                        <div class="bg-slate-950 rounded-[2.5rem] p-10 border border-white/5 relative overflow-hidden group">
                            <!-- Efecto de brillo -->
                            <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-600/10 blur-[100px] rounded-full group-hover:bg-indigo-600/20 transition-all duration-700"></div>
                            
                            <div class="relative z-10">
                                <div class="flex items-center gap-6 mb-8">
                                    <div class="w-14 h-14 bg-indigo-600/20 rounded-2xl flex items-center justify-center text-indigo-400 border border-indigo-500/20 shadow-lg shadow-indigo-500/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-white uppercase tracking-widest">Sincronización de Terminal</h3>
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Vincula tu aplicación de escritorio para operación offline</p>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <p class="text-sm font-medium text-slate-400 leading-relaxed max-w-2xl">
                                        Utiliza este <span class="text-indigo-400 font-bold">Token de API</span> para vincular esta terminal de escritorio. 
                                        Esto permitirá que los tickets se sincronicen automáticamente con la nube cuando haya conexión.
                                    </p>

                                    <div class="flex flex-col sm:flex-row gap-4 items-stretch">
                                        <div class="flex-1 bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center justify-between group/token overflow-hidden">
                                            <code class="text-indigo-300 font-mono text-xs truncate mr-4">
                                                {{ tenant?.api_token || 'Token no generado' }}
                                            </code>
                                            <div class="flex gap-2 shrink-0">
                                                <button 
                                                    type="button"
                                                    @click="copyToClipboard(tenant?.api_token)"
                                                    class="bg-white/10 hover:bg-white/20 text-white p-2.5 rounded-xl transition-all active:scale-95"
                                                    title="Copiar Token"
                                                    :disabled="!tenant?.api_token"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 1 1.5.124M9 17.25h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 1 1.5.124M9 17.25h3.375M9 11.25v5.25" />
                                                    </svg>
                                                </button>
                                                
                                                <button 
                                                    type="button"
                                                    @click="regenerateToken"
                                                    :disabled="isRegenerating"
                                                    class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-95 flex items-center gap-2 shadow-lg shadow-indigo-600/20"
                                                >
                                                    <svg v-if="isRegenerating" class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span v-if="!tenant?.api_token">Generar</span>
                                                    <span v-else>Regenerar</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 text-[10px] font-black text-rose-500/80 uppercase tracking-widest bg-rose-500/5 border border-rose-500/10 rounded-xl p-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.401 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                                        </svg>
                                        Importante: Mantén este token en secreto. Cualquiera con acceso al token puede sincronizar datos.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" :disabled="form.processing"
                                class="bg-indigo-950 text-white px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-slate-900 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-wait flex items-center justify-center gap-2">
                                <template v-if="form.processing">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Guardando...
                                </template>
                                <template v-else>
                                    Actualizar Identidad Corporativa
                                </template>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
