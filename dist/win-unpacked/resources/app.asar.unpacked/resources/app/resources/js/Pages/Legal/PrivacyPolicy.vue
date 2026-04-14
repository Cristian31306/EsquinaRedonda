<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, ArrowLeft, Mail, Phone, MapPin } from 'lucide-vue-next';

// Nota: Estos textos son plantillas profesionales para Colombia. 
// Algorah debe revisarlos con su departamento legal.
</script>

<template>

    <Head title="Política de Privacidad - Algorah" />

    <div class="min-h-screen bg-slate-50 selection:bg-indigo-100 selection:text-indigo-900">
        <!-- Navigation -->
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-200">
            <div class="max-w-5xl mx-auto px-6 h-20 flex items-center justify-between">
                <Link :href="route('welcome')"
                    class="flex items-center gap-2 text-slate-400 hover:text-indigo-600 transition-colors">
                    <ArrowLeft class="w-4 h-4" />
                    <span class="text-[10px] font-black uppercase tracking-widest">Volver</span>
                </Link>
                <div class="flex items-center gap-2">
                    <ShieldCheck class="w-5 h-5 text-indigo-600" />
                    <span class="text-sm font-black tracking-tighter text-slate-900 uppercase">Seguridad Algorah</span>
                </div>
            </div>
        </nav>

        <main class="max-w-4xl mx-auto py-20 px-6">
            <header class="mb-16">
                <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-4">Política de Tratamiento de
                    Información</h1>
                <p class="text-slate-500 font-bold uppercase tracking-[0.2em] text-xs">Cumplimiento Ley 1581 de 2012
                    (Habeas Data) - Colombia</p>
            </header>

            <div class="bg-white rounded-[2.5rem] p-12 shadow-sm border border-slate-200 prose prose-slate max-w-none">
                <section class="mb-12">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-6">1. Identidad del
                        Responsable</h2>
                    <p class="text-slate-600 leading-relaxed font-medium">
                        Algorah (en adelante <b>"ParkiApp"</b>), empresa comprometida con la protección de datos
                        personales, informa que es la responsable del tratamiento de la información recolectada a través
                        de este sitio web y el software de gestión de parqueaderos asociado.
                    </p>
                </section>

                <section class="mb-12">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-6">2. Finalidad del
                        Tratamiento</h2>
                    <p class="text-slate-600 leading-relaxed font-medium mb-4">
                        Los datos personales recolectados (Nombres, Correo Electrónico, Teléfono, Placas de Vehículos,
                        entre otros) serán tratados para las siguientes finalidades:
                    </p>
                    <ul class="list-disc pl-6 space-y-2 text-slate-600 font-medium">
                        <li>Prestación del servicio de gestión de estacionamientos (ingreso, cobro y salida).</li>
                        <li>Envío de comunicaciones comerciales y actualizaciones del sistema ParkiApp.</li>
                        <li>Gestión de membresías y convenios con clientes corporativos.</li>
                        <li>Atención de peticiones, quejas y reclamos (PQR).</li>
                        <li>Cumplimiento de obligaciones legales y contables ante las autoridades colombianas.</li>
                    </ul>
                </section>

                <section class="mb-12">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-6">3. Derechos del Titular
                        (Habeas Data)</h2>
                    <p class="text-slate-600 leading-relaxed font-medium mb-4">
                        De acuerdo con la Ley 1581 de 2012, usted como titular de los datos tiene derecho a:
                    </p>
                    <ul class="list-disc pl-6 space-y-2 text-slate-600 font-medium">
                        <li><b>Conocer</b>, actualizar y rectificar sus datos personales.</li>
                        <li><b>Solicitar prueba</b> de la autorización otorgada.</li>
                        <li><b>Ser informado</b> sobre el uso que se le ha dado a sus datos.</li>
                        <li><b>Presentar quejas</b> ante la Superintendencia de Industria y Comercio (SIC).</li>
                        <li><b>Revocar la autorización</b> o solicitar la supresión de los datos cuando no se respeten
                            los principios legales.</li>
                    </ul>
                </section>

                <section class="mb-12">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-6">4. Canales de Atención
                    </h2>
                    <p class="text-slate-600 leading-relaxed font-medium mb-8">
                        Para ejercer sus derechos, puede contactar a nuestro oficial de protección de datos a través de:
                    </p>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 italic">
                            <Mail class="w-5 h-5 text-indigo-600 mb-3" />
                            <p class="text-[11px] font-black text-slate-900 uppercase">Correo Electrónico</p>
                            <p class="text-xs text-slate-500 font-bold">durancristian31306@gmail.com</p>
                        </div>
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 italic">
                            <Phone class="w-5 h-5 text-indigo-600 mb-3" />
                            <p class="text-[11px] font-black text-slate-900 uppercase">WhatsApp Corporativo</p>
                            <p class="text-xs text-slate-500 font-bold">+57 321 375 8945</p>
                        </div>
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 italic">
                            <MapPin class="w-5 h-5 text-indigo-600 mb-3" />
                            <p class="text-[11px] font-black text-slate-900 uppercase">Notificación Física</p>
                            <p class="text-xs text-slate-500 font-bold">Cll 17 #12A-05 Brr la Esperanza. Villa del
                                Rosario - Norte de Santander</p>
                        </div>
                    </div>
                </section>
            </div>

            <footer class="mt-16 text-center">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Última actualización: Abril
                    2026</p>
            </footer>
        </main>
    </div>
</template>
