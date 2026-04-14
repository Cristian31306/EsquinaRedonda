<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { FileText, ArrowLeft, ShieldCheck, Scale, CreditCard } from 'lucide-vue-next';
</script>

<template>
    <Head title="Términos y Condiciones - Algorah" />

    <div class="min-h-screen bg-slate-50 selection:bg-indigo-100 selection:text-indigo-900">
        <!-- Navigation -->
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-200">
            <div class="max-w-5xl mx-auto px-6 h-20 flex items-center justify-between">
                <Link :href="route('welcome')" class="flex items-center gap-2 text-slate-400 hover:text-indigo-600 transition-colors">
                    <ArrowLeft class="w-4 h-4" />
                    <span class="text-[10px] font-black uppercase tracking-widest">Volver</span>
                </Link>
                <div class="flex items-center gap-2">
                    <FileText class="w-5 h-5 text-indigo-600" />
                    <span class="text-sm font-black tracking-tighter text-slate-900 uppercase">Términos Algorah</span>
                </div>
            </div>
        </nav>

        <main class="max-w-4xl mx-auto py-20 px-6">
            <header class="mb-16">
                <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-4">Términos y Condiciones de Uso</h1>
                <p class="text-slate-500 font-bold uppercase tracking-[0.2em] text-xs">SaaS ParkiApp - Estatuto del Consumidor Colombia</p>
            </header>

            <div class="bg-white rounded-[2.5rem] p-12 shadow-sm border border-slate-200 prose prose-slate max-w-none">
                <section class="mb-12">
                    <div class="flex items-center gap-3 mb-6">
                        <Scale class="w-6 h-6 text-indigo-600" />
                        <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight m-0">1. Alcance del Servicio</h2>
                    </div>
                    <p class="text-slate-600 leading-relaxed font-medium">
                        ParkiApp es una plataforma SaaS (Software as a Service) desarrollada por Algorah para la automatización, control y auditoría de parqueaderos. El uso de la plataforma está sujeto a la aceptación integral de estos términos. El servicio incluye el alojamiento en la nube, actualizaciones automáticas y soporte básico.
                    </p>
                </section>

                <section class="mb-12">
                    <div class="flex items-center gap-3 mb-6">
                        <CreditCard class="w-6 h-6 text-indigo-600" />
                        <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight m-0">2. Suscripciones y Pagos</h2>
                    </div>
                    <p class="text-slate-600 leading-relaxed font-medium mb-4">
                        El acceso a ParkiApp se rige por planes de suscripción (Básico y Pro) con periodicidad mensual o anual:
                    </p>
                    <ul class="list-disc pl-6 space-y-2 text-slate-600 font-medium">
                        <li><b>Mora en el pago:</b> El incumplimiento en el pago de la suscripción resultará en la suspensión automática del servicio tras 5 días calendario.</li>
                        <li><b>Aumentos de tarifa:</b> Algorah se reserva el derecho de ajustar los precios, notificando al cliente con un mínimo de 30 días de antelación.</li>
                        <li><b>Derecho de Retracto:</b> Según la Ley 1480 de 2011, el cliente tiene derecho a solicitar la devolución del dinero dentro de los primeros 5 días hábiles tras la compra inicial, siempre que el servicio no haya sido utilizado intensivamente.</li>
                    </ul>
                </section>

                <section class="mb-12">
                    <div class="flex items-center gap-3 mb-6">
                        <ShieldCheck class="w-6 h-6 text-indigo-600" />
                        <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight m-0">3. Responsabilidad Técnica</h2>
                    </div>
                    <p class="text-slate-600 leading-relaxed font-medium mb-4">
                        Algorah garantiza una disponibilidad del servicio (uptime) del 99.5%, salvo en periodos de mantenimiento programado o fallas críticas en los proveedores de infraestructura (AWS/Google Cloud).
                    </p>
                    <div class="bg-rose-50 border-1 border-rose-100 p-6 rounded-2xl italic">
                        <p class="text-xs text-rose-900 font-bold m-0 leading-relaxed">
                            IMPORTANTE: Algorah NO se hace responsable por pérdidas de dinero, errores de auditoría causados por mala manipulación del usuario, o ataques externos debidos a contraseñas débiles. Cada parqueadero es responsable de la integridad de sus registros operativos.
                        </p>
                    </div>
                </section>

                <section class="mb-12">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-6">4. Propiedad Intelectual</h2>
                    <p class="text-slate-600 leading-relaxed font-medium">
                        Todo el código fuente, diseño gráfico, logotipos y algoritmos de ParkiApp son propiedad exclusiva de Algorah. Queda prohibida la ingeniería inversa, copia total o parcial o comercialización a terceros sin autorización expresa por escrito.
                    </p>
                </section>
            </div>

            <footer class="mt-16 text-center">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Servicio prestado bajo leyes colombianas</p>
            </footer>
        </main>
    </div>
</template>
