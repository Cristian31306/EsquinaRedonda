<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import ShiftSummary from '@/Components/ShiftSummary.vue';
import { ref, nextTick } from 'vue';

const props = defineProps({
    activeShift: Object,
});

const showOpenModal = ref(false);
const showCloseModal = ref(false);
const printData = ref(null);

const openForm = useForm({});

const closeForm = useForm({
    closing_cash_declared: '',
});

const openShift = () => {
    openForm.post(route('shifts.open'), {
        onSuccess: () => {
            showOpenModal.value = false;
        },
    });
};

const closeShift = () => {
    closeForm.post(route('shifts.close'), {
        onSuccess: (page) => {
            showCloseModal.value = false;
            // Capturar datos para imprimir si vienen en la respuesta
            if (page.props.flash.printShift) {
                printData.value = page.props.flash.printShift;
                nextTick(() => {
                    window.print();
                    // Limpiar después de imprimir para no repetir si hay cambios de estado
                    setTimeout(() => { printData.value = null; }, 1000);
                });
            }
            closeForm.reset();
        },
    });
};
</script>

<template>
    <Head title="Control de Caja" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center text-slate-900">
                <div>
                    <h2 class="text-xl font-black tracking-tight uppercase">Control de Caja</h2>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1">Operación de Turno & Arqueo Ciego</p>
                </div>
            </div>
        </template>

        <div class="h-full flex flex-col space-y-8">
            <!-- Active Shift Status -->
            <div v-if="activeShift" class="bg-indigo-950 rounded-[3rem] p-12 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute right-0 top-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-12">
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_15px_rgba(16,185,129,0.5)]"></span>
                            <p class="text-[9px] font-black text-indigo-300 uppercase tracking-[0.3em]">Turno Activo en Curso</p>
                        </div>
                        <h3 class="text-4xl font-black tracking-tighter mb-2 italic">Operando Correctamente</h3>
                        <p class="text-xs font-medium text-indigo-200 opacity-60">El sistema está registrando todos los movimientos de forma segura.</p>
                    </div>
                    
                    <button 
                        @click="showCloseModal = true"
                        class="w-full md:w-auto px-10 py-5 bg-white text-indigo-950 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-50 transition-all shadow-xl hover:-translate-y-1 active:translate-y-0"
                    >
                        Entregar Turno / Cerrar Caja
                    </button>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="flex-1 flex flex-col items-center justify-center bg-white rounded-[3rem] border-2 border-dashed border-slate-200 p-20 text-center">
                <div class="w-24 h-24 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-slate-300"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75l2.25 2.25" /></svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-3">No hay un turno abierto</h3>
                <p class="text-sm text-slate-400 mb-10 max-w-sm">Para comenzar a registrar pagos y movimientos, debes iniciar la operación del día.</p>
                <button 
                    @click="showOpenModal = true"
                    class="px-10 py-5 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100"
                >
                    Iniciar Operación / Abrir Turno
                </button>
            </div>
        </div>

        <!-- Modals: Blind Closing -->
        <transition name="fade">
            <div v-if="showOpenModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 sm:p-0">
                <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-md" @click="showOpenModal = false"></div>
                <div class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
                    <div class="p-10">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-2 text-center">Abrir Turno</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed mb-8 text-center">¿Confirmas el inicio de la operación?</p>
                        
                        <div class="flex flex-col gap-3">
                            <button 
                                @click="openShift"
                                :disabled="openForm.processing"
                                class="w-full py-5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 disabled:opacity-50"
                            >
                                <span v-if="openForm.processing">Iniciando...</span>
                                <span v-else>Confirmar Apertura</span>
                            </button>
                            <button 
                                @click="showOpenModal = false"
                                class="w-full py-5 bg-slate-50 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:text-slate-900 transition-all"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <transition name="fade">
            <div v-if="showCloseModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 sm:p-0">
                <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-md" @click="showCloseModal = false"></div>
                <div class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
                    <div class="p-12">
                        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-950 mb-6 mx-auto border border-indigo-100 shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 text-center tracking-tighter uppercase mb-2">Cierre de Caja</h3>
                        <p class="text-[9px] text-slate-400 text-center font-bold uppercase tracking-widest mb-10">Declare el total de dinero recaudado</p>
                        
                        <form @submit.prevent="closeShift" class="space-y-10">
                            <div class="text-center group">
                                <div class="relative inline-block border-b-2 border-slate-100 group-hover:border-indigo-950 transition-colors py-2">
                                     <input 
                                        v-model="closeForm.closing_cash_declared"
                                        type="number" 
                                        class="bg-transparent border-none text-center text-4xl font-black text-slate-900 focus:ring-0 w-48 placeholder:text-slate-100"
                                        placeholder="0"
                                        autofocus
                                        required
                                    >
                                    <div class="absolute -left-8 top-1/2 -translate-y-1/2 text-2xl font-black text-slate-200">$</div>
                                </div>
                                <p v-if="closeForm.errors.closing_cash_declared" class="mt-4 text-[9px] font-bold text-rose-500 uppercase tracking-widest">{{ closeForm.errors.closing_cash_declared }}</p>
                            </div>

                            <div class="flex flex-col gap-3">
                                <button 
                                    type="submit"
                                    :disabled="closeForm.processing"
                                    class="w-full py-5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-black transition-all shadow-xl disabled:opacity-50"
                                >
                                    <span v-if="closeForm.processing">Cerrando Turno...</span>
                                    <span v-else>Confirmar Arqueo</span>
                                </button>
                                <button 
                                    type="button"
                                    @click="showCloseModal = false"
                                    class="w-full py-5 bg-slate-50 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:text-slate-900 transition-all font-bold"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </transition>
    </AuthenticatedLayout>

    <!-- Resumen Imprimible -->
    <ShiftSummary v-if="printData" :shift="printData" />
</template>
