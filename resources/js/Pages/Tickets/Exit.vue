<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const plateSearch = ref('');
const results = ref([]);
const selectedTicket = ref(null);
const loading = ref(false);
const searchInput = ref(null);

onMounted(() => {
    searchInput.value?.focus();
});

const searchTicket = async () => {
    if (plateSearch.value.length < 3) {
        results.value = [];
        selectedTicket.value = null;
        return;
    }
    
    loading.value = true;
    try {
        const response = await axios.get(route('tickets.search', { plate: plateSearch.value }));
        results.value = response.data;
        
        // Auto-select if unique
        if (results.value.length === 1) {
            selectedTicket.value = results.value[0];
        } else {
            selectedTicket.value = null;
        }
    } catch (error) {
        console.error(error);
        results.value = [];
    } finally {
        loading.value = false;
    }
};

const payForm = useForm({
    amount: 0,
    method: 'efectivo',
});

const processPayment = () => {
    if (!selectedTicket.value) return;
    // Si tiene membresía activa, forzar $0
    const isMembership = selectedTicket.value.stay_type === 'membership' || selectedTicket.value.has_active_membership;
    payForm.amount = isMembership ? 0 : selectedTicket.value.total;
    if (isMembership) payForm.method = 'membership';
    payForm.post(route('tickets.pay', { ticket: selectedTicket.value.id }), {
        onSuccess: () => {
            selectedTicket.value = null;
            results.value = [];
            plateSearch.value = '';
            searchInput.value?.focus();
        },
    });
};

watch(plateSearch, (val) => {
    if (val.length >= 3) {
        searchTicket();
    } else {
        results.value = [];
        selectedTicket.value = null;
    }
});
</script>

