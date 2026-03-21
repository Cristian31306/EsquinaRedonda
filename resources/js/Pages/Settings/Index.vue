<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object
});

const form = useForm({
    telegram_bot_token: props.settings.telegram_bot_token || '',
    telegram_chat_ids: props.settings.telegram_chat_ids || '',
});

const submit = () => {
    form.post(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => alert('Configuración guardada correctamente'),
    });
};
</script>

<template>
    <Head title="Ajustes del Sistema" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-widest">
                ⚙️ Ajustes del Sistema
            </h2>
        </template>

        <div class="max-w-4xl mx-auto space-y-8">
            <!-- Telegram Integration Card -->
            <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100 transition-all duration-300 hover:shadow-2xl">
                <div class="p-10">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-widest">Respaldo Telegram</h3>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Configuración del Bot y Notificaciones</p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Telegram Bot Token</label>
                                <input 
                                    v-model="form.telegram_bot_token"
                                    type="text" 
                                    placeholder="8630547662:AAGC..."
                                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all shadow-inner"
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Chat IDs (Separa por comas)</label>
                                <input 
                                    v-model="form.telegram_chat_ids"
                                    type="text" 
                                    placeholder="12345678, 87654321"
                                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all shadow-inner"
                                />
                            </div>
                        </div>

                        <div class="bg-indigo-50/50 rounded-3xl p-6 border border-indigo-100">
                            <div class="flex gap-4">
                                <span class="text-xl">ℹ️</span>
                                <div class="text-xs font-bold text-indigo-900/70 leading-relaxed uppercase tracking-widest">
                                    Los respaldos de la base de datos y los reportes de cierre de turno se enviarán automáticamente a estos destinos.
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="bg-indigo-950 text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-900 active:scale-95 transition-all disabled:opacity-50"
                            >
                                Guardar Ajustes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
