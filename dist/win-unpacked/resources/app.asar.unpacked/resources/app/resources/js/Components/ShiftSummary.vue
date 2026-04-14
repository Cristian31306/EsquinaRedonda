<script setup>
import { computed } from 'vue';

const props = defineProps({
    shift: Object,
});

const payments = computed(() => props.shift?.payments || []);

const totalCash = computed(() => {
    return payments.value
        .filter(p => p.payment_method === 'efectivo')
        .reduce((acc, p) => acc + parseFloat(p.amount), 0);
});

const totalTransfer = computed(() => {
    return payments.value
        .filter(p => p.payment_method === 'trasnferencia')
        .reduce((acc, p) => acc + parseFloat(p.amount), 0);
});

const totalCollected = computed(() => {
    return payments.value.reduce((acc, p) => acc + parseFloat(p.amount), 0);
});

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString();
};

const formatTime = (date) => {
    if (!date) return '--:--';
    return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <div v-if="shift" class="print-summary p-8 bg-white text-black font-mono text-[10px] w-[80mm] mx-auto border border-dashed border-slate-300">
        <div class="text-center mb-6">
            <h1 class="text-lg font-black uppercase tracking-tighter">ParkiApp</h1>
            <p class="uppercase text-[8px] font-bold tracking-widest">Resumen de Cierre de Caja</p>
        </div>

        <div class="space-y-1 mb-6 pb-4 border-b border-black border-dashed">
            <div class="flex justify-between">
                <span>TURNO ID:</span>
                <span class="font-black">#{{ shift.id }}</span>
            </div>
            <div class="flex justify-between">
                <span>RESPONSABLE:</span>
                <span class="font-black uppercase">{{ shift.user?.name || 'Sistema / No identificado' }}</span>
            </div>
            <div class="flex justify-between">
                <span>FECHA:</span>
                <span>{{ formatDate(shift.start_time) }}</span>
            </div>
            <div class="flex justify-between">
                <span>HORA INICIO:</span>
                <span>{{ formatTime(shift.start_time) }}</span>
            </div>
            <div class="flex justify-between">
                <span>HORA FIN:</span>
                <span>{{ formatTime(shift.end_time || new Date()) }}</span>
            </div>
        </div>

        <div class="mb-6">
            <div class="flex justify-between font-black uppercase text-[8px] mb-2 border-b border-black pb-1">
                <span>PLACA</span>
                <span>VALOR</span>
            </div>
            <div v-for="payment in payments" :key="payment.id" class="flex justify-between mb-1">
                <span>{{ payment.ticket?.vehicle?.plate || 'S/P' }}</span>
                <span>{{ payment.payment_method === 'trasnferencia' ? '(T) ' : '' }}${{ new Intl.NumberFormat().format(payment.amount) }}</span>
            </div>
            <div v-if="payments.length === 0" class="text-center py-2 italic opacity-50">
                Sin movimientos registrados
            </div>
        </div>

        <div class="space-y-1 pt-4 border-t border-black border-dashed">
            <div class="flex justify-between text-[9px] opacity-70">
                <span>(+) TOTAL EFECTIVO:</span>
                <span>${{ new Intl.NumberFormat().format(totalCash) }}</span>
            </div>
            <div class="flex justify-between text-[9px] opacity-70">
                <span>(+) TOTAL TRANSFERENCIA:</span>
                <span>${{ new Intl.NumberFormat().format(totalTransfer) }}</span>
            </div>
            <div class="flex justify-between text-xs font-black pt-1 mb-2 border-t border-black/10">
                <span>TOTAL RECAUDADO:</span>
                <span>${{ new Intl.NumberFormat().format(totalCollected) }}</span>
            </div>
            
            <div class="flex justify-between text-xs font-black pt-2 border-t border-black">
                <span>DECLARADO EN CAJA:</span>
                <span>${{ new Intl.NumberFormat().format(shift.closing_cash_declared || 0) }}</span>
            </div>
            <div class="flex justify-between text-xs font-bold" :class="(shift.closing_cash_declared - totalCash) < 0 ? 'text-red-600' : ''">
                <span>DIFERENCIA (EFECTIVO):</span>
                <span>${{ new Intl.NumberFormat().format((shift.closing_cash_declared || 0) - totalCash) }}</span>
            </div>
        </div>
            <div class="border-t border-black text-center pt-2">
                <p class="uppercase font-black text-[7px]">Firma Responsable</p>
                <p class="text-[6px] opacity-50">{{ shift.user?.name || 'Sistema' }}</p>
            </div>

        <div class="mt-8 text-center text-[6px] uppercase opacity-50">
            &nbsp;
        </div>
    </div>
</template>

<style scoped>
@media screen {
    .print-summary {
        display: none;
    }
}

@media print {
    body * {
        visibility: hidden !important;
    }
    .print-summary, .print-summary * {
        visibility: visible !important;
    }
    .print-summary {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        display: block !important;
        width: 100% !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .no-print {
        display: none !important;
    }
}
</style>
