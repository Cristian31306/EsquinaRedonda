<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShiftSummary from '@/Components/ShiftSummary.vue';
import { ref, nextTick } from 'vue';

const props = defineProps({
    shifts: Object,
    activeShift: Object,
});

const printData = ref(null);
const closeForm = useForm({
    closing_cash_declared: '',
    shift_id: null
});

const showCloseAdminModal = ref(false);

const openCloseAdmin = (shift) => {
    closeForm.shift_id = shift.id;
    showCloseAdminModal.value = true;
};

const closeShiftAdmin = () => {
    closeForm.post(route('shifts.close'), {
        onSuccess: (page) => {
            showCloseAdminModal.value = false;
            if (page.props.flash.printShift) {
                printData.value = page.props.flash.printShift;
                nextTick(() => { window.print(); setTimeout(() => { printData.value = null; }, 1000); });
            }
        },
    });
};

const reprint = (shift) => {
    printData.value = shift;
    nextTick(() => {
        window.print();
        setTimeout(() => { printData.value = null; }, 1000);
    });
};
</script>

<template>
    <Head title="Auditoría de Caja" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center text-slate-900">
                <div>
                    <h2 class="text-xl font-black tracking-tight uppercase">Auditoría de Turnos</h2>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1">Control Histórico & Cuadres de Caja</p>
                </div>
            </div>
        </template>

        <div class="h-full flex flex-col space-y-8 pb-12">
            <!-- Turno Activo (Sólo para Admin) -->
            <div v-if="activeShift" class="bg-indigo-950 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden group/active">
                <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-indigo-300 border border-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                <p class="text-[8px] font-black uppercase tracking-widest text-indigo-400">Turno en Curso</p>
                            </div>
                            <h3 class="text-lg font-black tracking-tight uppercase">{{ activeShift.user.name }}</h3>
                            <p class="text-[9px] font-bold text-indigo-200 mt-0.5 opacity-60">Iniciado: {{ new Date(activeShift.start_time).toLocaleString() }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-[8px] font-black uppercase text-indigo-400 mb-1">Ventas Actuales</p>
                            <p class="text-xl font-black tracking-tighter">${{ new Intl.NumberFormat().format(activeShift.payments.reduce((acc, p) => acc + parseFloat(p.amount), 0)) }}</p>
                        </div>
                        <button 
                            @click="openCloseAdmin(activeShift)"
                            class="px-6 py-3 bg-white text-indigo-950 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-rose-50 hover:text-rose-600 transition-all shadow-xl"
                        >
                            Cerrar Turno (Admin)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Auditoría de Cierres (History) -->
            <div class="bg-white rounded-[2.5rem] border border-slate-200 overflow-hidden shadow-sm flex flex-col min-h-0">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center text-slate-900">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-widest">Historial de Turnos Cerrados</h3>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">Control de cuadres y diferencias detectadas</p>
                    </div>
                </div>
                <!-- ... existing table structure ... -->
                <div class="overflow-y-auto no-scrollbar flex-1">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest">Fecha / Hora Cierre</th>
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest">Responsable</th>
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-right">Efectivo Entregado</th>
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-right">Diferencia</th>
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-center">Resultado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="shift in shifts.data" :key="shift.id" class="hover:bg-slate-50 transition-all group">
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-200 group-hover:bg-indigo-500 transition-colors"></div>
                                        <div>
                                            <p class="text-xs font-black text-slate-900 uppercase">{{ new Date(shift.end_time || shift.start_time).toLocaleDateString() }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ new Date(shift.end_time || shift.start_time).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-slate-100 rounded-lg flex items-center justify-center text-[10px] font-black text-slate-400 group-hover:bg-indigo-950 group-hover:text-white transition-all">
                                            {{ shift.user?.name?.substring(0,2).toUpperCase() }}
                                        </div>
                                        <span class="text-[10px] font-black text-slate-900 uppercase tracking-tight">{{ shift.user?.name }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <span class="text-xs font-black text-slate-900 tracking-tighter">${{ new Intl.NumberFormat().format(shift.closing_cash_declared || 0) }}</span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <span 
                                        class="text-xs font-black tracking-tighter"
                                        :class="(shift.closing_cash_declared - shift.total_collected) < 0 ? 'text-rose-600' : (shift.closing_cash_declared - shift.total_collected) > 0 ? 'text-emerald-600' : 'text-slate-400'"
                                    >
                                        {{ (shift.closing_cash_declared - shift.total_collected) > 0 ? '+' : '' }}
                                        ${{ new Intl.NumberFormat().format(shift.closing_cash_declared - shift.total_collected) }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <span 
                                            v-if="(shift.closing_cash_declared - shift.total_collected) === 0"
                                            class="text-[8px] font-black text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg uppercase tracking-widest border border-emerald-100"
                                        >Cuadre Perfecto</span>
                                        <span 
                                            v-else-if="(shift.closing_cash_declared - shift.total_collected) < 0"
                                            class="text-[8px] font-black text-rose-600 bg-rose-50 px-3 py-1 rounded-lg uppercase tracking-widest border border-rose-100"
                                        >Faltante</span>
                                        <span 
                                            v-else
                                            class="text-[8px] font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg uppercase tracking-widest border border-indigo-100"
                                        >Sobrante</span>
                                        
                                        <button 
                                            @click="reprint(shift)"
                                            class="p-2 bg-slate-100 text-slate-400 rounded-lg hover:bg-slate-900 hover:text-white transition-all shadow-sm"
                                            title="Reimprimir Resumen"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821 2.25 9.75m0 0 4.47-4.071M2.25 9.75h9.384c3.126 0 5.625 2.53 5.625 5.655V18.75" /></svg>
                                        </button>

                                        <Link 
                                            :href="route('shifts.show', shift.id)"
                                            class="p-2 bg-slate-100 text-slate-400 rounded-lg hover:bg-slate-900 hover:text-white transition-all shadow-sm"
                                            title="Ver Reporte Completo"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="shifts.data.length === 0">
                                <td colspan="5" class="px-8 py-20 text-center text-slate-300">
                                    <p class="text-[9px] font-black uppercase tracking-[0.3em]">No hay turnos finalizados en el historial</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div v-if="shifts.links && shifts.links.length > 3" class="px-8 py-6 border-t border-slate-100 bg-slate-50/30 flex justify-center gap-2">
                    <Link 
                        v-for="link in shifts.links" 
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[link.active ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-400 hover:text-slate-900 border border-slate-200']"
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>

        <!-- Modal Cierre Admin -->
        <transition name="fade">
            <div v-if="showCloseAdminModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 sm:p-0">
                <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-md" @click="showCloseAdminModal = false"></div>
                <div class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
                    <div class="p-12">
                        <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 mb-6 mx-auto border border-rose-100 shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 text-center tracking-tighter uppercase mb-2">Cierre Administrativo</h3>
                        <p class="text-[9px] text-slate-400 text-center font-bold uppercase tracking-widest mb-10 leading-relaxed">Estás cerrando el turno de otro usuario de forma manual. Por favor declara el efectivo recolectado.</p>
                        
                        <form @submit.prevent="closeShiftAdmin" class="space-y-10">
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
                            </div>

                            <div class="flex flex-col gap-3">
                                <button 
                                    type="submit"
                                    :disabled="closeForm.processing"
                                    class="w-full py-5 bg-rose-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-rose-700 transition-all shadow-xl disabled:opacity-50"
                                >
                                    <span v-if="closeForm.processing">Cerrando Turno...</span>
                                    <span v-else>Confirmar Cierre Forzado</span>
                                </button>
                                <button 
                                    type="button"
                                    @click="showCloseAdminModal = false"
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

        <ShiftSummary v-if="printData" :shift="printData" />
    </AuthenticatedLayout>
</template>
