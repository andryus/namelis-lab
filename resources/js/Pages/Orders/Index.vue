<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    orders: Object,   // paginated
    auth: Object,
    filters: Object,
});

const statusLabels = {
    draft: 'Rascunho',
    awaiting_pickup: 'Aguardar Recolha',
    in_transit: 'Em Trânsito',
    at_lab: 'No Lab',
    in_production: 'Em Produção',
    quality_check: 'QC',
    awaiting_delivery: 'Aguardar Entrega',
    delivered: 'Entregue',
    cancelled: 'Cancelado',
};

const statusColors = {
    draft: 'bg-gray-100 text-gray-600',
    awaiting_pickup: 'bg-yellow-100 text-yellow-800',
    in_transit: 'bg-blue-100 text-blue-800',
    at_lab: 'bg-indigo-100 text-indigo-800',
    in_production: 'bg-purple-100 text-purple-800',
    quality_check: 'bg-orange-100 text-orange-800',
    awaiting_delivery: 'bg-teal-100 text-teal-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const search = ref(props.filters?.search ?? '');
const statusFilter = ref(props.filters?.status ?? '');

function applyFilters() {
    router.get(route('orders.index'), {
        search: search.value,
        status: statusFilter.value,
    }, { preserveState: true, replace: true });
}
</script>

<template>
    <Head title="Pedidos" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Todos os Pedidos</h2>
                <Link v-if="auth?.user?.role === 'dentist'"
                    :href="route('orders.create')"
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                    + Novo Pedido
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow p-4 flex flex-wrap gap-3 items-center">
                    <input v-model="search" @input="applyFilters" type="text"
                        placeholder="Pesquisar referência, paciente..."
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 w-64"/>
                    <select v-model="statusFilter" @change="applyFilters"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos os estados</option>
                        <option v-for="(label, value) in statusLabels" :key="value" :value="value">{{ label }}</option>
                    </select>
                    <span class="text-xs text-gray-400 ml-auto">{{ orders?.total ?? 0 }} pedidos</span>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Referência</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Paciente</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Clínica</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dentes</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="!orders?.data?.length">
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                                    Nenhum pedido encontrado.
                                </td>
                            </tr>
                            <tr v-for="order in orders?.data" :key="order.id" class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-sm font-semibold text-gray-900">{{ order.reference }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ order.patient_ref }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ order.clinic?.name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="item in (order.items ?? []).slice(0,4)" :key="item.id"
                                            class="inline-block w-8 h-8 bg-blue-100 text-blue-800 rounded-full text-xs font-bold flex items-center justify-center">
                                            {{ item.tooth_fdi }}
                                        </span>
                                        <span v-if="(order.items ?? []).length > 4"
                                            class="text-xs text-gray-400 self-center">+{{ order.items.length - 4 }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span :class="statusColors[order.status]" class="px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ statusLabels[order.status] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ new Date(order.created_at).toLocaleDateString('pt-PT') }}
                                </td>
                                <td class="px-4 py-3">
                                    <Link :href="route('orders.show', order.id)"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Ver →
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="orders?.last_page > 1" class="flex gap-2 justify-center">
                    <Link v-for="page in orders.last_page" :key="page"
                        :href="route('orders.index', { page })"
                        :class="page === orders.current_page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                        class="px-3 py-1 rounded border text-sm">
                        {{ page }}
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