<template>
    <Head title="Salida y Cobro" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center text-slate-900 px-2">
                <div>
                    <h2 class="text-xl font-black tracking-tight uppercase leading-none">Salida de Vehículos</h2>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1">Liquidación en Proceso</p>
                </div>
            </div>
        </template>

        <div class="h-full flex flex-col items-center justify-center p-6 bg-slate-100/50">
            <div class="bg-white border-2 border-slate-100 rounded-[3rem] p-8 max-w-5xl w-full shadow-[0_30px_60px_rgba(0,0,0,0.03)] animate-in slide-in-from-bottom-4 duration-500 overflow-hidden min-h-[400px] flex flex-col justify-center">
                
                <!-- Search & Results List -->
                <div v-if="!selectedTicket" class="flex flex-col lg:flex-row items-center gap-12 py-6">
                    <div class="flex-1 space-y-4">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-950 rounded-xl flex items-center justify-center border border-indigo-100 shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                        </div>
                        <h3 class="text-4xl font-black text-slate-900 tracking-tighter uppercase whitespace-nowrap">Liquidar <br/> Vehículo</h3>
                        
                        <!-- Collision List -->
                        <div v-if="results.length > 1" class="space-y-2 mt-4 max-h-[150px] overflow-y-auto no-scrollbar pr-2 pt-2">
                            <p class="text-[8px] font-black text-indigo-900 uppercase tracking-widest bg-indigo-50 p-2 rounded-lg mb-2">Múltiples coincidencias:</p>
                            <button 
                                v-for="t in results" 
                                :key="t.id"
                                @click="selectedTicket = t"
                                class="w-full flex items-center justify-between p-3 bg-white border border-slate-100 rounded-xl hover:border-indigo-950 hover:bg-indigo-50 transition-all group"
                            >
                                <span class="text-xs font-black text-slate-900 tracking-widest">{{ t.vehicle.plate }}</span>
                                <span class="text-[9px] font-bold text-slate-400 group-hover:text-indigo-950 uppercase">{{ t.vehicle.type }}</span>
                            </button>
                        </div>
                        <p v-else class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em]">Ingrese placa de salida</p>
                    </div>

                    <div class="flex-[2] relative w-full group">
                        <input 
                            ref="searchInput"
                            v-model="plateSearch" 
                            @input="plateSearch = plateSearch.toUpperCase()"
                            type="text" 
                            class="w-full text-center lg:text-left text-9xl font-black bg-transparent border-none text-indigo-950 focus:ring-0 placeholder:text-slate-100 uppercase tracking-tighter"
                            placeholder="PLACA"
                        />
                        <div v-if="loading" class="absolute right-4 top-1/2 -translate-y-1/2">
                            <div class="w-10 h-10 border-4 border-indigo-950 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                        <div class="h-2 w-full bg-indigo-950 rounded-full mt-2"></div>
                    </div>
                </div>

                <!-- Final Payment State -->
                <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center animate-in zoom-in-95 duration-300">
                    <!-- Left: Details -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-6">
                            <div class="px-5 py-2 bg-slate-900 text-white rounded-xl font-black text-5xl tracking-widest shadow-xl">{{ selectedTicket.vehicle.plate }}</div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-1">{{ selectedTicket.vehicle.type }}</h4>
                                <button @click="selectedTicket = null" class="text-[8px] font-black text-indigo-900 uppercase tracking-widest hover:underline flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-2.5 h-2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                                    Cambiar Vehículo
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex justify-between items-center">
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Ingreso</span>
                                <span class="text-[11px] font-bold text-slate-900 text-right">{{ new Date(selectedTicket.entry_time).toLocaleString('es-CO', {day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit'}) }}</span>
                            </div>
                            <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-2xl flex justify-between items-center">
                                <span class="text-[8px] font-black text-indigo-950/40 uppercase tracking-widest">Estadía</span>
                                <span class="text-sm font-black text-indigo-950 uppercase tracking-tighter">{{ selectedTicket.duration_text }}</span>
                            </div>
                            <div v-if="selectedTicket.vehicle.observation" class="p-4 bg-amber-50 border border-amber-100 rounded-xl flex gap-3 italic">
                                <p class="text-[8px] text-amber-800 uppercase leading-relaxed tracking-tight font-bold">Nota: {{ selectedTicket.vehicle.observation }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Operativo (Indigo 950) -->
                    <div class="bg-indigo-950 rounded-[2.5rem] p-8 text-white shadow-2xl flex flex-col justify-between h-full space-y-6">
                        <div>
                            <!-- Badge de Mensualidad -->
                            <div v-if="selectedTicket.stay_type === 'membership' || selectedTicket.has_active_membership" class="mb-4 space-y-2">
                                <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-2">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                    <p class="text-[9px] font-black text-emerald-700 uppercase tracking-widest">Mensualidad Activa — Salida Libre</p>
                                </div>
                                <!-- Alerta Vencimiento Próximo -->
                                <div v-if="selectedTicket.membership_info && selectedTicket.membership_info.days_left <= 7" 
                                    class="flex items-start gap-2 bg-amber-400 text-amber-950 rounded-xl px-4 py-3 shadow-lg shadow-amber-900/20 border border-white/20 animate-in zoom-in-95 duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 mt-0.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                                    <div>
                                        <p class="text-[9px] font-black uppercase tracking-tight">Vence en {{ selectedTicket.membership_info.days_left }} días</p>
                                        <p class="text-[7px] font-bold opacity-80 uppercase leading-none mt-1">Recuerde al cliente renovar antes del {{ selectedTicket.membership_info.end_date }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else-if="selectedTicket.stay_type === 'overnight'" class="mb-4 flex items-center gap-2 bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 text-indigo-600"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" /></svg>
                                <p class="text-[9px] font-black text-indigo-700 uppercase tracking-widest">Estadía Nocturna — Tarifa Fija</p>
                            </div>
                            <div v-else-if="selectedTicket.stay_type === 'fullday'" class="mb-4 flex items-center gap-2 bg-amber-50 border border-amber-100 rounded-xl px-4 py-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 text-amber-600"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m.386-6.364 1.591 1.591M18.75 12a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" /></svg>
                                <p class="text-[9px] font-black text-amber-700 uppercase tracking-widest">Día Completo — Tarifa Fija</p>
                            </div>
                            <p class="text-[8px] font-black uppercase tracking-[0.3em] opacity-60 mb-1">Monto de Liquidación</p>
                            <h4 v-if="selectedTicket.stay_type === 'membership' || selectedTicket.has_active_membership" class="text-6xl font-black tracking-tighter text-emerald-400">$0</h4>
                            <h4 v-else class="text-6xl font-black tracking-tighter">${{ new Intl.NumberFormat().format(selectedTicket.total) }}</h4>
                        </div>

                        <div class="space-y-3">
                            <div v-if="!(selectedTicket.stay_type === 'membership' || selectedTicket.has_active_membership)" class="grid grid-cols-3 gap-2">
                                <button 
                                    v-for="m in ['efectivo', 'nequi', 'tarjeta']" 
                                    :key="m"
                                    @click="payForm.method = m"
                                    :class="payForm.method === m ? 'bg-white text-indigo-950 font-black shadow-lg scale-105' : 'bg-black/20 text-white/50 border-white/5 hover:bg-black/40'"
                                    class="py-3 rounded-xl text-[8px] font-bold uppercase tracking-widest border transition-all"
                                >
                                    {{ m }}
                                </button>
                            </div>
                            <button 
                                @click="processPayment"
                                :disabled="payForm.processing"
                                class="w-full py-4 font-black uppercase text-[10px] rounded-2xl shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all tracking-[0.25em]"
                                :class="(selectedTicket.stay_type === 'membership' || selectedTicket.has_active_membership) ? 'bg-emerald-400 text-emerald-950' : 'bg-white text-indigo-950'"
                            >
                                {{ (selectedTicket.stay_type === 'membership' || selectedTicket.has_active_membership) ? 'Confirmar Salida Libre' : 'Confirmar Salida' }}
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
