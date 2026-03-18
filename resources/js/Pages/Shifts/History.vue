<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    shifts: Object,
});
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

        <div class="h-full flex flex-col space-y-8">
            <!-- Auditoría de Cierres (History) -->
            <div class="bg-white rounded-[2.5rem] border border-slate-200 overflow-hidden shadow-sm flex flex-col min-h-0">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center text-slate-900">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-widest">Historial de Turnos Anteriores</h3>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">Control de cuadres y diferencias detectadas</p>
                    </div>
                </div>
                <div class="overflow-y-auto no-scrollbar flex-1">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest">Fecha / Hora Cierre</th>
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
                                <td class="px-8 py-4 text-right">
                                    <span class="text-xs font-black text-slate-900 tracking-tighter">${{ new Intl.NumberFormat().format(shift.closing_cash_declared || 0) }}</span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <template v-if="shift.status === 'closed'">
                                        <span 
                                            class="text-xs font-black tracking-tighter"
                                            :class="(shift.closing_cash_declared - shift.payments.reduce((acc, p) => acc + parseFloat(p.amount), 0)) < 0 ? 'text-rose-600' : (shift.closing_cash_declared - shift.payments.reduce((acc, p) => acc + parseFloat(p.amount), 0)) > 0 ? 'text-emerald-600' : 'text-slate-400'"
                                        >
                                            {{ (shift.closing_cash_declared - shift.payments.reduce((acc, p) => acc + parseFloat(p.amount), 0)) > 0 ? '+' : '' }}
                                            ${{ new Intl.NumberFormat().format(shift.closing_cash_declared - shift.payments.reduce((acc, p) => acc + parseFloat(p.amount), 0)) }}
                                        </span>
                                    </template>
                                    <span v-else class="text-[9px] text-slate-300 uppercase tracking-widest font-black">---</span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <template v-if="shift.status === 'closed'">
                                            <span 
                                                v-if="(shift.closing_cash_declared - shift.payments.reduce((acc, p) => acc + parseFloat(p.amount), 0)) === 0"
                                                class="text-[8px] font-black text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg uppercase tracking-widest border border-emerald-100"
                                            >Cuadre Perfecto</span>
                                            <span 
                                                v-else-if="(shift.closing_cash_declared - shift.payments.reduce((acc, p) => acc + parseFloat(p.amount), 0)) < 0"
                                                class="text-[8px] font-black text-rose-600 bg-rose-50 px-3 py-1 rounded-lg uppercase tracking-widest border border-rose-100"
                                            >Faltante</span>
                                            <span 
                                                v-else
                                                class="text-[8px] font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg uppercase tracking-widest border border-indigo-100"
                                            >Sobrante</span>
                                        </template>
                                        <span v-else class="text-[8px] font-black text-emerald-500 uppercase tracking-widest">En Curso</span>
                                        
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
                                <td colspan="4" class="px-8 py-20 text-center text-slate-300">
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
    </AuthenticatedLayout>
</template>
