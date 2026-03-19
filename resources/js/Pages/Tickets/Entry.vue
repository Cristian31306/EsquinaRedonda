<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref, watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import EntryTicket from '@/Components/EntryTicket.vue';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    plate: '',
    type: props.categories?.length > 0 ? props.categories[0] : 'carro',
    observation: '',
    stay_type: null,
});

const plateInput = ref(null);
const ticketToPrint = ref(null);
const page = usePage();

// Escuchar cuando llega un nuevo ticket para imprimir desde el servidor (Flash)
watch(() => page.props.print_ticket, async (newTicket) => {
    if (newTicket) {
        ticketToPrint.value = newTicket;
        await nextTick();
        window.print();
        // Opcional: limpiar después de un momento
        setTimeout(() => { ticketToPrint.value = null; }, 1000);
    }
}, { immediate: true });

onMounted(() => {
    plateInput.value?.focus();
});

const submit = () => {
    form.post(route('tickets.store'), {
        onSuccess: () => {
            form.reset('plate', 'observation');
            plateInput.value?.focus();
        },
    });
};

const setType = (type) => {
    form.type = type;
};
</script>

<template>
    <Head title="Ingreso de Vehículo" />

    <AuthenticatedLayout>
        <!-- Área de Impresión (Solo visible por la impresora) -->
        <div class="print-container">
            <EntryTicket :ticket="ticketToPrint" />
        </div>
        <template #header>
            <div class="flex justify-between items-center text-slate-900 px-2">
                <div>
                    <h2 class="text-xl font-black tracking-tight uppercase leading-none">Nueva Entrada</h2>
                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Punto de Control</span>
                </div>
            </div>
        </template>

        <div class="h-full flex flex-col items-center justify-center -mt-8"> <!-- Compensate layouts padding -->
            <!-- Alerta de Membresía Próxima a Vencer -->
            <div v-if="page.props.membership_info && page.props.membership_info.days_left <= 7" 
                class="mb-6 max-w-4xl w-full bg-amber-50 border-2 border-amber-200 rounded-3xl p-4 flex items-center justify-between animate-in fade-in slide-in-from-top-4 duration-500">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-amber-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-amber-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-amber-900 uppercase tracking-widest">Aviso de Vencimiento</p>
                        <p class="text-xs font-bold text-amber-700">La mensualidad de este vehículo vence en <span class="font-black underline">{{ page.props.membership_info.days_left }} días</span> ({{ page.props.membership_info.end_date }}).</p>
                    </div>
                </div>
                <div class="text-[8px] font-black text-amber-400 uppercase tracking-widest mr-4">Atención</div>
            </div>

            <div class="bg-white border-2 border-slate-100 rounded-[3rem] p-8 max-w-4xl w-full shadow-[0_30px_60px_rgba(0,0,0,0.03)] animate-in slide-in-from-bottom-4 duration-500 overflow-hidden">
                
                <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                    <!-- Left Column: Input and Header -->
                    <div class="space-y-6 text-center lg:text-left">
                        <div>
                            <h1 class="text-xs font-black text-indigo-950 uppercase tracking-[0.3em] mb-1">Registro de Ingreso</h1>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Ingrese la placa del vehículo</p>
                        </div>

                        <div class="relative group">
                            <input 
                                ref="plateInput"
                                v-model="form.plate" 
                                @input="form.plate = form.plate.toUpperCase()"
                                type="text" 
                                placeholder="AAA000"
                                class="w-full bg-transparent border-none focus:ring-0 text-7xl font-black text-slate-900 placeholder:text-slate-100 uppercase tracking-tighter"
                                required
                            />
                             <div class="h-1.5 w-full bg-indigo-950 rounded-full mt-2 transition-all group-focus-within:w-full w-24 mx-auto lg:mx-0"></div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest px-1">Notas Rápidas (Ej. Ralladura)</label>
                            <input 
                                v-model="form.observation" 
                                type="text" 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-700 text-xs font-bold focus:bg-white focus:border-indigo-950 focus:ring-0 transition-all"
                                placeholder="..."
                            />
                        </div>
                    </div>

                    <!-- Right Column: Categories and Button -->
                    <div class="bg-slate-50 p-6 rounded-[2.5rem] border border-slate-100 flex flex-col justify-between h-full space-y-8">
                        <div class="space-y-4">
                            <p class="text-center lg:text-left text-[9px] font-black text-slate-400 uppercase tracking-widest px-2">Seleccione Categoría</p>
                            <div class="grid grid-cols-2 gap-2">
                                <button 
                                    v-for="cat in categories" 
                                    :key="cat"
                                    type="button"
                                    @click="setType(cat)"
                                    :class="form.type === cat ? 'bg-indigo-950 text-white shadow-lg border-indigo-950 scale-[1.03]' : 'bg-white text-slate-500 border-slate-100 hover:bg-slate-100'"
                                    class="px-4 py-4 rounded-2xl border-2 font-black text-[10px] uppercase tracking-widest transition-all duration-200"
                                >
                                    {{ cat }}
                                </button>
                            </div>
                        </div>

                        <!-- Tipo de Estadía -->
                        <div class="space-y-2 pt-2 border-t border-slate-200">
                            <p class="text-center lg:text-left text-[9px] font-black text-slate-400 uppercase tracking-widest px-2">Tipo de Estadía</p>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="form.stay_type = null"
                                    :class="form.stay_type === null ? 'bg-indigo-950 text-white border-indigo-950 scale-[1.05] shadow-lg shadow-indigo-100' : 'bg-white text-slate-500 border-slate-100 hover:bg-slate-50'"
                                    class="py-3 px-2 rounded-2xl border-2 font-black text-[8px] uppercase tracking-widest transition-all flex flex-col items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    Normal
                                </button>
                                <button type="button" @click="form.stay_type = 'overnight'"
                                    :class="form.stay_type === 'overnight' ? 'bg-indigo-950 text-white border-indigo-950 scale-[1.05] shadow-lg shadow-indigo-100' : 'bg-white text-slate-500 border-slate-100 hover:bg-slate-50'"
                                    class="py-3 px-2 rounded-2xl border-2 font-black text-[8px] uppercase tracking-widest transition-all flex flex-col items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" /></svg>
                                    Noche
                                </button>
                                <button type="button" @click="form.stay_type = 'fullday'"
                                    :class="form.stay_type === 'fullday' ? 'bg-indigo-950 text-white border-indigo-950 scale-[1.05] shadow-lg shadow-indigo-100' : 'bg-white text-slate-500 border-slate-100 hover:bg-slate-50'"
                                    class="py-3 px-2 rounded-2xl border-2 font-black text-[8px] uppercase tracking-widest transition-all flex flex-col items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m.386-6.364 1.591 1.591M18.75 12a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" /></svg>
                                    Día
                                </button>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full py-5 bg-emerald-600 text-white font-black uppercase text-xs rounded-2xl shadow-xl shadow-emerald-100 hover:bg-emerald-700 active:scale-95 transition-all tracking-[0.2em]"
                        >
                            Generar Entrada
                        </button>
                    </div>
                    <!-- TODO:
                    - [x] Transformación Light Premium (100vh)
                    - [x] Reestructurar `AuthenticatedLayout.vue` (Fondo claro, Sidebar vibrante)
                    - [x] Adaptar `Dashboard.vue` (Tarjetas blancas, alto contraste)
                    - [x] Refinar `Rates/Index.vue` (Tabla clara, modal blanco puro)
                    - [x] Compactar `Tickets/Entry.vue` y `Tickets/Exit.vue` (Diseño horizontal/compacto)
                    - [x] Refinar Iconografía del Sidebar (Iconos minimalistas y elegantes)
                    - [x] Refinar Iconografía de Tarifas (Iconos minimalistas en tabla y modal)
                    - [x] Simplificar Iconos de Categorías (Icono genérico único)
                    - [x] Implementar Impresión Automática (EntryTicket.vue + Silent Print)
                    - [x] Implementar Reimpresión en Dashboard (Acción en tabla "Vehículos en Sitio")
                    - [x] Refinar Diseño de Ticket (Compacto, Placa centrada, Fecha/Hora unificadas)
                    - [/] Centrado Total del Ticket (Layout 100% centrado)
                    - [x] Verificación de legibilidad y usabilidad
                    -->
                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Ocultar en pantalla */
.print-container {
    display: none;
}

@media print {
    /* Ocultar todo lo demás al imprimir */
    body * {
        visibility: hidden !important;
    }
    .print-container, .print-container * {
        visibility: visible !important;
        display: block !important;
    }
    .print-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    @page {
        margin: 0;
        size: auto;
    }
}
</style>
