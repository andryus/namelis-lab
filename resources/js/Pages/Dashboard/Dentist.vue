<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    clinic: Object,
    orders: Array,
    stats: Object,
});

const statusLabel = {
    draft: 'Rascunho', awaiting_pickup: 'Aguarda Recolha', in_transit: 'Em Transporte',
    at_lab: 'No Laboratório', in_production: 'Em Produção', quality_check: 'Controlo Qualidade',
    awaiting_delivery: 'Aguarda Entrega', delivered: 'Entregue', cancelled: 'Cancelado',
};
const statusColor = {
    draft: 'bg-slate-100 text-slate-600', awaiting_pickup: 'bg-yellow-100 text-yellow-700',
    in_transit: 'bg-blue-100 text-blue-700', at_lab: 'bg-indigo-100 text-indigo-700',
    in_production: 'bg-purple-100 text-purple-700', quality_check: 'bg-orange-100 text-orange-700',
    awaiting_delivery: 'bg-cyan-100 text-cyan-700', delivered: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-700',
};
</script>

<template>
    <Head title="Os Meus Pedidos" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Os Meus Pedidos</h2>
                <Link :href="route('orders.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
                    + Novo Pedido
                </Link>
            </div>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

            <!-- Clinic not approved yet -->
            <div v-if="!clinic?.approved" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-800 text-sm">
                A sua clínica está a aguardar aprovação pelo laboratório. Entretanto pode explorar a plataforma.
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-white rounded-xl p-5 border border-slate-100 shadow-sm">
                    <p class="text-sm text-slate-500">Pedidos Ativos</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.pending }}</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-slate-100 shadow-sm">
                    <p class="text-sm text-slate-500">Entregues</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ stats.delivered }}</p>
                </div>
            </div>

            <!-- Orders list -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Referência</th>
                                <th class="px-6 py-3 text-left">Paciente</th>
                                <th class="px-6 py-3 text-left">Trabalho</th>
                                <th class="px-6 py-3 text-left">Estado</th>
                                <th class="px-6 py-3 text-left">Entrega</th>
                                <th class="px-6 py-3 text-left"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="order in orders" :key="order.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-blue-700">{{ order.reference }}</td>
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ order.patient_ref }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="item in (order.items ?? []).slice(0, 2)" :key="item.id" class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded text-xs">
                                            {{ item.tooth_fdi }}
                                        </span>
                                        <span v-if="(order.items?.length ?? 0) > 2" class="text-xs text-slate-400">+{{ order.items.length - 2 }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="['inline-flex px-2.5 py-1 rounded-full text-xs font-semibold', statusColor[order.status]]">
                                        {{ statusLabel[order.status] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-xs">
                                    {{ order.requested_delivery_date ? new Date(order.requested_delivery_date).toLocaleDateString('pt-PT') : '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <Link :href="route('orders.show', order.id)" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Ver →</Link>
                                </td>
                            </tr>
                            <tr v-if="!orders?.length">
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                    Sem pedidos ainda.
                                    <Link :href="route('orders.create')" class="text-blue-600 hover:underline ml-1">Criar primeiro pedido</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
