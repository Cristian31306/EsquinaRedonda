<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-50 p-6">
        <div class="mb-8 flex flex-col items-center">
            <div class="w-16 h-16 bg-indigo-950 rounded-3xl flex items-center justify-center text-white shadow-xl shadow-indigo-100 mb-4 animate-in zoom-in-50 duration-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">Esquina Redonda</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1">Gestión de Parqueadero</p>
        </div>

        <div class="w-full max-w-md bg-white border border-slate-200 p-10 rounded-[2.5rem] shadow-[0_40px_80px_rgba(0,0,0,0.04)] animate-in slide-in-from-bottom-4 duration-700">
            <slot />
        </div>
        
        <p class="mt-8 text-[9px] font-bold text-slate-400 uppercase tracking-widest">&copy; 2024 Esquina Redonda Premium</p>
    </div>
</template>
