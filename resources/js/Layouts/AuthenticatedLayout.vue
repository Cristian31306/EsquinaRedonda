<script setup>
import { ref, watch, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const showingNavigationDropdown = ref(false);

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
        <!-- Sidebar: Vibrant and Solid -->
        <aside class="w-20 lg:w-72 bg-indigo-950 text-white flex flex-col z-50 shadow-2xl transition-all duration-300 no-print">
            <!-- Brand -->
            <div class="h-20 flex items-center justify-center lg:justify-start lg:px-10 border-b border-indigo-500/30">
                <Link :href="route('dashboard')" class="flex items-center gap-4 group">
                    <div class="w-10 h-10 bg-white text-indigo-950 rounded-xl flex items-center justify-center font-black text-xl shadow-lg group-hover:scale-110 transition-transform duration-300">ER</div>
                    <div class="hidden lg:block">
                        <span class="text-sm font-black tracking-widest block leading-none uppercase">Esquina</span>
                        <span class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mt-1 block">Redonda</span>
                    </div>
                </Link>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 lg:px-4 py-4 space-y-6 overflow-y-auto no-scrollbar">
                <!-- Group 1 -->
                <div>
                    <p class="hidden lg:block text-[9px] font-black text-indigo-300 uppercase px-4 mb-2 tracking-[0.2em] opacity-70">Operación</p>
                    <div class="space-y-1">
                        <Link 
                            :href="route('tickets.entry')" 
                            :class="[route().current('tickets.entry') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200 group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Entrada</span>
                        </Link>
                        <Link 
                            :href="route('tickets.exit')" 
                            :class="[route().current('tickets.exit') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200 group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Salida</span>
                        </Link>
                    </div>
                </div>

                <!-- Group 2 -->
                <div>
                    <p class="hidden lg:block text-[9px] font-black text-indigo-300 uppercase px-4 mb-2 tracking-[0.2em] opacity-70">Administración</p>
                    <div class="space-y-1">
                        <Link 
                            :href="route('dashboard')" 
                            :class="[route().current('dashboard') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200 group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Panel</span>
                        </Link>
                        <Link 
                            v-if="$page.props.auth.user.role === 'admin'"
                            :href="route('reports.index')" 
                            :class="[route().current('reports.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200 group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Reportes</span>
                        </Link>
                        <Link 
                            :href="route('shifts.index')" 
                            :class="[route().current('shifts.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200 group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75l2.25 2.25" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Caja</span>
                        </Link>
                        <Link 
                            v-if="$page.props.auth.user.role === 'admin'"
                            :href="route('shifts.history')" 
                            :class="[route().current('shifts.history') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200 group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .415.162.791.425 1.066.262.275.612.446.996.446.384 0 .734-.171.996-.446.263-.275.425-.651.425-1.066 0-.231-.035-.454-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25c.754 0 1.41.374 1.8 1.02m-5.8 0A2.251 2.251 0 0 0 13.5 2.25c.754 0 1.41.374 1.8 1.02m0 0c.13.204.26.417.39.639" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Auditoría</span>
                        </Link>
                        <Link 
                            :href="route('memberships.index')" 
                            :class="[route().current('memberships.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200 group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Mensualidades</span>
                        </Link>
                        <Link 
                            v-if="$page.props.auth.user.role === 'admin'"
                            :href="route('rates.index')" 
                            :class="[route().current('rates.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200 group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.659A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Tarifas</span>
                        </Link>
                        <Link 
                            v-if="$page.props.auth.user.role === 'admin'"
                            :href="route('users.index')" 
                            :class="[route().current('users.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200 group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Usuarios</span>
                        </Link>
                        <Link 
                            v-if="$page.props.auth.user.role === 'admin'"
                            :href="route('settings.index')" 
                            :class="[route().current('settings.index') ? 'bg-white text-indigo-950 shadow-lg' : 'text-indigo-100 hover:bg-white/10']"
                            class="flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl transition-all duration-200 group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.004.827c.422.348.53.954.26 1.43l-1.297 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Ajustes</span>
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- Logout -->
            <div class="p-4 mt-auto border-t border-indigo-500/30">
                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button"
                    class="w-full flex items-center justify-center lg:justify-start gap-4 p-2.5 rounded-2xl text-indigo-200 hover:bg-white/10 hover:text-white transition-all duration-200 group"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 group-hover:rotate-12 transition-transform"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" /></svg>
                    <span class="hidden lg:block text-xs font-black uppercase tracking-widest">Cerrar Sesión</span>
                </Link>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 bg-slate-50 relative">
            <!-- Header: High Contrast -->
            <header v-if="$slots.header" class="h-20 bg-white shadow-sm flex items-center px-10 z-40 no-print border-b border-slate-200">
                <div class="flex-1">
                    <slot name="header" />
                </div>
                <!-- User Info -->
                <Link :href="route('profile.edit')" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <div class="text-right hidden sm:block">
                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest leading-none">{{ $page.props.auth.user.name }}</p>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $page.props.auth.user.role === 'admin' ? 'Administrador' : 'Usuario' }}</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-950 rounded-xl flex items-center justify-center font-black text-xs shadow-inner">
                        {{ $page.props.auth.user.name.substring(0,2).toUpperCase() }}
                    </div>
                </Link>
            </header>

            <!-- Scrollable Content Container -->
            <div class="flex-1 overflow-hidden p-8 flex flex-col relative">
                <!-- Notifications Area: Floating Toasts -->
                <div class="fixed top-6 right-6 w-80 z-[100] flex flex-col gap-3 no-print">
                    <transition-group name="toast">
                        <div v-if="$page.props.flash && $page.props.flash.success" key="s" class="p-4 bg-white border border-slate-100 text-emerald-600 rounded-2xl flex items-center gap-4 shadow-2xl animate-in slide-in-from-right duration-300">
                            <div class="w-8 h-8 bg-emerald-500 text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg shadow-emerald-100">✓</div>
                            <div class="flex-1">
                                <p class="text-[9px] font-black uppercase tracking-widest">{{ $page.props.flash.success }}</p>
                            </div>
                            <button @click="$page.props.flash.success = null" class="text-slate-300 hover:text-slate-900 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div v-if="$page.props.flash && $page.props.flash.error" key="e" class="p-4 bg-white border border-slate-100 text-rose-600 rounded-2xl flex items-center gap-4 shadow-2xl animate-in slide-in-from-right duration-300">
                            <div class="w-8 h-8 bg-rose-500 text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg shadow-rose-100">!</div>
                            <div class="flex-1">
                                <p class="text-[9px] font-black uppercase tracking-widest">{{ $page.props.flash.error }}</p>
                            </div>
                            <button @click="$page.props.flash.error = null" class="text-slate-300 hover:text-slate-900 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </transition-group>
                </div>

                <!-- Main Viewport Slot -->
                <div class="flex-1 overflow-y-auto no-scrollbar relative min-h-0">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');

html, body {
    font-family: 'Outfit', sans-serif;
    height: 100%;
    margin: 0;
    padding: 0;
    background-color: #f8fafc; /* slate-50 */
}

/* Custom Scrollbar for inner zones */
.no-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.no-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.no-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.no-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}

@media print {
    .no-print {
        display: none !important;
    }
    main {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .bg-indigo-950 {
        background-color: #1e1b4b !important;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}

.fade-enter-active, .fade-leave-active {
  transition: all 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.toast-enter-active, .toast-leave-active {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}
.toast-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
</style>
