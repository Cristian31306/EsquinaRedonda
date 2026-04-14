<script setup>
import QrcodeVue from 'qrcode.vue';

defineProps({
    ticket: Object
});
</script>

<template>
    <div v-if="ticket" class="ticket-container font-mono px-1 py-2 text-black bg-white leading-tight">
        <h1 class="text-sm font-black uppercase text-center mb-2 tracking-widest">PARQUEADERO</h1>
        <!-- Header Info -->
        <div class="text-center text-[9px] mb-3 leading-[0.9] space-y-0 uppercase">
            <p class="font-bold">NIT: {{ $page.props.settings?.business_nit || '800.123.456-1' }}</p>
            <p>{{ $page.props.settings?.is_iva_responsible === 'SI' ? 'RESPONSABLE DE IVA' : 'NO RESPONSABLE DE IVA' }}
            </p>
            <p>{{ $page.props.settings?.business_address || 'DIRECCIÓN POR CONFIGURAR' }}</p>
            <p>TEL: {{ $page.props.settings?.business_phone || '0000000' }}</p>
            <p>HORARIOS: {{ $page.props.settings?.business_schedule || 'Lunes a Sábado - 24 Horas' }}</p>
        </div>

        <!-- Recibo y Placa -->
        <div class="text-center py-2 mb-2 border-t border-black">
            <p class="text-[9px] font-bold">Recibo No: {{ String(ticket.id).padStart(6, '0') }}</p>
            <h2 class="text-sm font-black tracking-tight uppercase mb-1">
                {{ $page.props.settings?.business_name || 'PARQUEADERO' }}
            </h2>
            <h2 class="text-2xl font-black tracking-widest uppercase">Placa: {{ ticket.vehicle?.plate }}</h2>
        </div>

        <!-- QR Code Antifraude (Lectura rápida en salida) -->
        <div style="text-align: center; width: 100%;" class="mb-5">
            <qrcode-vue :value="'TKT-' + ticket.id" :size="120" level="H" render-as="svg"
                style="display: block; margin: 0 auto;" />
        </div>

        <!-- Details -->
        <div class="text-[9px] border-b border-black mt-1 pt-1 pb-1 leading-none">
            <div class="grid grid-cols-2 gap-0">
                <div class="font-bold">Tarifa:</div>
                <div class="text-right uppercase">{{ ticket.vehicle?.type }}</div>
                <div class="font-bold">Entrada:</div>
                <div class="text-right">{{ new Date(ticket.entry_time).toLocaleString('es-CO', {
                    hour: '2-digit',
                    minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric' }) }}</div>
                <div class="font-bold">Cajero:</div>
                <div class="text-right uppercase">{{ ticket.user?.name || 'Sistema' }}</div>
                <template v-if="ticket.vehicle?.observation">
                    <div class="font-bold italic" style="font-size: 8px;">Obs:</div>
                    <div class="text-right italic" style="font-size: 8px;">{{ ticket.vehicle.observation }}</div>
                </template>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-[9px] border-b border-black mt-1 pt-1 pb-1">
            <p class="font-bold uppercase tracking-tighter italic">
                {{ $page.props.settings?.ticket_footer || '¡Gracias por su visita!' }}
            </p>
            <p class="leading-none opacity-90 italic">No nos responsabilizamos por objetos de valor dejados dentro del
                vehículo.
            </p>
        </div>
    </div>
</template>

<style scoped>
.ticket-container {
    width: 100%;
    max-width: 280px;
    margin: 0 auto;
}

@media print {
    .ticket-container {
        width: 100% !important;
        margin: 0 !important;
        padding: 2px !important;
    }
}
</style>