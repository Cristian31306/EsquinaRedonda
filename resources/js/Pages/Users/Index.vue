<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: Array,
});

const showModal = ref(false);
const isEditing = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'usuario',
});

const openModal = (user = null) => {
    isEditing.value = !!user;
    editingUser.value = user;
    form.reset();
    if (user) {
        form.name = user.name;
        form.email = user.email;
        form.role = user.role;
    }
    showModal.value = true;
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('users.update', editingUser.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const toggleStatus = (user) => {
    const action = user.is_active ? 'desactivar' : 'activar';
    if (confirm(`¿Está seguro de ${action} al usuario ${user.name}?`)) {
        router.patch(route('users.toggle-status', user.id));
    }
};

const getRoleLabel = (role) => {
    return role === 'admin' ? 'Administrador' : 'Usuario';
};
</script>

<template>
    <Head title="Gestión de Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center text-slate-900">
                <div>
                    <h2 class="text-xl font-black tracking-tight uppercase">Control de Usuarios</h2>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1">Gestión de Accesos y Permisos</p>
                </div>
                <button 
                    @click="openModal()"
                    class="px-8 py-3 bg-indigo-950 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg hover:bg-slate-900 active:scale-95 transition-all"
                >
                    + Nuevo Usuario
                </button>
            </div>
        </template>

        <div class="h-full flex flex-col min-h-0 overflow-hidden">
            <div class="flex-1 bg-white rounded-3xl border border-slate-200 overflow-hidden flex flex-col min-h-0 shadow-sm">
                <div class="overflow-y-auto no-scrollbar flex-1">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-white border-b border-slate-100 z-10">
                            <tr>
                                <th class="px-10 py-5 text-[9px] font-black uppercase text-slate-400 tracking-widest">Usuario</th>
                                <th class="px-6 py-5 text-[9px] font-black uppercase text-slate-400 tracking-widest text-center">Rol</th>
                                <th class="px-6 py-5 text-[9px] font-black uppercase text-slate-400 tracking-widest text-center">Estado</th>
                                <th class="px-6 py-5 text-[9px] font-black uppercase text-slate-400 tracking-widest text-right">Mantenimiento</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-indigo-50/30 transition-all group" :class="{'opacity-60': !user.is_active}">
                                <td class="px-10 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center transition-all shadow-inner" :class="user.is_active ? 'text-slate-400 group-hover:bg-indigo-950 group-hover:text-white' : 'text-slate-300'">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ user.name }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span 
                                        :class="user.role === 'admin' ? 'bg-indigo-100 text-indigo-950 border-indigo-200' : 'bg-slate-50 text-slate-500 border-slate-100'"
                                        class="px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest border"
                                    >
                                        {{ getRoleLabel(user.role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span 
                                        :class="user.is_active ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-rose-100 text-rose-700 border-rose-200'"
                                        class="px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest border"
                                    >
                                        {{ user.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-10 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openModal(user)" class="p-3 bg-indigo-50 text-indigo-950 rounded-xl hover:bg-indigo-950 hover:text-white transition-all shadow-sm" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                        </button>
                                        <button 
                                            v-if="$page.props.auth.user.id !== user.id"
                                            @click="toggleStatus(user)" 
                                            :class="user.is_active ? 'bg-rose-50 text-rose-600 hover:bg-rose-600' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600'"
                                            class="p-3 rounded-xl hover:text-white transition-all shadow-sm"
                                            :title="user.is_active ? 'Desactivar' : 'Activar'"
                                        >
                                            <svg v-if="user.is_active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
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
            <div class="bg-white rounded-[3rem] p-10 max-w-xl w-full shadow-2xl animate-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between mb-8 border-b border-slate-100 pb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-950 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}</h3>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Configure los datos de acceso para el personal</p>
                        </div>
                    </div>
                    <button @click="closeModal()" class="p-2 text-slate-300 hover:text-slate-900 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg></button>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="inline-block text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Nombre Completo</label>
                                <input v-model="form.name" type="text" class="w-full bg-slate-50 border-0 rounded-2xl px-5 py-4 text-sm font-black text-slate-900 focus:ring-2 focus:ring-indigo-500/20" required />
                            </div>
                            <div class="space-y-1">
                                <label class="inline-block text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Email / Usuario</label>
                                <input v-model="form.email" type="text" class="w-full bg-slate-50 border-0 rounded-2xl px-5 py-4 text-sm font-black text-slate-900 focus:ring-2 focus:ring-indigo-500/20" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="inline-block text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Contraseña {{ isEditing ? '(Opcional)' : '' }}</label>
                                <input v-model="form.password" type="password" class="w-full bg-slate-50 border-0 rounded-2xl px-5 py-4 text-sm font-black text-slate-900 focus:ring-2 focus:ring-indigo-500/20" :required="!isEditing" />
                            </div>
                            <div class="space-y-1">
                                <label class="inline-block text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Confirmar</label>
                                <input v-model="form.password_confirmation" type="password" class="w-full bg-slate-50 border-0 rounded-2xl px-5 py-4 text-sm font-black text-slate-900 focus:ring-2 focus:ring-indigo-500/20" :required="!isEditing" />
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="inline-block text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Rol del Sistema</label>
                            <select v-model="form.role" class="w-full bg-slate-50 border-0 rounded-2xl px-5 py-4 text-sm font-black text-slate-900 focus:ring-2 focus:ring-indigo-500/20">
                                <option value="usuario">Usuario (Acceso General)</option>
                                <option value="admin">Administrador (Acceso Total)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                        <button type="button" @click="closeModal()" class="px-8 py-3 text-slate-400 font-bold uppercase text-[10px] hover:text-slate-900 transition-all tracking-widest">Cancelar</button>
                        <button type="submit" :disabled="form.processing" class="px-10 py-3 bg-indigo-950 text-white font-black uppercase text-[10px] rounded-xl hover:bg-slate-900 transition-all tracking-widest shadow-lg shadow-indigo-100">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
