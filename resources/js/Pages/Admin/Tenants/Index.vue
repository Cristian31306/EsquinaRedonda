<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    ShieldCheck, 
    ShieldAlert, 
    Users, 
    Building2, 
    Power, 
    PowerOff, 
    Plus, 
    Pencil,
    X,
    Calendar,
    Clock
} from 'lucide-vue-next';

const props = defineProps({
    tenants: Array,
});

const showingAddModal = ref(false);
const showingEditModal = ref(false);
const selectedTenant = ref(null);

const addForm = useForm({
    name: '',
    plan: 'basico',
    billing_cycle: 'mensual',
    expires_at: '',
    status: 'active',
});

const editForm = useForm({
    name: '',
    plan: '',
    billing_cycle: '',
    expires_at: '',
});

const openEditModal = (tenant) => {
    selectedTenant.value = tenant;
    editForm.name = tenant.name;
    editForm.plan = tenant.plan;
    editForm.billing_cycle = tenant.billing_cycle || 'mensual';
    editForm.expires_at = tenant.expires_at || '';
    showingEditModal.value = true;
};

const formatDate = (dateString) => {
    if (!dateString) return 'Sin fecha';
    return new Date(dateString).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const submitAdd = () => {
    addForm.post(route('admin.tenants.store'), {
        onSuccess: () => {
            showingAddModal.value = false;
            addForm.reset();
        },
    });
};

const submitEdit = () => {
    editForm.patch(route('admin.tenants.update', selectedTenant.value.id), {
        onSuccess: () => {
            showingEditModal.value = false;
        },
    });
};

const toggleStatus = (tenant) => {
    if (confirm(`¿Está seguro de cambiar el estado de ${tenant.name}?`)) {
        router.patch(route('admin.tenants.toggle', tenant.id));
    }
};
</script>

<template>
    <Head title="Gestión de Empresas - Algorah" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center text-slate-900">
                <div>
                    <h2 class="text-xl font-black tracking-tight uppercase">Control Maestro SaaS</h2>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1">Gestión Global de Clientes Algorah</p>
                </div>
                <div class="flex items-center gap-6">
                    <button 
                        @click="showingAddModal = true"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-sky-100"
                    >
                        <Plus class="w-4 h-4" /> Nueva Empresa
                    </button>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none">Super Admin</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-200">
                    <div class="p-8">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-1.5 h-6 bg-sky-600 rounded-full"></div>
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Empresas Registradas</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100">
                                        <th class="px-6 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-left">Empresa</th>
                                        <th class="px-6 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-left">Estado</th>
                                        <th class="px-6 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-left">Suscripción</th>
                                        <th class="px-6 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-left">Vencimiento</th>
                                        <th class="px-6 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-center">Usuarios</th>
                                        <th class="px-6 py-4 text-[9px] font-black uppercase text-slate-400 tracking-widest text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <tr v-for="tenant in tenants" :key="tenant.id" class="hover:bg-slate-50 transition-colors group">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-sky-50 group-hover:text-sky-600 transition-colors">
                                                    <Building2 class="w-5 h-5" />
                                                </div>
                                                <div>
                                                    <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ tenant.name }}</p>
                                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ tenant.slug }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span v-if="tenant.status === 'active'" class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100">
                                                <ShieldCheck class="w-3 h-3" /> Activo
                                            </span>
                                            <span v-else class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-rose-100">
                                                <ShieldAlert class="w-3 h-3" /> Suspendido
                                            </span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col gap-1">
                                                <span 
                                                    :class="tenant.plan === 'pro' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-slate-50 text-slate-500 border-slate-100'"
                                                    class="inline-flex items-center justify-center px-3 py-1 border rounded-lg text-[9px] font-black uppercase tracking-widest w-fit"
                                                >
                                                    {{ tenant.plan === 'basico' ? 'Básico' : 'Pro' }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 text-[8px] font-bold text-slate-400 uppercase tracking-widest ml-1">
                                                    <Clock class="w-2.5 h-2.5" /> {{ tenant.billing_cycle === 'anual' ? 'Anual' : 'Mensual' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-2">
                                                <Calendar class="w-3.5 h-3.5 text-slate-300" />
                                                <span class="text-[10px] font-black text-slate-700 uppercase tracking-tight">
                                                    {{ formatDate(tenant.expires_at) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <Users class="w-4 h-4 text-slate-300" />
                                                <span class="text-sm font-black text-slate-700">{{ tenant.users_count }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-right space-x-2">
                                            <Link 
                                                :href="route('admin.tenants.users', tenant.id)"
                                                class="inline-flex items-center gap-2 px-3 py-2 border border-slate-100 bg-white text-slate-600 hover:border-indigo-200 hover:text-indigo-600 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-sm"
                                            >
                                                <Users class="w-3.5 h-3.5" /> Equipo
                                            </Link>
                                            <button 
                                                @click="openEditModal(tenant)"
                                                class="inline-flex items-center gap-2 px-3 py-2 border border-slate-100 bg-white text-slate-600 hover:border-sky-200 hover:text-sky-600 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-sm"
                                            >
                                                <Pencil class="w-3.5 h-3.5" /> Editar
                                            </button>
                                            <button 
                                                @click="toggleStatus(tenant)"
                                                :class="tenant.status === 'active' ? 'bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border-rose-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border-emerald-100'"
                                                class="inline-flex items-center gap-2 px-3 py-2 border rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-sm whitespace-nowrap"
                                            >
                                                <template v-if="tenant.status === 'active'">
                                                    <PowerOff class="w-3.5 h-3.5" /> Suspender
                                                </template>
                                                <template v-else>
                                                    <Power class="w-3.5 h-3.5" /> Activar
                                                </template>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modals -->
        <div v-if="showingAddModal" class="fixed inset-0 z-[100] flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-sm" @click="showingAddModal = false"></div>
            <div class="bg-white rounded-[2.5rem] w-full max-w-lg relative shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
                <div class="p-10">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 uppercase">Nueva Empresa</h3>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Registrar un nuevo cliente en Algorah</p>
                        </div>
                        <button @click="showingAddModal = false" class="p-2 hover:bg-slate-100 rounded-xl transition-colors">
                            <X class="w-5 h-5 text-slate-400" />
                        </button>
                    </div>

                    <form @submit.prevent="submitAdd" class="space-y-6">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Nombre Comercial</label>
                            <input v-model="addForm.name" type="text" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all border-2" placeholder="Ej: Parqueadero La 14">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Plan</label>
                                <select v-model="addForm.plan" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all border-2 appearance-none">
                                    <option value="basico">Básico</option>
                                    <option value="pro">Pro</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Ciclo</label>
                                <select v-model="addForm.billing_cycle" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all border-2 appearance-none">
                                    <option value="mensual">Mensual</option>
                                    <option value="anual">Anual</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Vencimiento</label>
                                <input v-model="addForm.expires_at" type="date" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all border-2">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Estado</label>
                                <select v-model="addForm.status" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all border-2 appearance-none">
                                    <option value="active">Activo</option>
                                    <option value="suspended">Suspendido</option>
                                </select>
                            </div>
                        </div>

                        <button 
                            type="submit"
                            :disabled="addForm.processing"
                            class="w-full py-4 bg-sky-600 hover:bg-sky-700 disabled:opacity-50 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl shadow-sky-100 mt-4"
                        >
                            {{ addForm.processing ? 'Guardando...' : 'Crear Empresa Ahora' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showingEditModal" class="fixed inset-0 z-[100] flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-sm" @click="showingEditModal = false"></div>
            <div class="bg-white rounded-[2.5rem] w-full max-w-sm relative shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
                <div class="p-10 text-center">
                    <div class="w-16 h-16 bg-sky-50 rounded-3xl flex items-center justify-center text-sky-500 mx-auto mb-6">
                        <Pencil class="w-8 h-8" />
                    </div>
                    
                    <h3 class="text-lg font-black text-slate-900 uppercase">Editar Datos</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 mb-8">Actualización de identidad</p>

                    <form @submit.prevent="submitEdit" class="space-y-6">
                        <div class="space-y-1.5 text-left">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Nombre Comercial</label>
                            <input v-model="editForm.name" type="text" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all border-2" placeholder="Nombre de la empresa">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5 text-left">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Plan</label>
                                <select v-model="editForm.plan" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all border-2 appearance-none">
                                    <option value="basico">Básico</option>
                                    <option value="pro">Pro</option>
                                </select>
                            </div>
                            <div class="space-y-1.5 text-left">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Ciclo</label>
                                <select v-model="editForm.billing_cycle" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all border-2 appearance-none">
                                    <option value="mensual">Mensual</option>
                                    <option value="anual">Anual</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1.5 text-left">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Fecha de Vencimiento</label>
                            <input v-model="editForm.expires_at" type="date" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all border-2">
                        </div>

                        <div class="flex gap-3 mt-4">
                            <button 
                                type="button"
                                @click="showingEditModal = false"
                                class="flex-1 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                :disabled="editForm.processing"
                                class="flex-1 py-4 bg-sky-600 hover:bg-sky-700 disabled:opacity-50 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl shadow-sky-100"
                            >
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
