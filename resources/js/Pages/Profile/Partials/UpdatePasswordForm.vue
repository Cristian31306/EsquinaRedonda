<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight">
                Actualizar Contraseña
            </h2>

            <p class="mt-1 text-sm text-slate-500 uppercase font-bold tracking-widest text-[10px]">
                Asegúrese de que su cuenta esté utilizando una contraseña larga y aleatoria para mantenerse segura.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_password" value="Contraseña Actual" class="uppercase text-[10px] font-black tracking-widest text-slate-400" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    toggleable
                    class="mt-1 block w-full bg-slate-50 border-0 rounded-2xl px-5 py-4 text-sm font-black text-slate-900 focus:ring-2 focus:ring-indigo-500/20"
                    autocomplete="current-password"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div>
                <InputLabel for="password" value="Nueva Contraseña" class="uppercase text-[10px] font-black tracking-widest text-slate-400" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    toggleable
                    class="mt-1 block w-full bg-slate-50 border-0 rounded-2xl px-5 py-4 text-sm font-black text-slate-900 focus:ring-2 focus:ring-indigo-500/20"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Contraseña"
                    class="uppercase text-[10px] font-black tracking-widest text-slate-400"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    toggleable
                    class="mt-1 block w-full bg-slate-50 border-0 rounded-2xl px-5 py-4 text-sm font-black text-slate-900 focus:ring-2 focus:ring-indigo-500/20"
                    autocomplete="new-password"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing" class="px-10 py-3 bg-indigo-950 text-white font-black uppercase text-[10px] rounded-xl hover:bg-slate-900 transition-all tracking-widest shadow-lg shadow-indigo-100 border-0">Guardar Cambios</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-emerald-600 font-bold uppercase tracking-widest text-[10px]"
                    >
                        Guardado.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
