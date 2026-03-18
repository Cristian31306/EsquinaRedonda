<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    memberships: Object,
});

const showModal = ref(false);
const today = new Date().toISOString().split('T')[0];

const form = useForm({
    plate: '',
    vehicle_type: 'moto',
    start_date: today,
    end_date: '',
    amount_paid: '',
    notes: '',
});

const vehicleTypes = ['moto', 'carro', 'pesado'];

const submit = () => {
    form.post(route('memberships.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            form.start_date = today;
            form.vehicle_type = 'moto';
        },
    });
};

const cancel = (id) => {
    if (confirm('¿Cancelar esta mensualidad? El vehículo perderá acceso libre.')) {
        router.delete(route('memberships.destroy', id));
    }
};

const isActive = (endDate) => {
    return new Date(endDate) >= new Date(today);
};
</script>

<template>
    <Head title="Mensualidades" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-black tracking-tight uppercase text-slate-900">Mensualidades</h2>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1">Gestión de Acceso Mensual & Cobros</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-6 py-3 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100"
                >
                    + Nueva Mensualidad
                </button>
            </div>
        </template>

        <div class="bg-white rounded-[2.5rem] border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest">Placa / Tipo</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest">Período</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-right">Valor Pagado</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest">Notas</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-center">Estado</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="m in memberships.data" :key="m.id" class="hover:bg-slate-50 transition-all group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-1.5 h-1.5 rounded-full transition-colors" :class="isActive(m.end_date) ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                                    <div>
                                        <p class="text-sm font-black text-slate-900 tracking-tight">{{ m.plate }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ m.vehicle_type }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-4">
                                <p class="text-xs font-bold text-slate-900">{{ new Date(m.start_date).toLocaleDateString() }}</p>
                                <p class="text-[9px] text-slate-400 font-bold mt-0.5">→ {{ new Date(m.end_date).toLocaleDateString() }}</p>
                            </td>
                            <td class="px-8 py-4 text-right">
                                <span class="text-xs font-black text-slate-900 tracking-tighter">${{ new Intl.NumberFormat().format(m.amount_paid) }}</span>
                            </td>
                            <td class="px-8 py-4">
                                <span class="text-[10px] text-slate-400">{{ m.notes || '—' }}</span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                <span
                                    v-if="isActive(m.end_date)"
                                    class="text-[8px] font-black text-emerald-600 bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-lg uppercase tracking-widest"
                                >Activa</span>
                                <span
                                    v-else
                                    class="text-[8px] font-black text-slate-400 bg-slate-50 border border-slate-100 px-3 py-1 rounded-lg uppercase tracking-widest"
                                >Vencida</span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                <button
                                    @click="cancel(m.id)"
                                    class="p-2 bg-slate-100 text-slate-400 rounded-xl hover:bg-rose-50 hover:text-rose-500 transition-all text-[9px] font-black uppercase tracking-widest"
                                    title="Cancelar mensualidad"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="memberships.data.length === 0">
                            <td colspan="6" class="px-8 py-20 text-center">
                                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-300">No hay mensualidades registradas</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div v-if="memberships.links && memberships.links.length > 3" class="px-8 py-6 border-t border-slate-100 bg-slate-50/30 flex justify-center gap-2">
                <a
                    v-for="link in memberships.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    :class="[link.active ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-400 hover:text-slate-900 border border-slate-200']"
                    class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                    v-html="link.label"
                />
            </div>
        </div>

        <!-- Modal Nueva Mensualidad -->
        <transition name="fade">
            <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-md" @click="showModal = false"></div>
                <div class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden">
                    <div class="p-10">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-1">Nueva Mensualidad</h3>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-8">Registrar acceso mensual y cobrar a caja</p>

                        <form @submit.prevent="submit" class="space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Placa</label>
                                    <input
                                        v-model="form.plate"
                                        type="text"
                                        class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-black text-slate-900 uppercase focus:ring-2 focus:ring-indigo-500/20"
                                        placeholder="ABC123"
                                        required
                                    />
                                    <p v-if="form.errors.plate" class="mt-1 text-[9px] font-bold text-rose-500 uppercase">{{ form.errors.plate }}</p>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Tipo</label>
                                    <select
                                        v-model="form.vehicle_type"
                                        class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-black text-slate-900 focus:ring-2 focus:ring-indigo-500/20"
                                    >
                                        <option v-for="t in vehicleTypes" :key="t" :value="t" class="capitalize">{{ t }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Inicio</label>
                                    <input
                                        v-model="form.start_date"
                                        type="date"
                                        class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-black text-slate-700 focus:ring-2 focus:ring-indigo-500/20"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Vencimiento</label>
                                    <input
                                        v-model="form.end_date"
                                        type="date"
                                        class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-black text-slate-700 focus:ring-2 focus:ring-indigo-500/20"
                                        required
                                    />
                                    <p v-if="form.errors.end_date" class="mt-1 text-[9px] font-bold text-rose-500 uppercase">{{ form.errors.end_date }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Valor a Cobrar ($)</label>
                                <input
                                    v-model="form.amount_paid"
                                    type="number"
                                    class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-black text-slate-900 focus:ring-2 focus:ring-indigo-500/20"
                                    placeholder="0"
                                    required
                                />
                                <p class="mt-1 text-[8px] text-slate-400 font-bold">Este monto se registra en el turno de caja activo.</p>
                            </div>

                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Notas (opcional)</label>
                                <input
                                    v-model="form.notes"
                                    type="text"
                                    class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500/20"
                                    placeholder="ej: Cliente habitual, piso 2..."
                                />
                            </div>

                            <div class="flex flex-col gap-3 pt-2">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all disabled:opacity-50"
                                >{{ form.processing ? 'Procesando...' : 'Registrar y Cobrar a Caja' }}</button>
                                <button type="button" @click="showModal = false" class="w-full py-4 bg-slate-50 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:text-slate-900 transition-all">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </transition>
    </AuthenticatedLayout>
</template>
