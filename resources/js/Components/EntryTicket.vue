<script setup>
defineProps({
    ticket: Object
});
</script>

<template>
    <div v-if="ticket" class="ticket-container font-mono p-2 text-black bg-white leading-tight">
        <div class="text-center border-b border-black pb-1 mb-1">
            <p class="font-bold">PARQUEADERO</p>
            <h1 class="text-lg font-bold uppercase">Esquina Redonda</h1>
            <p class="text-[8px] italic">¡Bienvenido!</p>
        </div>

        <div class="text-center py-1 mb-1">
            <span class="text-[8px] uppercase tracking-widest block">Placa Vehículo</span>
            <h2 class="text-4xl font-black leading-none">{{ ticket.vehicle?.plate }}</h2>
            <p class="text-[9px] font-bold mt-1 uppercase opacity-80">{{ String(ticket.id).padStart(6, '0') }}
            </p>
        </div>

        <div class="text-[10px] text-center space-y-0.5 border-y border-dotted border-black py-1">
            <p><span class="font-bold">INGRESO:</span> {{ new Date(ticket.entry_time).toLocaleDateString() }} {{ new
                Date(ticket.entry_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</p>
            <p><span class="font-bold uppercase">{{ ticket.vehicle?.type }}</span></p>
        </div>

        <div class="text-center text-[8px] mt-2 px-2">
            <p class="font-bold uppercase tracking-tighter">Conserve este ticket</p>
            <p class="text-[7px] leading-none opacity-90">No nos responsabilizamos por objetos de valor dejados dentro
                del vehículo.</p>
        </div>
    </div>
</template>

<style scoped>
.ticket-container {
    width: 100%;
    max-width: 260px;
    /* Más estrecho para asegurar que quepa en 58mm y 80mm */
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