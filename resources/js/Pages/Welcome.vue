<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    TrendingDown,
    ShieldAlert,
    UserX,
    Zap,
    Lock,
    BarChart3,
    Users,
    Printer,
    ShieldCheck,
    Smartphone,
    Eye,
    RefreshCw,
    Check,
    Calendar,
    MapPin,
    ArrowRight,
    MessageSquare,
    Send
} from 'lucide-vue-next';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    description: '',
    accept_terms: false,
});

const submitContact = () => {
    form.post(route('contact.submit'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            alert('¡Mensaje enviado con éxito! Nos contactaremos pronto.');
        },
    });
};

</script>

<template>

    <Head title="ParkiApp - Por Algorah" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans selection:bg-sky-500 selection:text-white pb-20">
        <!-- Header / Nav -->
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-sky-600 rounded-xl flex items-center justify-center text-white">
                        <Zap class="w-6 h-6" />
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="text-2xl font-bold tracking-tight text-slate-800">ParkiApp</span>
                        <span class="text-[10px] uppercase tracking-widest text-sky-600 font-bold">Por Algorah</span>
                    </div>
                </div>

                <div v-if="canLogin" class="flex gap-4 items-center">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                        class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-full font-semibold transition-all shadow-lg shadow-sky-200">
                        Panel de Control
                    </Link>

                    <template v-else>
                        <Link :href="route('login')"
                            class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-full font-semibold transition-all">
                            Log in
                        </Link>
                        <Link v-if="canRegister" :href="route('register')"
                            class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-full font-semibold transition-all shadow-lg shadow-sky-200">
                            Empezar Ahora
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <header class="relative overflow-hidden pt-16 pb-24 lg:pt-32 lg:pb-40">
            <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative z-10">
                    <span
                        class="inline-block px-4 py-1.5 bg-sky-100 text-sky-700 rounded-full text-sm font-bold tracking-wide uppercase mb-6">
                        Desarrollado por Algorah
                    </span>
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 leading-[1.1] mb-8">
                        Transforme su parqueadero en un <span class="text-sky-600">negocio inteligente</span>
                    </h1>
                    <p class="text-xl text-slate-600 mb-10 max-w-lg leading-relaxed">
                        Elimine las fugas de dinero, automatice sus cobros y tome el control total de su negocio desde
                        cualquier
                        lugar.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <Link :href="route('login')"
                            class="px-8 py-4 bg-sky-600 hover:bg-sky-700 text-white rounded-2xl font-bold text-lg transition-all shadow-xl shadow-sky-200 flex items-center justify-center gap-2">
                            Ingresar al Sistema
                            <ArrowRight class="w-5 h-5" />
                        </Link>
                        <a href="#contacto"
                            class="px-8 py-4 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-2xl font-bold text-lg transition-all flex items-center justify-center gap-2">
                            Saber Más
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-4 bg-sky-400/10 blur-3xl rounded-full"></div>
                    <img src="/img/hero.png" alt="ParkiApp Dashboard"
                        class="relative z-10 w-full rounded-2xl shadow-2xl border border-slate-200 rotate-2 hover:rotate-0 transition-transform duration-700">
                </div>
            </div>
        </header>

        <!-- Problems Section -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-20">
                    <h2 class="text-3xl lg:text-5xl font-bold text-slate-900 mb-6">¿Su negocio tiene estos problemas?
                    </h2>
                    <p class="text-lg text-slate-500">Muchos parqueaderos operan con métodos arcaicos que facilitan el
                        descontrol.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="p-10 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all">
                        <div
                            class="w-14 h-14 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mb-6">
                            <TrendingDown class="w-7 h-7" />
                        </div>
                        <h3 class="text-xl font-bold mb-4">Fugas de dinero</h3>
                        <p class="text-slate-600">Errores humanos en el cálculo manual que generan pérdidas silenciosas
                            todos
                            los días.</p>
                    </div>

                    <div class="p-10 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all">
                        <div
                            class="w-14 h-14 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mb-6">
                            <ShieldAlert class="w-7 h-7" />
                        </div>
                        <h3 class="text-xl font-bold mb-4">Incertidumbre en Caja</h3>
                        <p class="text-slate-600">Cierres de turno confusos. ¿Sabe realmente cuánto dinero debería estar
                            en la
                            caja?</p>
                    </div>

                    <div class="p-10 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all">
                        <div
                            class="w-14 h-14 bg-slate-200 text-slate-600 rounded-2xl flex items-center justify-center mb-6">
                            <UserX class="w-7 h-7" />
                        </div>
                        <h3 class="text-xl font-bold mb-4">Imagen Informal</h3>
                        <p class="text-slate-600">La falta de un ticket profesional genera desconfianza y posibles
                            reclamos
                            innecesarios.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Solution Section -->
        <section class="py-24 bg-slate-50 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 flex flex-col lg:flex-row items-center gap-20">
                <div class="flex-1">
                    <h2 class="text-4xl lg:text-6xl font-extrabold text-slate-900 mb-8 leading-tight">
                        La Potencia de la <span class="text-sky-600">Automatización</span>
                    </h2>
                    <div class="space-y-8">
                        <div class="flex gap-5">
                            <div
                                class="shrink-0 w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-200 flex items-center justify-center text-sky-600">
                                <Zap class="w-6 h-6" />
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-2">Sencillez Extrema</h4>
                                <p class="text-slate-600">Interfaz diseñada por Algorah para que su personal aprenda a
                                    operar el
                                    sistema en minutos.</p>
                            </div>
                        </div>
                        <div class="flex gap-5">
                            <div
                                class="shrink-0 w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-200 flex items-center justify-center text-sky-600">
                                <Lock class="w-6 h-6" />
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-2">Control de Ingresos</h4>
                                <p class="text-slate-600">Cada vehículo queda registrado. Evite que se "pierdan" tickets
                                    o
                                    ingresos entre turnos de empleados.</p>
                            </div>
                        </div>
                        <div class="flex gap-5">
                            <div
                                class="shrink-0 w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-200 flex items-center justify-center text-sky-600">
                                <ShieldCheck class="w-6 h-6" />
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-2">Base de Datos en la Nube</h4>
                                <p class="text-slate-600">Tecnología MySQL hospedada en servidores de alto rendimiento. Sus datos siempre estarán a salvo contra robos físicos de equipos.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-1 relative">
                    <div
                        class="bg-gradient-to-br from-sky-400 to-indigo-600 p-8 rounded-[3rem] shadow-2xl relative z-10">
                        <div class="bg-white rounded-2xl p-6 shadow-inner">
                            <div class="flex items-center justify-between mb-8 border-b pb-4">
                                <div class="font-bold text-slate-800">Cierre de Caja Actual</div>
                                <div class="text-green-600 font-bold">$1'245.000 COP</div>
                            </div>
                            <div class="space-y-4">
                                <div
                                    class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <div class="text-sm text-slate-500">Motos Ingresadas</div>
                                    <div class="font-bold">42</div>
                                </div>
                                <div
                                    class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <div class="text-sm text-slate-500">Carros Ingresados</div>
                                    <div class="font-bold">18</div>
                                </div>
                                <div
                                    class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <div class="text-sm text-slate-500">Membresías Activas</div>
                                    <div class="font-bold">7</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="py-24 bg-white overflow-hidden relative">
            <div class="absolute top-0 right-0 w-72 h-72 bg-sky-50 rounded-full blur-3xl -mr-32 -mt-32"></div>
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="text-center mb-20">
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-slate-900 mb-6 text-center">Inversión Inteligente para su Negocio</h2>
                    <p class="text-lg text-slate-500 text-center max-w-2xl mx-auto">Soluciones escalables diseñadas por Algorah para cada nivel de operación. Sin costos ocultos.</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-10 max-w-5xl mx-auto">
                    <!-- PLAN BÁSICO -->
                    <div class="bg-slate-50 rounded-[3.5rem] p-12 border border-slate-200 hover:shadow-2xl transition-all duration-500 group relative">
                        <div class="mb-10 text-left">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-slate-400 group-hover:text-sky-600 transition-colors shadow-sm">
                                    <Zap class="w-5 h-5" />
                                </div>
                                <span class="text-slate-500 font-black uppercase tracking-[0.3em] text-[10px]">Plan Básico</span>
                            </div>
                            <h3 class="text-3xl font-black mb-6 text-slate-900">Eficiencia Esencial</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-5xl font-black text-slate-900 tracking-tighter">$35.000</span>
                                    <span class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">/ Mensual</span>
                                </div>
                                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                                    Anual: $399.000 <span class="opacity-60 ml-1">(-5% Desc)</span>
                                </div>
                            </div>
                            <p class="text-slate-500 text-sm mt-6 leading-relaxed">Perfecto para parqueaderos independientes que buscan control total con una inversión mínima.</p>
                        </div>

                        <ul class="space-y-6 mb-12">
                            <li class="flex items-center gap-4">
                                <div class="w-6 h-6 bg-white rounded-lg flex items-center justify-center text-sky-600 shadow-sm border border-slate-100">
                                    <Check class="w-4 h-4" />
                                </div>
                                <span class="text-sm font-bold text-slate-700">Hasta <b>3 Usuarios</b> de sistema</span>
                            </li>
                            <li class="flex items-center gap-4">
                                <div class="w-6 h-6 bg-white rounded-lg flex items-center justify-center text-sky-600 shadow-sm border border-slate-100">
                                    <Check class="w-4 h-4" />
                                </div>
                                <span class="text-sm font-bold text-slate-700">Control de entradas y salidas</span>
                            </li>
                            <li class="flex items-center gap-4">
                                <div class="w-6 h-6 bg-white rounded-lg flex items-center justify-center text-sky-600 shadow-sm border border-slate-100">
                                    <Check class="w-4 h-4" />
                                </div>
                                <span class="text-sm font-bold text-slate-700">Módulo de Auditoría y Caja</span>
                            </li>
                        </ul>
                        
                        <a href="https://wa.me/573213758945?text=Hola! Quiero empezar con el Plan Básico de ParkiApp" target="_blank" class="block w-full py-5 bg-white border-2 border-slate-200 text-slate-900 rounded-3xl font-black text-xs uppercase tracking-[0.2em] text-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm">
                            Empezar Básico
                        </a>
                    </div>

                    <!-- PLAN PRO -->
                    <div class="bg-slate-900 rounded-[3.5rem] p-12 border-4 border-sky-500 shadow-2xl lg:scale-105 transition-transform duration-500 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
                        
                        <div class="absolute top-8 right-8">
                            <span class="px-5 py-2 bg-sky-500 text-slate-950 rounded-full text-[9px] font-black uppercase tracking-[0.2em] shadow-lg">Más Potente</span>
                        </div>

                        <div class="mb-10 text-left relative z-10">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-sky-500/20 rounded-xl flex items-center justify-center text-sky-400 shadow-sm border border-sky-500/30">
                                    <ShieldCheck class="w-5 h-5" />
                                </div>
                                <span class="text-sky-400 font-black uppercase tracking-[0.3em] text-[10px]">Plan Profesional</span>
                            </div>
                            <h3 class="text-3xl font-black mb-6 text-white">Dominio Total</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-5xl font-black text-white tracking-tighter">$50.000</span>
                                    <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">/ Mensual</span>
                                </div>
                                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-sky-500 text-slate-950 rounded-full text-[10px] font-black uppercase tracking-widest">
                                    Anual: $570.000 <span class="opacity-70 ml-1">(-5% Desc)</span>
                                </div>
                            </div>
                            <p class="text-slate-400 text-sm mt-6 leading-relaxed">Para negocios de alto tráfico que requieren usuarios ilimitados y soporte prioritario 24/7.</p>
                        </div>

                        <ul class="space-y-6 mb-12 relative z-10">
                            <li class="flex items-center gap-4">
                                <div class="w-6 h-6 bg-sky-500 text-slate-950 rounded-lg flex items-center justify-center shadow-lg">
                                    <Check class="w-4 h-4" />
                                </div>
                                <span class="text-sm font-bold text-white">Usuarios <b>ILIMITADOS</b></span>
                            </li>
                            <li class="flex items-center gap-4">
                                <div class="w-6 h-6 bg-sky-500/20 text-sky-400 rounded-lg flex items-center justify-center border border-sky-500/30">
                                    <Check class="w-4 h-4" />
                                </div>
                                <span class="text-sm font-bold text-slate-200">Soporte Técnico de <b>Algorah</b></span>
                            </li>
                            <li class="flex items-center gap-4">
                                <div class="w-6 h-6 bg-sky-500/20 text-sky-400 rounded-lg flex items-center justify-center border border-sky-500/30">
                                    <Check class="w-4 h-4" />
                                </div>
                                <span class="text-sm font-bold text-slate-200">Personalización de Marca incluida</span>
                            </li>
                        </ul>
                        
                        <a href="https://wa.me/573213758945?text=Hola! Me interesa el Plan PRO de ParkiApp para mi negocio" target="_blank" class="relative z-10 block w-full py-5 bg-sky-500 hover:bg-sky-400 text-slate-950 rounded-3xl font-black text-xs uppercase tracking-[0.2em] text-center transition-all shadow-xl shadow-sky-500/20">
                            Activar Plan PRO
                        </a>
                    </div>
                </div>
                
                <!-- Trust Badge -->
                <div class="mt-20 flex flex-col items-center gap-6">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">Hospedado y Respaldado por</p>
                    <div class="flex items-center gap-8 opacity-40 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                        <div class="flex items-center gap-2">
                            <ShieldCheck class="w-5 h-5 text-indigo-600" />
                            <span class="text-xl font-black tracking-tighter text-slate-800">Cifrado de Alta Seguridad</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contacto" class="py-24 bg-slate-900 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-sky-600/10 skew-x-12 translate-x-1/2"></div>

            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="grid lg:grid-cols-2 gap-20 items-center">
                    <div>
                        <h2 class="text-4xl lg:text-6xl font-bold mb-8 leading-tight">Hablemos de su <span
                                class="text-sky-400">Próximo Paso</span></h2>
                        <p class="text-xl text-slate-400 mb-12">
                            ¿Tiene alguna duda técnica o comercial? Nuestro equipo de Algorah está listo para
                            asesorarle.
                        </p>

                        <div class="space-y-8">
                            <a href="https://wa.me/573213758945" target="_blank"
                                class="flex items-center gap-6 p-6 bg-white/5 rounded-3xl border border-white/10 hover:bg-white/10 transition-all group">
                                <div
                                    class="w-14 h-14 bg-green-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/20">
                                    <MessageSquare class="w-7 h-7" />
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold">WhatsApp Directo</h4>
                                    <p class="text-slate-400 text-sm">+57 321 375 8945</p>
                                </div>
                                <ArrowRight
                                    class="w-6 h-6 ml-auto opacity-0 group-hover:opacity-100 group-hover:translate-x-2 transition-all text-sky-400" />
                            </a>

                            <div class="flex items-center gap-6 p-6">
                                <div
                                    class="w-14 h-14 bg-sky-600 text-white rounded-2xl flex items-center justify-center">
                                    <MapPin class="w-7 h-7" />
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold">Ubicación</h4>
                                    <p class="text-slate-400 text-sm">Cúcuta, Norte de Santander</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[3rem] p-10 lg:p-14 text-slate-900 shadow-2xl">
                        <h3 class="text-3xl font-bold mb-8">Déjenos sus datos</h3>
                        <form @submit.prevent="submitContact" class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-500 uppercase mb-2">Nombre
                                    Completo</label>
                                <input v-model="form.name" type="text" placeholder="Ej: Juan Pérez"
                                    class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-500 uppercase mb-2">Correo
                                    Electrónico</label>
                                <input v-model="form.email" type="email" placeholder="juan@ejemplo.com"
                                    class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-500 uppercase mb-2">Número de Teléfono / WhatsApp</label>
                                <input v-model="form.phone" type="tel" placeholder="Ej: 321 375 8945"
                                    class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-500 uppercase mb-2">¿Cómo podemos
                                    ayudarle?</label>
                                <textarea v-model="form.description" rows="4"
                                    placeholder="Cuéntenos sobre su parqueadero..."
                                    class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all outline-none resize-none"
                                    required></textarea>
                            </div>

                            <!-- Habeas Data Checkbox -->
                            <div class="flex items-start gap-3 px-2">
                                <input 
                                    v-model="form.accept_terms" 
                                    type="checkbox" 
                                    id="habeas_data" 
                                    required
                                    class="mt-1 w-5 h-5 rounded-lg border-slate-200 text-sky-600 focus:ring-sky-500 transition-all"
                                >
                                <label for="habeas_data" class="text-[11px] font-bold text-slate-500 leading-tight">
                                    Acepto la <Link :href="route('legal.privacy')" class="text-sky-600 hover:underline">Política de Tratamiento de Datos</Link> y los <Link :href="route('legal.terms')" class="text-sky-600 hover:underline">Términos del Servicio</Link> (Habeas Data - Ley 1581 de 2012).
                                </label>
                            </div>

                            <button :disabled="form.processing || !form.accept_terms" type="submit"
                                class="w-full py-5 bg-sky-600 hover:bg-sky-700 text-white rounded-2xl font-bold text-lg shadow-xl shadow-sky-200 transition-all flex items-center justify-center gap-3">
                                <span v-if="form.processing">Enviando...</span>
                                <span v-else class="flex items-center gap-2">Enviar Mensaje
                                    <Send class="w-5 h-5" />
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-50 pt-24 pb-12 border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-center gap-10">
                    <div class="flex flex-col items-center md:items-start gap-4">
                        <div class="flex items-center gap-2">
                            <Zap class="w-8 h-8 text-sky-600" />
                            <span class="text-3xl font-black tracking-tighter text-slate-800">ParkiApp</span>
                        </div>
                        <p class="text-slate-500 text-center md:text-left max-w-sm">
                            La plataforma líder en gestión de parqueaderos inteligentes. Crecer nunca fue tan fácil.
                        </p>
                    </div>

                    <div class="text-center md:text-right">
                        <div class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Creado con ❤️ por
                        </div>
                        <div class="text-3xl font-black text-slate-900 tracking-tighter mb-6">Algorah<span
                                class="text-sky-600">.</span></div>
                        
                        <div class="flex flex-col md:flex-row items-center md:justify-end gap-6">
                            <Link :href="route('legal.privacy')" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-sky-600 transition-colors">Privacidad</Link>
                            <Link :href="route('legal.terms')" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-sky-600 transition-colors">Términos</Link>
                            <a href="https://www.sic.gov.co" target="_blank" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-indigo-600 transition-colors">SuperIndustria (SIC)</a>
                        </div>
                    </div>
                </div>

                <div class="mt-20 pt-10 border-t border-slate-200 text-center">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-[0.2em]">
                        © {{ new Date().getFullYear() }} ParkiApp SyS · Todos los derechos reservados por Algorah
                    </p>
                </div>
            </div>
        </footer>

        <!-- Botón Flotante WhatsApp -->
        <a href="https://wa.me/573213758945" target="_blank"
            class="fixed bottom-8 right-8 z-[100] w-16 h-16 bg-green-500 text-white rounded-full shadow-2xl flex items-center justify-center hover:bg-green-600 hover:scale-110 transition-all duration-300 group">
            <MessageSquare class="w-8 h-8 group-hover:scale-110 transition-transform" />
            <span
                class="absolute right-20 bg-white text-slate-900 px-4 py-2 rounded-xl text-sm font-bold shadow-xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none border border-slate-100 whitespace-nowrap">
                ¿Necesitas ayuda? Chatea con nosotros
            </span>
        </a>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

.font-sans {
    font-family: 'Outfit', sans-serif;
}

section {
    scroll-margin-top: 5rem;
}
</style>
