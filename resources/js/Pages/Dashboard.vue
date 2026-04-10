<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';
import EntryTicket from '@/Components/EntryTicket.vue';

const ticketToPrint = ref(null);

const handleReprint = async (ticket) => {
    ticketToPrint.value = ticket;
    await nextTick();
    
    // Pequeña espera para asegurar carga de imágenes (especialmente .ico pesados)
    setTimeout(() => {
        window.print();
        setTimeout(() => { ticketToPrint.value = null; }, 1000);
    }, 500);
};

const props = defineProps({
    stats: Object,
    inventory: Array,
    alerts: Array,
});

const searchQuery = ref('');
const filteredInventory = ref([]);

import { computed } from 'vue';
const displayedInventory = computed(() => {
    if (!searchQuery.value) return props.inventory.slice(0, 50);
    return props.inventory.filter(t => 
        t.vehicle.plate.toLowerCase().includes(searchQuery.value.toLowerCase())
    ).slice(0, 50);
});
</script>

<template>

    <Head title="Panel de Control" />

    <AuthenticatedLayout>
        <!-- Área de Impresión (Oculta) -->
        <div class="print-container">
            <EntryTicket :ticket="ticketToPrint" />
        </div>

        <template #header>
            <div class="flex justify-between items-center text-slate-900">
                <div>
                    <h2 class="text-xl font-black tracking-tight uppercase">Dashboard Administrativo</h2>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1">Control General en
                        Tiempo Real</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none">Sistema
                        Sincronizado</span>
                </div>
            </div>
        </template>

        <!-- Zero-Scroll Dashboard Container -->
        <div class="h-full flex flex-col space-y-6 overflow-hidden">
            <!-- Stats Bar -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Stat 1: Removed for Anti-Fraud -->

                <!-- Stat 2 -->
                <div
                    class="bg-white border border-slate-200 p-6 rounded-3xl flex items-center justify-between shadow-sm hover:shadow-md transition-all">
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Vehículos en
                            Sitio</p>
                        <h4 class="text-3xl font-black text-emerald-700 tracking-tighter" v-if="stats">{{
                            stats.inventory_count
                            }} <span
                                class="text-sm text-emerald-300 font-bold uppercase tracking-widest ml-1">vhs</span>
                        </h4>
                    </div>
                    <div
                        class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 border border-emerald-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2a2 2 0 1 0 4 0h6a2 2 0 1 0 4 0" />
                        </svg>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div
                    class="bg-white border border-slate-200 p-6 rounded-3xl flex items-center justify-between shadow-sm hover:shadow-md transition-all">
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Alertas (>24h)
                        </p>
                        <h4 class="text-3xl font-black tracking-tighter"
                            :class="stats?.alerts_count > 0 ? 'text-rose-600' : 'text-slate-300'" v-if="stats">{{
                            stats.alerts_count }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center border transition-all"
                        :class="stats?.alerts_count > 0 ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-slate-50 text-slate-300 border-slate-100'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Main Tables Grid -->
            <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-8 min-h-0 pb-4">
                <!-- Left: Inventory -->
                <div class="bg-white rounded-3xl border border-slate-200 flex flex-col min-h-0 shadow-sm">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 bg-indigo-950 rounded-full"></div>
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Vehículos en Sitio
                            </h3>
                            <!-- Search Bar -->
                            <div class="relative ml-4">
                                <input 
                                    v-model="searchQuery"
                                    type="text" 
                                    placeholder="BUSCAR PLACA..." 
                                    class="pl-8 pr-4 py-1.5 bg-slate-50 border-none rounded-xl text-[9px] font-black uppercase tracking-widest focus:ring-2 focus:ring-indigo-950 w-40 placeholder:text-slate-300 transition-all"
                                />
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </div>
                        </div>
                        <Link :href="route('tickets.exit')"
                            class="text-[9px] font-black text-indigo-950 uppercase tracking-widest hover:underline">
                            Gestionar
                            Salidas →</Link>
                    </div>
                    <div class="flex-1 overflow-y-auto no-scrollbar">
                        <table class="w-full text-left">
                            <thead class="sticky top-0 bg-white border-b border-slate-100 z-10">
                                <tr>
                                    <th
                                        class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest">
                                        Placa
                                    </th>
                                    <th
                                        class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest">
                                        Categoría</th>
                                    <th
                                        class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-right">
                                        Ingreso</th>
                                    <th
                                        class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-right">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="ticket in displayedInventory" :key="ticket.id"
                                    class="hover:bg-slate-50 transition-colors group">
                                    <td class="px-8 py-4">
                                        <div
                                            class="inline-block px-3 py-1 bg-slate-900 text-white rounded-md font-black text-xs tracking-widest">
                                            {{ ticket.vehicle.plate }}</div>
                                    </td>
                                    <td class="px-8 py-4 capitalize text-xs font-bold text-slate-600 tracking-widest">{{
                                        ticket.vehicle.type }}</td>
                                    <td class="px-8 py-4 text-right">
                                        <p class="text-xs font-black text-slate-900">{{ new
                                            Date(ticket.entry_time).toLocaleTimeString([], {hour: '2-digit',
                                            minute:'2-digit'})
                                            }}</p>
                                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                                            {{ new
                                                Date(ticket.entry_time).toLocaleDateString() }}</p>
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <button @click="handleReprint(ticket)"
                                            class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.72 13.89 2.1 9.27a1.125 1.125 0 0 1 0-1.59l4.62-4.62a1.125 1.125 0 0 1 1.59 0l4.62 4.62a1.125 1.125 0 0 1 0 1.59l-4.62 4.62a1.125 1.125 0 0 1-1.59 0ZM17.25 12V3m0 0-3.75 3.75M17.25 3l3.75 3.75M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!inventory || inventory.length === 0">
                                    <td colspan="3" class="px-8 py-20 text-center text-slate-300">
                                        <p class="text-[9px] font-black uppercase tracking-[0.3em]">No hay vehículos
                                            registrados
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right: Alerts -->
                <div class="flex flex-col space-y-8 min-h-0">
                    <div
                        class="bg-white rounded-3xl border border-slate-200 p-6 flex flex-col flex-1 min-h-0 shadow-sm border-t-4 border-t-rose-500">
                        <div class="flex items-center gap-3 mb-6">
                            <h3 class="text-xs font-black text-rose-600 uppercase tracking-widest">Panel de Alertas
                                Críticas
                            </h3>
                        </div>

                        <div class="flex-1 overflow-y-auto no-scrollbar space-y-3">
                            <div v-for="alert in alerts" :key="alert.id"
                                class="flex items-center justify-between p-5 bg-rose-50 border border-rose-100 rounded-2xl group hover:bg-rose-100 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 bg-rose-600 text-white rounded-xl flex items-center justify-center font-black text-[10px] shadow-lg shadow-rose-200">
                                        {{ alert.vehicle.plate.substring(0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-lg font-black text-rose-900 tracking-widest leading-none">{{
                                            alert.vehicle.plate }}</p>
                                        <p
                                            class="text-[7px] font-bold text-rose-700 uppercase tracking-widest mt-1.5 text-rose-500/80">
                                            Estadía Prolongada (+24h)</p>
                                    </div>
                                </div>
                                <Link :href="route('tickets.exit', { plate: alert.vehicle.plate })"
                                    class="px-4 py-2 bg-rose-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-rose-700 transition-all shadow-md">
                                    Resolver</Link>
                            </div>

                            <div v-if="!alerts || alerts.length === 0"
                                class="flex flex-col items-center justify-center h-full text-center py-10">
                                <div
                                    class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor" class="w-10 h-10">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                                <p class="text-[9px] font-black uppercase tracking-[0.4em] text-emerald-600">Todo en
                                    orden</p>
                            </div>
                        </div>
                    </div>
                </div>
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
    body * {
        visibility: hidden !important;
    }

    .print-container,
    .print-container * {
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
