<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    Users, 
    UserPlus, 
    Key, 
    Trash2, 
    Shield, 
    Building2, 
    ArrowLeft,
    Mail,
    UserCircle,
    BadgeCheck,
    X
} from 'lucide-vue-next';

const props = defineProps({
    tenant: Object,
    users: Array,
});

const showingAddUserModal = ref(false);
const showingResetPasswordModal = ref(false);
const selectedUser = ref(null);

const addForm = useForm({
    name: '',
    email: '',
    password: '',
    role: 'user',
});

const passwordForm = useForm({
    password: '',
});

const submitAddUser = () => {
    addForm.post(route('admin.tenants.users.store', props.tenant.id), {
        onSuccess: () => {
            showingAddUserModal.value = false;
            addForm.reset();
        },
    });
};

const openResetModal = (user) => {
    selectedUser.value = user;
    showingResetPasswordModal.value = true;
};

const submitResetPassword = () => {
    passwordForm.post(route('admin.users.reset-password', selectedUser.value.id), {
        onSuccess: () => {
            showingResetPasswordModal.value = false;
            passwordForm.reset();
        },
    });
};

const deleteUser = (user) => {
    if (confirm(`¿Está seguro de eliminar al usuario ${user.name}? Esta acción no se puede deshacer.`)) {
        useForm({}).delete(route('admin.users.delete', user.id));
    }
};
</script>

<template>
    <Head :title="'Gestionar Usuarios - ' + tenant.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <Link :href="route('admin.tenants.index')" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        <ArrowLeft class="w-5 h-5 text-slate-600" />
                    </Link>
                    <div>
                        <h2 class="text-xl font-black tracking-tight uppercase text-slate-900">{{ tenant.name }}</h2>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1">Gestión de Personal del Cliente</p>
                    </div>
                </div>
                <button 
                    @click="showingAddUserModal = true"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-100"
                >
                    <UserPlus class="w-4 h-4" /> Nuevo Empleado
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Users List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-200">
                    <div class="p-8">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-1.5 h-6 bg-indigo-600 rounded-full"></div>
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Equipo de trabajo</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="user in users" :key="user.id" class="p-6 bg-slate-50 border border-slate-100 rounded-3xl group hover:border-indigo-200 hover:bg-white hover:shadow-xl transition-all duration-300">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 shadow-sm transition-colors">
                                        <UserCircle class="w-6 h-6" />
                                    </div>
                                    <div :class="user.role === 'admin' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-slate-100 text-slate-500 border-slate-200'" class="px-3 py-1 border rounded-lg text-[8px] font-black uppercase tracking-widest">
                                        {{ user.role === 'admin' ? 'Administrador' : 'Operativo' }}
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ user.name }}</h4>
                                    <p class="text-xs font-medium text-slate-500 truncate mt-1 flex items-center gap-1.2">
                                        <Mail class="w-3 h-3 opacity-40" /> {{ user.email }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 pt-4 border-t border-slate-200/50">
                                    <button 
                                        @click="openResetModal(user)"
                                        class="flex-1 flex items-center justify-center gap-2 py-2 bg-white border border-slate-200 text-slate-600 hover:border-amber-200 hover:text-amber-600 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all"
                                    >
                                        <Key class="w-3 h-3" /> Clave
                                    </button>
                                    <button 
                                        @click="deleteUser(user)"
                                        class="p-2 bg-white border border-slate-200 text-slate-400 hover:border-rose-200 hover:text-rose-600 rounded-xl transition-all"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="users.length === 0" class="py-20 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                <Users class="w-8 h-8 text-slate-300" />
                            </div>
                            <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest">No hay usuarios registrados aun</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <div v-if="showingAddUserModal" class="fixed inset-0 z-[100] flex items-center justify-center px-4 overflow-hidden">
            <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-sm" @click="showingAddUserModal = false"></div>
            <div class="bg-white rounded-[2.5rem] w-full max-w-lg relative shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
                <div class="p-10">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 uppercase">Nuevo Empleado</h3>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Registrar personal para {{ tenant.name }}</p>
                        </div>
                        <button @click="showingAddUserModal = false" class="p-2 hover:bg-slate-100 rounded-xl transition-colors">
                            <X class="w-5 h-5 text-slate-400" />
                        </button>
                    </div>

                    <form @submit.prevent="submitAddUser" class="space-y-6">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Nombre Completo</label>
                            <input v-model="addForm.name" type="text" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all border-2" placeholder="Ej: Juan Pérez">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Correo Electrónico</label>
                            <input v-model="addForm.email" type="text" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all border-2" placeholder="usuario o email@empresa.com">
                            <p class="px-2 text-[8px] font-bold text-indigo-400 uppercase tracking-widest leading-loose">
                                Tip: Solo escribe el nombre (ej: juan). Se guardará como juan@{{ tenant.slug.replace(/-/g, '') }}.com
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Contraseña Inicial</label>
                                <input v-model="addForm.password" type="password" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all border-2" placeholder="*******">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Rol de Usuario</label>
                                <select v-model="addForm.role" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all border-2 appearance-none">
                                    <option value="user">Operativo</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        </div>

                        <button 
                            type="submit"
                            :disabled="addForm.processing"
                            class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl shadow-indigo-100 mt-4"
                        >
                            {{ addForm.processing ? 'Registrando...' : 'Crear Usuario Ahora' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reset Password Modal -->
        <div v-if="showingResetPasswordModal" class="fixed inset-0 z-[100] flex items-center justify-center px-4 overflow-hidden">
            <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-sm" @click="showingResetPasswordModal = false"></div>
            <div class="bg-white rounded-[2.5rem] w-full max-w-sm relative shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
                <div class="p-10 text-center">
                    <div class="w-16 h-16 bg-amber-50 rounded-3xl flex items-center justify-center text-amber-500 mx-auto mb-6">
                        <Key class="w-8 h-8" />
                    </div>
                    
                    <h3 class="text-lg font-black text-slate-900 uppercase">Nueva Clave</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 mb-8">Para: {{ selectedUser?.name }}</p>

                    <form @submit.prevent="submitResetPassword" class="space-y-6">
                        <div class="space-y-1.5 text-left">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Contraseña Nueva</label>
                            <input v-model="passwordForm.password" type="password" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all border-2 text-center" placeholder="*******">
                        </div>

                        <div class="flex gap-3">
                            <button 
                                type="button"
                                @click="showingResetPasswordModal = false"
                                class="flex-1 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                :disabled="passwordForm.processing"
                                class="flex-1 py-4 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl shadow-amber-100"
                            >
                                Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
