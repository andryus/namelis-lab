<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    recent_orders: Array,
});

const statusLabel = {
    draft: 'Rascunho',
    awaiting_pickup: 'Aguarda Recolha',
    in_transit: 'Em Transporte',
    at_lab: 'No Laboratório',
    in_production: 'Em Produção',
    quality_check: 'Controlo Qualidade',
    awaiting_delivery: 'Aguarda Entrega',
    delivered: 'Entregue',
    cancelled: 'Cancelado',
};

const statusColor = {
    draft: 'bg-slate-100 text-slate-700',
    awaiting_pickup: 'bg-yellow-100 text-yellow-800',
    in_transit: 'bg-blue-100 text-blue-800',
    at_lab: 'bg-indigo-100 text-indigo-800',
    in_production: 'bg-purple-100 text-purple-800',
    quality_check: 'bg-orange-100 text-orange-800',
    awaiting_delivery: 'bg-cyan-100 text-cyan-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};
</script>

<template>
    <Head title="Dashboard Admin" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Painel de Administração</h2>
                <span class="text-sm text-gray-500">Namelis — Laboratório de Prótese Dentária</span>
            </div>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

            <!-- Stats cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                    <p class="text-sm text-slate-500 font-medium">Total Pedidos</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.total_orders }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                    <p class="text-sm text-slate-500 font-medium">Em Produção</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">{{ stats.in_production }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                    <p class="text-sm text-slate-500 font-medium">Aguarda Entrega</p>
                    <p class="text-3xl font-bold text-cyan-600 mt-1">{{ stats.awaiting_delivery }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                    <p class="text-sm text-slate-500 font-medium">Entregues (mês)</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ stats.delivered_month }}</p>
                </div>
            </div>

            <!-- Recent orders -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-800">Pedidos Recentes</h3>
                    <a :href="route('orders.index')" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Ver todos →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Referência</th>
                                <th class="px-6 py-3 text-left">Clínica</th>
                                <th class="px-6 py-3 text-left">Paciente</th>
                                <th class="px-6 py-3 text-left">Estado</th>
                                <th class="px-6 py-3 text-left">Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="order in recent_orders" :key="order.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono font-semibold text-blue-700">{{ order.reference }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ order.clinic?.name ?? '—' }}</td>
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ order.patient_ref }}</td>
                                <td class="px-6 py-4">
                                    <span :class="['inline-flex px-2.5 py-1 rounded-full text-xs font-semibold', statusColor[order.status] ?? 'bg-slate-100 text-slate-600']">
                                        {{ statusLabel[order.status] ?? order.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-xs">{{ new Date(order.created_at).toLocaleDateString('pt-PT') }}</td>
                            </tr>
                            <tr v-if="!recent_orders?.length">
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400">Nenhum pedido ainda.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
