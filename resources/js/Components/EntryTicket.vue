<script setup>
import QrcodeVue from 'qrcode.vue';

defineProps({
    ticket: Object
});
</script>

<template>
    <div v-if="ticket" class="ticket-container font-mono px-1 py-2 text-black bg-white leading-tight">
        <!-- Header -->
        <div class="flex flex-col items-center mb-1">
            <h1 class="text-sm font-black uppercase text-center">PARQUEADERO</h1>
            <div class="mt-2">
                <img src="/LogoGrande.png" alt="Logo" class="h-12 mx-auto object-contain" />
            </div>
        </div>

        <div class="text-center text-[9px] mb-3 leading-[0.9] space-y-0">
            <p class="font-bold">NIT: {{ $page.props.auth.user.tenant?.nit || '800.123.456-1' }}</p>
            <p>{{ $page.props.auth.user.tenant?.tax_regime || 'NO RESPONSABLE DE IVA' }}</p>
            <p>{{ $page.props.auth.user.tenant?.address || 'DIRECCIÓN POR CONFIGURAR' }}</p>
            <p>TELÉFONO: {{ $page.props.auth.user.tenant?.phone || '0000000' }}</p>
            <p>HORARIO: {{ $page.props.auth.user.tenant?.business_hours || '6:00 AM A 9:00 PM' }}</p>
        </div>

        <!-- Recibo y Placa -->
        <div class="text-center py-2 mb-2 border-t border-black">
            <p class="text-[9px] font-bold">Recibo No: {{ String(ticket.id).padStart(6, '0') }}</p>
            <h2 class="text-sm font-black tracking-tight uppercase mb-1">{{ $page.props.auth.user.tenant?.name ||
                'PARQUEADERO' }}</h2>
            <h2 class="text-2xl font-black tracking-widest uppercase">Placa: {{ ticket.vehicle?.plate }}</h2>
        </div>

        <!-- QR Code Antifraude (Lectura rápida en salida) -->
        <div class="flex justify-center mb-3">
            <div class="p-1 border-2 border-black rounded-lg bg-white">
                <qrcode-vue 
                    :value="'TKT-' + ticket.id" 
                    :size="80" 
                    level="H" 
                    render-as="svg"
                />
            </div>
        </div>

        <!-- Details Table -->
        <div class="text-[9px] border-b border-black mt-1 pt-1 pb-1 space-y-0.5">
            <div><span style="font-weight:bold; display:inline;">Tarifa:</span> <span class="uppercase">{{
                ticket.vehicle?.type }}</span></div>
            <div><span style="font-weight:bold; display:inline;">Entrada:</span> {{ new
                Date(ticket.entry_time).toLocaleString('es-CO', {
                    hour: '2-digit', minute: '2-digit', day: '2-digit',
                    month: '2-digit', year: 'numeric' }) }}</div>
            <div><span style="font-weight:bold; display:inline;">Cajero:</span> <span class="uppercase">{{
                ticket.user?.name || 'Sistema' }}</span></div>
            <div v-if="ticket.vehicle?.observation" style="font-size:8px; font-style:italic"><span
                    style="font-weight:bold; font-style:normal; display:inline;">Obs:</span> {{
                        ticket.vehicle.observation }}</div>
        </div>

        <!-- Footer -->
        <div class="text-center text-[9px] border-b border-black mt-1 pt-1 pb-1">
            <p class="font-bold uppercase tracking-tighter italic">{{ $page.props.auth.user.tenant?.welcome_message || '¡Gracias por su visita!' }}</p>
            <p class="leading-none opacity-90 italic">{{ $page.props.auth.user.tenant?.disclaimer_message || 'No nos responsabilizamos por objetos de valor dejados dentro del vehículo.' }}</p>
            <p class="font-black pt-2 tracking-widest uppercase">{{ $page.props.auth.user.tenant?.social_handle ||
                '@parkiapp' }}</p>
        </div>
    </div>
</template>

<style scoped>
.ticket-container {
    width: 100%;
    max-width: 260px;
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