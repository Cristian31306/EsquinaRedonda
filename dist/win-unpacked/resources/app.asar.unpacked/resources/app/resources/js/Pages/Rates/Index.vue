<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    rates: Object,
});

const ALL_CONCEPTS = ['fraccion', 'hora', 'dia', 'noche', 'mensualidad'];
const conceptLabels = {
    'fraccion': 'Frac.',
    'hora': 'Hora',
    'dia': 'Día',
    'noche': 'Noche',
    'mensualidad': 'Mes',
};

const form = useForm({
    vehicle_type: '',
    rates: { fraccion: 0, hora: 0, dia: 0, noche: 0, mensualidad: 0 }
});

const showModal = ref(false);
const isEditing = ref(false);

const openModal = (vType = null) => {
    isEditing.value = !!vType;
    if (vType) {
        form.vehicle_type = vType;
        ALL_CONCEPTS.forEach(concept => {
            const existing = props.rates[vType].find(r => r.concept === concept);
            form.rates[concept] = existing ? existing.value : 0;
        });
    } else {
        form.reset();
    }
    showModal.value = true;
};

const submit = () => {
    form.post(route('rates.store_bulk'), {
        onSuccess: () => { showModal.value = false; form.reset(); },
    });
};

const deleteCategory = (vType) => {
    if (confirm(`¿Eliminar la categoría de tarifas para ${vType}?`)) {
        router.delete(route('rates.destroy_category', { vehicle_type: vType }));
    }
};

const getRateValue = (vType, concept) => {
    const rate = props.rates[vType].find(r => r.concept === concept);
    return rate ? rate.value : 0;
};
</script>

<template>
    <Head title="Gestión de Tarifas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center text-slate-900">
                <div>
                    <h2 class="text-xl font-black tracking-tight uppercase">Configuración de Tarifas</h2>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1">Precios por Categoría de Vehículo</p>
                </div>
                <button 
                    @click="openModal()"
                    class="px-8 py-3 bg-indigo-950 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg hover:bg-slate-900 active:scale-95 transition-all"
                >
                    + Crear Nueva Categoría
                </button>
            </div>
        </template>

        <div class="h-full flex flex-col min-h-0 overflow-hidden">
            <div class="flex-1 bg-white rounded-3xl border border-slate-200 overflow-hidden flex flex-col min-h-0 shadow-sm">
                <div class="overflow-y-auto no-scrollbar flex-1">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-white border-b border-slate-100 z-10">
                            <tr>
                                <th class="px-10 py-5 text-[9px] font-black uppercase text-slate-400 tracking-widest">Categoría</th>
                                <th class="px-6 py-5 text-[9px] font-black uppercase text-slate-400 tracking-widest text-center">Conceptos Configurables</th>
                                <th class="px-6 py-5 text-[9px] font-black uppercase text-slate-400 tracking-widest text-center">Referencia (Hora)</th>
                                <th class="px-10 py-5 text-[9px] font-black uppercase text-slate-400 tracking-widest text-right">Mantenimiento</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="(group, vType) in rates" :key="vType" class="hover:bg-indigo-50/30 transition-all group">
                                <td class="px-10 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-indigo-950 group-hover:text-white transition-all shadow-inner">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                                        </div>
                                        <span class="text-lg font-black text-slate-900 tracking-widest uppercase">{{ vType }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-center gap-1.5">
                                        <div 
                                            v-for="concept in ALL_CONCEPTS" 
                                            :key="concept"
                                            :class="getRateValue(vType, concept) > 0 ? 'bg-indigo-100/50 text-indigo-950 border-indigo-200' : 'bg-slate-50 text-slate-300 border-slate-100'"
                                            class="px-2 py-1 rounded-lg text-[7px] font-black uppercase tracking-tighter border transition-all"
                                        >
                                            {{ conceptLabels[concept] }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="text-lg font-black text-indigo-950 tracking-tighter">${{ new Intl.NumberFormat().format(getRateValue(vType, 'hora')) }}</span>
                                </td>
                                <td class="px-10 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openModal(vType)" class="p-3 bg-indigo-50 text-indigo-950 rounded-xl hover:bg-indigo-950 hover:text-white transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                        </button>
                                        <button @click="deleteCategory(vType)" class="p-3 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Management Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
            <div class="bg-white rounded-[3rem] p-10 max-w-4xl w-full shadow-2xl animate-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between mb-8 border-b border-slate-100 pb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-950 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ isEditing ? 'Editar Categoría' : 'Nueva Categoría' }}</h3>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ isEditing ? form.vehicle_type : 'Ingrese el nombre y defina los valores' }}</p>
                        </div>
                    </div>
                    <button @click="showModal = false" class="p-2 text-slate-300 hover:text-slate-900 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg></button>
                </div>

                <form @submit.prevent="submit" class="space-y-10">
                    <div class="grid grid-cols-5 gap-6">
                        <div v-if="!isEditing" class="col-span-1 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">ID Único</label>
                            <input v-model="form.vehicle_type" type="text" class="w-full bg-transparent border-none p-0 text-sm font-black text-slate-900 focus:ring-0 uppercase" placeholder="EJ: PESADOS" required />
                        </div>
                        <div v-for="concept in ALL_CONCEPTS" :key="concept" class="bg-slate-50 p-5 rounded-2xl border border-slate-200 group hover:border-indigo-500 transition-all shadow-inner">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">{{ conceptLabels[concept] }}</span>
                                <div class="w-4 h-4 text-indigo-950 transition-transform group-hover:scale-110">
                                    <svg v-if="concept === 'fraccion'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    <svg v-if="concept === 'hora'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    <svg v-if="concept === 'dia'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m.386-6.364 1.591 1.591M18.75 12a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" /></svg>
                                    <svg v-if="concept === 'noche'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" /></svg>
                                    <svg v-if="concept === 'mensualidad'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 group-focus-within:text-indigo-950">
                                <span class="text-xs font-black text-slate-300">$</span>
                                <input v-model="form.rates[concept]" type="number" class="w-full bg-transparent border-none p-0 text-xl font-black text-slate-900 focus:ring-0 tracking-tighter" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-8 py-3 text-slate-400 font-bold uppercase text-[10px] hover:text-slate-900 transition-all tracking-widest">Cancelar</button>
                        <button type="submit" :disabled="form.processing" class="px-10 py-3 bg-indigo-950 text-white font-black uppercase text-[10px] rounded-xl hover:bg-slate-900 transition-all tracking-widest shadow-lg shadow-indigo-100 disabled:opacity-50 disabled:cursor-wait flex items-center justify-center gap-2">
                            <template v-if="form.processing">
                                <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Guardando...</span>
                            </template>
                            <span v-else>Guardar Cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
