<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Plus, 
    MessageSquare, 
    Calendar, 
    User,
    AlertCircle,
    CheckCircle2,
    Clock,
    ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
    tickets: Array
});

const getStatusColor = (status) => {
    switch (status) {
        case 'open': return 'bg-sky-100 text-sky-700 border-sky-200';
        case 'in_progress': return 'bg-amber-100 text-amber-700 border-amber-200';
        case 'closed': return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        default: return 'bg-slate-100 text-slate-700 border-slate-200';
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'open': return 'Abierto';
        case 'in_progress': return 'En Progreso';
        case 'closed': return 'Cerrado';
        default: return status;
    }
};

const getPriorityLabel = (priority) => {
    switch (priority) {
        case 'low': return 'Baja';
        case 'medium': return 'Media';
        case 'high': return 'Alta';
        default: return priority;
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-CO', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

</script>

<template>
    <Head title="Soporte Técnico" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Soporte Técnico</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Gestión de tickets y asistencia</p>
                </div>
                <Link 
                    v-if="$page.props.auth.user.role === 'admin'"
                    :href="route('support.create')" 
                    class="flex items-center gap-2 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-sky-100"
                >
                    <Plus class="w-4 h-4" />
                    Nuevo Ticket
                </Link>
            </div>
        </template>

        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Empty State -->
            <div v-if="tickets.length === 0" class="bg-white rounded-[2.5rem] p-20 text-center border-2 border-dashed border-slate-100">
                <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <MessageSquare class="w-10 h-10" />
                </div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight mb-2">No hay tickets activos</h3>
                <p class="text-slate-400 text-sm max-w-sm mx-auto mb-8">Si tienes alguna duda técnica o problema con la plataforma, abre un ticket y nuestro equipo te ayudará pronto.</p>
                <Link 
                    v-if="$page.props.auth.user.role === 'admin'"
                    :href="route('support.create')" 
                    class="inline-flex items-center gap-2 px-8 py-4 bg-sky-600 hover:bg-sky-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-sky-100"
                >
                    Abrir mi primer ticket
                </Link>
            </div>

            <!-- Tickets List -->
            <div v-else class="grid gap-4">
                <Link 
                    v-for="ticket in tickets" 
                    :key="ticket.id"
                    :href="route('support.show', ticket.id)"
                    class="group bg-white rounded-[2rem] p-6 border border-slate-100 hover:border-sky-300 hover:shadow-2xl hover:shadow-sky-100/50 transition-all duration-300 flex items-center gap-6"
                >
                    <!-- Icon/Status -->
                    <div class="w-14 h-14 shrink-0 rounded-2xl flex items-center justify-center" :class="getStatusColor(ticket.status)">
                        <CheckCircle2 v-if="ticket.status === 'closed'" class="w-7 h-7" />
                        <Clock v-else-if="ticket.status === 'in_progress'" class="w-7 h-7" />
                        <AlertCircle v-else class="w-7 h-7" />
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">#{{ ticket.id }}</span>
                            <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                            <span class="text-[10px] font-black uppercase tracking-widest" :class="ticket.priority === 'high' ? 'text-rose-500' : 'text-slate-500'">
                                Prioridad {{ getPriorityLabel(ticket.priority) }}
                            </span>
                        </div>
                        <h4 class="text-lg font-black text-slate-800 truncate group-hover:text-sky-600 transition-colors">
                            {{ ticket.subject }}
                        </h4>
                        <div class="flex items-center gap-4 mt-2">
                            <div class="flex items-center gap-1.5 text-slate-400">
                                <User class="w-3.5 h-3.5" />
                                <span class="text-[10px] font-bold uppercase tracking-tight">{{ ticket.user?.name || 'Desconocido' }}</span>
                                <span v-if="ticket.tenant" class="text-[10px] font-bold text-slate-300">| {{ ticket.tenant?.name }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-400">
                                <Calendar class="w-3.5 h-3.5" />
                                <span class="text-[10px] font-bold uppercase tracking-tight">Actualizado {{ formatDate(ticket.last_reply_at || ticket.created_at) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Badge & Arrow -->
                    <div class="flex items-center gap-6">
                        <div class="hidden sm:flex px-4 py-1.5 rounded-full border text-[10px] font-black uppercase tracking-widest" :class="getStatusColor(ticket.status)">
                            {{ getStatusLabel(ticket.status) }}
                        </div>
                        <ChevronRight class="w-5 h-5 text-slate-300 group-hover:text-sky-600 group-hover:translate-x-1 transition-all" />
                    </div>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
