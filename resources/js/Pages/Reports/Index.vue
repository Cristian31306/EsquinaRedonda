<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    filters: Object,
    monthlyIncome: Array,
    dailyIncome: Object,
    summary: Object,
    availableYears: Array,
});

const chartRef = ref(null);
let myChart = null;

const months = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

const selectedYear = ref(props.filters.year);
const selectedMonth = ref(props.filters.month);

const updateFilters = () => {
    router.get(route('reports.index'), {
        year: selectedYear.value,
        month: selectedMonth.value,
    }, { preserveState: true, replace: true });
};

watch([selectedYear, selectedMonth], () => {
    updateFilters();
});

onMounted(() => {
    renderChart();
});

watch(() => props.dailyIncome, () => {
    renderChart();
});

const renderChart = () => {
    if (myChart) myChart.destroy();
    if (!chartRef.value) return;

    const ctx = chartRef.value.getContext('2d');
    myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: props.dailyIncome.labels,
            datasets: [{
                label: 'Ingresos Diarios ($)',
                data: props.dailyIncome.values,
                borderColor: '#1e1b4b', // indigo-950
                backgroundColor: 'rgba(30, 27, 75, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#1e1b4b',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: '#1e1b4b',
                    titleFont: { size: 10, weight: 'bold' },
                    bodyFont: { size: 12, weight: 'black' },
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: (context) => `Total: $${context.parsed.y.toLocaleString()}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        font: { size: 10, weight: 'bold' },
                        callback: (value) => '$' + value.toLocaleString()
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: 'bold' } }
                }
            }
        }
    });
};

const handleExportExcel = () => {
    window.location.href = route('reports.excel', {
        year: selectedYear.value,
        month: selectedMonth.value,
    });
};

const handleExportPdf = () => {
    window.location.href = route('reports.pdf', {
        year: selectedYear.value,
        month: selectedMonth.value,
    });
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(val);
};
</script>

<template>
    <Head title="Reportes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center no-print">
                <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-widest">
                    📊 Reportes y Estadísticas
                </h2>
                <div class="flex gap-4">
                                    <button @click="handleExportExcel" class="px-6 py-2 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition-all flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                        Excel
                    </button>
                    <button @click="handleExportPdf" class="px-6 py-2 bg-rose-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-800 shadow-lg shadow-rose-100 transition-all flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        PDF
                    </button>
                </div>
            </div>
            
            <!-- Título especializado para Impresión -->
            <div class="hidden print-only">
                <h1 class="text-2xl font-black uppercase text-center mb-2">Esquina Redonda - Reporte Mensual</h1>
                <p class="text-center font-bold uppercase text-xs mb-8">{{ months[selectedMonth-1] }} {{ selectedYear }}</p>
            </div>
        </template>

        <div class="space-y-8">
            <!-- Filtros -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-wrap gap-8 items-end no-print">
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Año</label>
                    <select v-model="selectedYear" class="w-40 bg-slate-50 border-none rounded-2xl p-3 text-xs font-black uppercase tracking-widest focus:ring-2 focus:ring-indigo-950 shadow-inner">
                        <option v-for="y in availableYears" :key="y" :value="y">{{ y }}</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Mes</label>
                    <select v-model="selectedMonth" class="w-48 bg-slate-50 border-none rounded-2xl p-3 text-xs font-black uppercase tracking-widest focus:ring-2 focus:ring-indigo-950 shadow-inner">
                        <option v-for="(m, i) in months" :key="i" :value="i+1">{{ m }}</option>
                    </select>
                </div>
            </div>

            <!-- Resumen -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Ingresos por Tickets</p>
                    <h4 class="text-3xl font-black text-indigo-950 tracking-tighter">{{ formatCurrency(summary.tickets) }}</h4>
                </div>
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Ingresos por Mensualidades</p>
                    <h4 class="text-3xl font-black text-indigo-950 tracking-tighter">{{ formatCurrency(summary.memberships) }}</h4>
                </div>
                <div class="bg-gradient-to-br from-indigo-950 to-slate-900 p-8 rounded-[2rem] shadow-xl flex flex-col justify-between transform hover:scale-[1.02] transition-transform">
                    <p class="text-[9px] font-black text-indigo-200 uppercase tracking-[0.2em] mb-4">Resumen Total Mes</p>
                    <h4 class="text-3xl font-black text-white tracking-tighter">{{ formatCurrency(summary.total) }}</h4>
                </div>
            </div>

            <!-- Gráfica -->
            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Comportamiento Diario</h3>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Evolución del recaudo día tras día</p>
                    </div>
                </div>
                <div class="h-80 w-full">
                    <canvas ref="chartRef"></canvas>
                </div>
            </div>

            <!-- Tabla de Resumen Mensual del Año -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden no-print">
                <div class="p-8 border-b border-slate-50">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Recaudo Anual {{ selectedYear }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50">
                                <th v-for="m in months" :key="m" class="px-4 py-4 text-[8px] font-black uppercase text-slate-400 tracking-widest text-center">{{ m.substring(0,3) }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td v-for="(val, i) in monthlyIncome" :key="i" class="px-4 py-6 border-r border-slate-50 last:border-0">
                                    <p class="text-[10px] font-black text-slate-900 text-center">{{ formatCurrency(val) }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Aviso de Footer para Impresión -->
            <div class="hidden print-only mt-20 text-center border-t pt-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Esquina Redonda - Sistema de Gestión de Parqueaderos</p>
                <p class="text-[8px] font-bold text-slate-400 mt-2 italic">Reporte generado el {{ new Date().toLocaleString() }}</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    .print-only {
        display: block !important;
    }
    .bg-white {
        border: none !important;
        box-shadow: none !important;
    }
    .rounded-\[2rem\], .rounded-\[2\.5rem\] {
        border-radius: 0 !important;
    }
    main {
        padding: 0 !important;
        background: white !important;
    }
}
</style>
