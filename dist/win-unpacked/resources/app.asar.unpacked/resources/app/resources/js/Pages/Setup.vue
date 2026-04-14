<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    apiUrl: String,
});

const form = useForm({
    token: '',
});

const isSyncing = ref(false);

const submit = () => {
    isSyncing.value = true;
    form.post(route('setup.store'), {
        onFinish: () => {
            isSyncing.value = false;
        },
    });
};
</script>

<template>
    <Head title="Configuración Inicial" />

    <div class="min-h-screen bg-slate-950 flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-slate-900/50 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-10 shadow-2xl">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-indigo-600 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-indigo-500/40 rotate-6 hover:rotate-0 transition-transform duration-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6.75h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-black text-white mb-2 tracking-tight">Bienvenido a ParkiApp</h1>
                <p class="text-slate-400 font-medium">Configuración de terminal de escritorio</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block text-xs font-black text-indigo-400 uppercase tracking-widest mb-2 ml-1">Token de Sincronización</label>
                    <input
                        v-model="form.token"
                        type="password"
                        placeholder="••••••••••••••••••••••••"
                        class="w-full bg-slate-800/50 border-white/5 rounded-2xl p-4 text-white placeholder-slate-600 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none"
                        :disabled="isSyncing"
                        required
                    />
                    <p v-if="form.errors.token" class="mt-2 text-sm text-rose-500 font-bold ml-1">{{ form.errors.token }}</p>
                </div>

                <div class="bg-indigo-500/5 border border-indigo-500/10 rounded-2xl p-4">
                    <div class="flex items-center gap-3 text-indigo-300 text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                        </svg>
                        Conectado a: <span class="font-mono">{{ apiUrl }}</span>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-black py-4 rounded-2xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95 flex items-center justify-center gap-3"
                    :disabled="isSyncing"
                >
                    <svg v-if="isSyncing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ isSyncing ? 'SINCRONIZANDO...' : 'VINCULAR TERMINAL' }}</span>
                </button>
                
                <p class="text-center text-[10px] text-slate-500 font-bold uppercase tracking-widest pt-4">
                    ParkiApp © 2026 - Control Maestro de Estacionamientos
                </p>
            </form>
        </div>
    </div>
</template>
