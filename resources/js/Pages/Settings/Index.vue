<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object
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
                            class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615 3.001 3.001 0 0 0 3.75.615 3.001 3.001 0 0 0 3.75-.615 3.001 3.001 0 0 0 3.75.615m-15 0-1.39-1.39a1.5 1.5 0 0 1 0-2.121l1.39-1.39m15 0 1.39-1.39a1.5 1.5 0 0 0 0-2.121l-1.39-1.39" />
                            </svg>
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

                        <div class="flex justify-end pt-4">
                            <button type="submit" :disabled="form.processing"
                                class="bg-indigo-950 text-white px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-slate-900 active:scale-95 transition-all disabled:opacity-50">
                                {{ form.processing ? 'Guardando...' : 'Actualizar Identidad Corporativa' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
