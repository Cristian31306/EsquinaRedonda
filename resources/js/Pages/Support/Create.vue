<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { 
    Send, 
    ArrowLeft, 
    Info,
    AlertCircle
} from 'lucide-vue-next';

const form = useForm({
    subject: '',
    priority: 'medium',
    message: '',
});

const submit = () => {
    form.post(route('support.store'));
};

</script>

<template>
    <Head title="Nuevo Ticket de Soporte" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('support.index')" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-sky-600 hover:border-sky-200 transition-all">
                    <ArrowLeft class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Nuevo Ticket</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Describe tu solicitud para ayudarte mejor</p>
                </div>
            </div>
        </template>

        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-[2.5rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <form @submit.prevent="submit" class="space-y-8">
                    <!-- Asunto -->
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Asunto de la solicitud</label>
                        <input 
                            v-model="form.subject"
                            type="text" 
                            placeholder="Ej: Problema con impresión de recibos"
                            class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all outline-none font-bold text-slate-700"
                            required
                        />
                        <div v-if="form.errors.subject" class="text-rose-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ form.errors.subject }}</div>
                    </div>

                    <!-- Prioridad -->
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nivel de Prioridad</label>
                        <div class="grid grid-cols-3 gap-4">
                            <button 
                                v-for="p in ['low', 'medium', 'high']" 
                                :key="p"
                                type="button"
                                @click="form.priority = p"
                                :class="[
                                    form.priority === p 
                                        ? (p === 'high' ? 'bg-rose-500 text-white shadow-lg shadow-rose-200' : 'bg-sky-600 text-white shadow-lg shadow-sky-200')
                                        : 'bg-slate-50 text-slate-400 hover:bg-slate-100'
                                ]"
                                class="py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all"
                            >
                                {{ p === 'low' ? 'Baja' : (p === 'medium' ? 'Media' : 'Alta') }}
                            </button>
                        </div>
                    </div>

                    <!-- Mensaje -->
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Descripción detallada</label>
                        <textarea 
                            v-model="form.message"
                            rows="6"
                            placeholder="Explícanos con detalle qué sucede o qué necesitas..."
                            class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all outline-none resize-none font-bold text-slate-700 leading-relaxed"
                            required
                        ></textarea>
                        <div v-if="form.errors.message" class="text-rose-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ form.errors.message }}</div>
                    </div>

                    <!-- Help Box -->
                    <div class="bg-indigo-50/50 rounded-2xl p-6 flex gap-4 border border-indigo-100">
                        <Info class="w-6 h-6 text-indigo-500 shrink-0" />
                        <p class="text-[11px] font-bold text-indigo-900 leading-relaxed">
                            Nuestro equipo responderá a través de este sistema. Recibirás una notificación por correo cuando haya una actualización en tu ticket.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        :disabled="form.processing"
                        type="submit"
                        class="w-full py-5 bg-sky-600 hover:bg-sky-700 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all shadow-xl shadow-sky-200 flex items-center justify-center gap-3 active:scale-[0.98] disabled:opacity-50 disabled:cursor-wait"
                    >
                        <template v-if="form.processing">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Enviando...
                        </template>
                        <template v-else>
                            <Send class="w-4 h-4" />
                            Abrir Ticket de Soporte
                        </template>
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
