<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar Sesión" />

        <div class="mb-8">
            <h2 class="text-xl font-black text-slate-900 uppercase tracking-tighter">Control de Acceso</h2>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Ingrese sus credenciales operativas</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Usuario</label>
                <input 
                    id="email"
                    type="text"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900 focus:border-indigo-950 focus:ring-0 transition-all placeholder:text-slate-300"
                    placeholder="admin"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Contraseña</label>
                <input 
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900 focus:border-indigo-950 focus:ring-0 transition-all placeholder:text-slate-300"
                    placeholder="••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="pt-4">
                <button
                    class="w-full bg-indigo-950 text-white font-black uppercase tracking-[0.2em] text-[11px] py-5 rounded-2xl shadow-xl shadow-indigo-100 hover:bg-slate-900 transition-all disabled:opacity-50"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Autenticando...</span>
                    <span v-else>Entrar al Sistema</span>
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
