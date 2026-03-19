<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import ShiftSummary from '@/Components/ShiftSummary.vue';

const props = defineProps({
    shift: Object,
});

const calculateTotalExpected = () => {
    const opening = parseFloat(props.shift.opening_cash);
    const sales = props.shift.payments.reduce((acc, p) => acc + parseFloat(p.amount), 0);
    return opening + sales;
};

const difference = props.shift.status === 'closed' 
    ? (props.shift.closing_cash_declared - calculateTotalExpected()) 
    : 0;

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const formatTime = (date) => {
    return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const printReport = () => {
    window.print();
};
</script>

<template>
    <Head title="Reporte de Turno" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center no-print">
                <div>
                    <h2 class="text-xl font-black tracking-tight text-slate-900 uppercase">Reporte de Auditoría</h2>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1">Cierre de Turno #{{ shift.id }}</p>
                </div>
                <div class="flex gap-4 no-print">
                    <button @click="printReport" class="px-6 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all shadow-lg">Imprimir Ticket</button>
                    <Link :href="route('shifts.history')" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-400 text-[9px] font-black uppercase tracking-widest rounded-xl hover:text-slate-900 transition-all">Volver</Link>
                </div>
            </div>
        </template>

        <div class="space-y-8 pb-12 no-print">
            <!-- Header Info Card -->
            <div class="bg-indigo-950 rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute right-0 top-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div>
                        <p class="text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-3">Responsable</p>
                        <h3 class="text-2xl font-black tracking-tight">{{ shift.user.name }}</h3>
                        <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-[0.2em] mt-1">Administrador de Turno</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-3">Periodo</p>
                        <div class="flex items-center gap-4">
                            <div class="text-center bg-white/10 px-4 py-2 rounded-2xl border border-white/10">
                                <p class="text-[10px] font-black">{{ formatTime(shift.start_time) }}</p>
                                <p class="text-[7px] font-bold text-indigo-200 uppercase tracking-tighter">Inicio</p>
                            </div>
                            <div class="w-4 h-px bg-white/20"></div>
                            <div class="text-center bg-white/10 px-4 py-2 rounded-2xl border border-white/10">
                                <p class="text-[10px] font-black">{{ shift.end_time ? formatTime(shift.end_time) : '--:--' }}</p>
                                <p class="text-[7px] font-bold text-indigo-200 uppercase tracking-tighter">Fin</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-3">Fecha de Operación</p>
                        <h3 class="text-2xl font-black tracking-tight">{{ formatDate(shift.start_time) }}</h3>
                        <span class="inline-block mt-2 px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest" :class="shift.status === 'open' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-white/10 text-white/50 border border-white/10'">
                            {{ shift.status === 'open' ? 'Turno en Curso' : 'Turno Finalizado' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Money Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white border border-slate-200 p-6 rounded-3xl shadow-sm">
                    <p class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mb-2">Ventas Registradas en Sistema</p>
                    <h4 class="text-2xl font-black text-slate-900 tracking-tighter">${{ new Intl.NumberFormat().format(shift.payments.reduce((acc, p) => acc + parseFloat(p.amount), 0)) }}</h4>
                </div>
                <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-3xl shadow-sm">
                    <p class="text-[8px] font-black text-indigo-600 uppercase tracking-widest mb-2">Total Esperado en Caja</p>
                    <h4 class="text-2xl font-black text-indigo-950 tracking-tighter">${{ new Intl.NumberFormat().format(shift.payments.reduce((acc, p) => acc + parseFloat(p.amount), 0)) }}</h4>
                </div>
                <div class="bg-slate-900 text-white p-6 rounded-3xl shadow-2xl">
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2">Declarado por Op. (Recaudo)</p>
                    <h4 class="text-2xl font-black tracking-tighter">${{ new Intl.NumberFormat().format(shift.closing_cash_declared || 0) }}</h4>
                </div>
                <div class="p-6 rounded-3xl shadow-lg border" :class="difference < 0 ? 'bg-rose-50 border-rose-100 text-rose-600' : difference > 0 ? 'bg-indigo-50 border-indigo-100 text-indigo-600' : 'bg-emerald-50 border-emerald-100 text-emerald-600'">
                    <p class="text-[8px] font-black uppercase tracking-widest mb-2">Balance (Diferencia)</p>
                    <h4 class="text-2xl font-black tracking-tighter">
                        {{ difference > 0 ? '+' : '' }}${{ new Intl.NumberFormat().format(difference) }}
                    </h4>
                </div>
            </div>

            <!-- Transaction Detail -->
            <div class="bg-white rounded-[2.5rem] border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Detalle de Transacciones</h3>
                    <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 px-4 py-1.5 rounded-full uppercase tracking-widest">
                        Total Vehículos: {{ shift.payments.length }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest">Placa</th>
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest">Categoría</th>
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest">Hora Salida</th>
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-right">Valor Pagado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="payment in shift.payments" :key="payment.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-8 py-4">
                                    <div class="inline-block px-3 py-1 bg-slate-900 text-white rounded-md font-black text-xs tracking-widest">
                                        {{ payment.ticket?.vehicle?.plate || '---' }}
                                    </div>
                                </td>
                                <td class="px-8 py-4 capitalize text-xs font-bold text-slate-600 tracking-widest">
                                    {{ payment.ticket?.vehicle?.type || '---' }}
                                </td>
                                <td class="px-8 py-4">
                                    <p class="text-xs font-black text-slate-900">{{ formatTime(payment.created_at) }}</p>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <span class="text-xs font-black text-slate-900 tracking-tighter">${{ new Intl.NumberFormat().format(payment.amount) }}</span>
                                </td>
                            </tr>
                            <tr v-if="shift.payments.length === 0">
                                <td colspan="4" class="px-8 py-20 text-center text-slate-300">
                                    <p class="text-[9px] font-black uppercase tracking-[0.3em]">Sin actividad económica en este turno</p>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-900 text-white">
                            <tr>
                                <td colspan="3" class="px-8 py-4 text-[9px] font-black uppercase tracking-widest text-right opacity-50">Total Recaudado</td>
                                <td class="px-8 py-4 text-right">
                                    <span class="text-lg font-black tracking-tighter">${{ new Intl.NumberFormat().format(shift.payments.reduce((acc, p) => acc + parseFloat(p.amount), 0)) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <!-- Audit Notes (Print only) -->
            <div class="hidden print:block mt-12 pt-12 border-t-2 border-slate-100">
                <div class="grid grid-cols-2 gap-20">
                    <div>
                        <div class="w-full border-b border-slate-300 h-24"></div>
                        <p class="text-[10px] font-black uppercase tracking-widest mt-4 text-center">Firma Responsable Turno</p>
                    </div>
                    <div>
                        <div class="w-full border-b border-slate-300 h-24"></div>
                        <p class="text-[10px] font-black uppercase tracking-widest mt-4 text-center">Firma Administración</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Resumen para Impresora Térmica -->
        <ShiftSummary :shift="shift" />
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body {
        background: white !important;
    }
    .bg-indigo-950 {
        background-color: #1e1b4b !important;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
    .text-white {
        color: white !important;
    }
}
</style>
