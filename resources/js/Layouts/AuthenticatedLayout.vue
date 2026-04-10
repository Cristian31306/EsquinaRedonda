<script setup>
import { ref, watch, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const showingNavigationDropdown = ref(false);

// Lógica de Sincronización
const lastSync = ref(localStorage.getItem('last_sync_at') || 'Sin datos');
const syncStatus = ref('idle'); // idle, syncing, success, error

const runManualSync = async () => {
    if (syncStatus.value === 'syncing') return;
    
    syncStatus.value = 'syncing';
    try {
        const response = await axios.post('/api/v1/sync/now', {}, {
            headers: {
                'Authorization': `Bearer ${page.props.auth.user?.tenant?.api_token}`
            }
        });
        
        if (response.data.success) {
            lastSync.value = response.data.synced_at;
            localStorage.setItem('last_sync_at', lastSync.value);
            syncStatus.value = 'success';
        } else {
            syncStatus.value = 'error';
        }
    } catch (error) {
        console.error('Sync failed:', error);
        syncStatus.value = 'error';
    } finally {
        setTimeout(() => syncStatus.value = 'idle', 3000);
    }
};

onMounted(() => {
    if (page.props.flash?.success?.includes('Turno cerrado')) {
        lastSync.value = new Date().toLocaleDateString() + ' ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        localStorage.setItem('last_sync_at', lastSync.value);
    }
});

watch(() => page.props.flash, (newFlash) => {
    if (newFlash && (newFlash.success || newFlash.error)) {
        setTimeout(() => {
            if (page.props.flash) {
                page.props.flash.success = null;
                page.props.flash.error = null;
            }
        }, 5000);
    }
}, { deep: true, immediate: true });
</script>

<template>
    <div class="h-screen w-full bg-slate-50 flex overflow-hidden font-sans selection:bg-indigo-100 selection:text-indigo-900 antialiased">
        <!-- Sidebar -->
        <aside class="w-20 lg:w-72 bg-indigo-950 text-white flex flex-col z-50 shadow-2xl transition-all duration-300 no-print">
            <!-- Brand -->
            <div class="h-20 flex items-center justify-center lg:justify-start lg:px-10 border-b border-indigo-500/30">
                <Link :href="route('dashboard')" class="flex items-center gap-4 group">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1.5 shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <img src="/favicon.png" alt="Logo" class="w-full h-full object-contain" />
                    </div>
                    <div class="hidden lg:block text-left">
                        <template v-if="$page.props.auth.user?.role === 'super_admin'">
                            <span class="text-sm font-black tracking-widest block leading-none uppercase">Algorah</span>
                            <span class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mt-1 block">Control Maestro</span>
                        </template>
                        <template v-else>
                            <span class="text-sm font-black tracking-widest block leading-none uppercase">
                                {{ ($page.props.auth.user?.tenant?.name || 'ParkiApp').split(' ')[0] }}
                            </span>
                            <span class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mt-1 block">
                                {{ ($page.props.auth.user?.tenant?.name || 'ParkiApp').split(' ').slice(1).join(' ') || 'Cloud' }}
                            </span>
                        </template>
                    </div>
                </Link>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 lg:px-4 py-4 space-y-6 overflow-y-auto no-scrollbar">
                <!-- Operación -->
                <div v-if="$page.props.auth.user?.role !== 'super_admin'">
                    <p class="hidden lg:block text-[9px] font-black text-indigo-300 uppercase px-4 mb-2 tracking-[0.2em] opacity-70">Operación</p>
                    <div class="space-y-1">
                        <Link :href="route('tickets.entry')" 
                            :class="[route().current('tickets.entry') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Entradas</span>
                        </Link>
                        <Link :href="route('tickets.exit')" 
                            :class="[route().current('tickets.exit') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Salidas</span>
                        </Link>
                    </div>
                </div>

                <!-- Administración -->
                <div v-if="$page.props.auth.user?.role === 'admin' || $page.props.auth.user?.role === 'super_admin'">
                    <p class="hidden lg:block text-[9px] font-black text-indigo-300 uppercase px-4 mb-2 tracking-[0.2em] opacity-70">Administración</p>
                    <div class="space-y-1">
                        <Link :href="route('dashboard')" 
                            :class="[route().current('dashboard') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Panel de Control</span>
                        </Link>
                        <Link :href="route('shifts.index')" 
                            :class="[route().current('shifts.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75l2.25 2.25" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Turnos (Caja)</span>
                        </Link>
                        <Link :href="route('shifts.history')" 
                            :class="[route().current('shifts.history') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Historial de Turnos</span>
                        </Link>
                        <Link :href="route('rates.index')" 
                            :class="[route().current('rates.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Tarifas</span>
                        </Link>
                        <Link :href="route('memberships.index')" 
                            :class="[route().current('memberships.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Mensualidades</span>
                        </Link>
                        <Link :href="route('users.index')" 
                            v-if="$page.props.auth.user?.role === 'admin'"
                            :class="[route().current('users.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Personal</span>
                        </Link>
                        <Link :href="route('reports.index')" 
                            v-if="$page.props.auth.user?.role === 'admin'"
                            :class="[route().current('reports.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Reportes</span>
                        </Link>
                        <Link :href="route('settings.index')" 
                            v-if="$page.props.auth.user?.role === 'admin'"
                            :class="[route().current('settings.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.004.827c.422.348.53.954.26 1.43l-1.297 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Ajustes</span>
                        </Link>
                    </div>
                </div>

                <!-- Soporte -->
                <div v-if="$page.props.auth.user?.role === 'admin' || $page.props.auth.user?.role === 'super_admin'">
                    <p class="hidden lg:block text-[9px] font-black text-indigo-300 uppercase px-4 mb-2 mt-6 tracking-[0.2em] opacity-70">Ayuda</p>
                    <div class="space-y-1">
                        <Link :href="route('support.index')" 
                            :class="[route().current('support.*') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a.75.75 0 0 1 .712.755V18a3.375 3.375 0 0 1-3.375 3.375H4.5A3.375 3.375 0 0 1 1.125 18V5.085a.75.75 0 0 1 .712-.755 41.13 41.13 0 0 1 14.875 0Zm0 0A2.25 2.25 0 0 0 14.485 2.13a41.147 41.147 0 0 0-11.22 0A2.25 2.25 0 0 0 1.125 4.33M16.712 4.33V18a3.375 3.375 0 0 1-3.375 3.375H4.5A3.375 3.375 0 0 1 1.125 18V5.085a.75.75 0 0 1 .712-.755m14.875 0A40.867 40.867 0 0 1 22.5 5.25v2.247" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Soporte técnico</span>
                        </Link>
                    </div>
                </div>

                <!-- Control Maestro (Algorah) -->
                <div v-if="$page.props.auth.user?.role === 'super_admin'">
                    <p class="hidden lg:block text-[9px] font-black text-sky-300 uppercase px-4 mb-2 mt-6 tracking-[0.2em] opacity-70">Administración Maestro</p>
                    <div class="space-y-1">
                        <Link :href="route('admin.tenants.index')" 
                            :class="[route().current('admin.tenants.*') ? 'bg-sky-500 text-white shadow-xl' : 'text-sky-300 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200 border border-sky-500/30"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Control Maestro</span>
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- Logout -->
            <div class="p-4 mt-auto border-t border-indigo-500/30">
                <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl text-indigo-200 hover:bg-white/10 hover:text-white transition-all duration-200 group">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 group-hover:rotate-12 transition-transform"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" /></svg>
                    <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Cerrar Sesión</span>
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 bg-slate-50 relative">
            <header v-if="$slots.header" class="h-20 bg-white shadow-sm flex items-center px-10 z-40 no-print border-b border-slate-200">
                <!-- Sync Widget -->
                <div class="mr-6 hidden md:flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-200 shadow-inner">
                    <div class="flex flex-col text-right">
                        <span class="text-[8px] font-black uppercase tracking-widest text-slate-400 leading-none">Última Sincronización</span>
                        <span class="text-[10px] font-bold text-slate-700 mt-0.5">{{ lastSync }}</span>
                    </div>
                    <button @click="runManualSync" :disabled="syncStatus === 'syncing'" class="p-1.5 rounded-lg transition-all duration-300" :class="[syncStatus === 'syncing' ? 'bg-indigo-100 text-indigo-500 animate-spin' : 'bg-white text-slate-400 hover:text-indigo-600 hover:shadow-md']">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    </button>
                </div>
                <div class="flex-1">
                    <slot name="header" />
                </div>
                <!-- User Profile -->
                <Link :href="route('profile.edit')" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <div class="text-right hidden sm:block">
                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest leading-none">{{ $page.props.auth.user?.name }}</p>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                            {{ $page.props.auth.user?.role === 'super_admin' ? 'Master Algorah' : ($page.props.auth.user?.role === 'admin' ? 'Administrador' : 'Usuario') }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-950 rounded-xl flex items-center justify-center font-black text-xs shadow-inner">
                        {{ ($page.props.auth.user?.name || 'US').substring(0,2).toUpperCase() }}
                    </div>
                </Link>
            </header>

            <div class="flex-1 overflow-hidden p-8 flex flex-col relative">
                <!-- Toasts -->
                <div class="fixed top-6 right-6 w-80 z-[100] flex flex-col gap-3 no-print">
                    <transition-group name="toast">
                        <div v-if="$page.props.flash?.success" key="s" class="p-4 bg-white border border-slate-100 text-emerald-600 rounded-2xl flex items-center gap-4 shadow-2xl">
                            <div class="w-8 h-8 bg-emerald-500 text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg">✓</div>
                            <p class="text-[9px] font-black uppercase tracking-widest">{{ $page.props.flash.success }}</p>
                        </div>
                        <div v-if="$page.props.flash?.error" key="e" class="p-4 bg-white border border-slate-100 text-rose-600 rounded-2xl flex items-center gap-4 shadow-2xl">
                            <div class="w-8 h-8 bg-rose-500 text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg">!</div>
                            <p class="text-[9px] font-black uppercase tracking-widest">{{ $page.props.flash.error }}</p>
                        </div>
                    </transition-group>
                </div>

                <div class="flex-1 overflow-y-auto no-scrollbar relative min-h-0">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');
html, body { font-family: 'Outfit', sans-serif; height: 100%; margin: 0; padding: 0; background-color: #f8fafc; }
.no-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.no-scrollbar::-webkit-scrollbar-track { background: transparent; }
.no-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.no-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
@media print { .no-print { display: none !important; } main { margin-left: 0 !important; padding: 0 !important; } .bg-indigo-950 { background-color: #1e1b4b !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; } }
.toast-enter-active, .toast-leave-active { transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.toast-enter-from { opacity: 0; transform: translateX(100%); }
.toast-leave-to { opacity: 0; transform: scale(0.9); }
</style>
