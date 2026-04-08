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

        <!-- Mensaje de Error (Suspensión o Fallos) -->
        <div v-if="$page.props.flash.error" class="mb-8 bg-rose-50 border-2 border-rose-100 rounded-[2rem] p-6 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-rose-600 shadow-sm shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-1">Acceso Restringido</h3>
                    <p class="text-xs font-bold text-rose-900/70 leading-relaxed">
                        {{ $page.props.flash.error }}
                    </p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6" autocomplete="off">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Usuario</label>
                <input 
                    id="email"
                    type="text"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="off"
                    name="user_email_field"
                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900 focus:border-indigo-950 focus:ring-0 transition-all placeholder:text-slate-300"
                    placeholder="admin"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Contraseña</label>
                <TextInput 
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    toggleable
                    autocomplete="new-password"
                    name="user_pass_field"
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
