<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    order: Object,
    auth: Object,
});

const statusLabels = {
    draft: 'Rascunho',
    awaiting_pickup: 'Aguardar Recolha',
    in_transit: 'Em Trânsito',
    at_lab: 'No Laboratório',
    in_production: 'Em Produção',
    quality_check: 'Controlo de Qualidade',
    awaiting_delivery: 'Aguardar Entrega',
    delivered: 'Entregue',
    cancelled: 'Cancelado',
};

const statusColors = {
    draft: 'bg-gray-100 text-gray-700',
    awaiting_pickup: 'bg-yellow-100 text-yellow-800',
    in_transit: 'bg-blue-100 text-blue-800',
    at_lab: 'bg-indigo-100 text-indigo-800',
    in_production: 'bg-purple-100 text-purple-800',
    quality_check: 'bg-orange-100 text-orange-800',
    awaiting_delivery: 'bg-teal-100 text-teal-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const allStatuses = [
    'awaiting_pickup',
    'in_transit',
    'at_lab',
    'in_production',
    'quality_check',
    'awaiting_delivery',
    'delivered',
];

const currentIdx = computed(() => allStatuses.indexOf(props.order.status));

const toothLabel = (id) => `Dente ${id}`;

const isLabOrAdmin = computed(() =>
    ['admin', 'lab'].includes(props.auth?.user?.role)
);

const selectedStatus = ref(props.order.status);

function updateStatus() {
    router.patch(route('orders.status', props.order.id), {
        status: selectedStatus.value,
    }, { preserveScroll: true });
}

const impressionLabels = {
    physical: 'Moldagem Física',
    digital: 'Scan Digital (STL)',
    mixed: 'Misto',
};
</script>

<template>
    <Head :title="`Pedido ${order.reference}`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('orders.index')" class="text-gray-500 hover:text-gray-800 text-sm">← Todos os Pedidos</Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ order.reference }}</h2>
                <span :class="statusColors[order.status]" class="px-3 py-1 rounded-full text-sm font-semibold">
                    {{ statusLabels[order.status] }}
                </span>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Progress Timeline -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Progresso do Pedido</h3>
                    <div class="flex items-center justify-between relative">
                        <div class="absolute left-0 right-0 top-4 h-1 bg-gray-200 z-0">
                            <div
                                class="h-full bg-blue-500 transition-all duration-500"
                                :style="`width: ${currentIdx < 0 ? 0 : ((currentIdx / (allStatuses.length - 1)) * 100)}%`"
                            ></div>
                        </div>
                        <div v-for="(st, idx) in allStatuses" :key="st" class="z-10 flex flex-col items-center gap-1" style="width: 14.28%">
                            <div
                                :class="idx <= currentIdx ? 'bg-blue-600 border-blue-600' : 'bg-white border-gray-300'"
                                class="w-8 h-8 rounded-full border-2 flex items-center justify-center"
                            >
                                <svg v-if="idx < currentIdx" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <div v-else-if="idx === currentIdx" class="w-3 h-3 bg-white rounded-full"></div>
                            </div>
                            <span class="text-xs text-center text-gray-500 leading-tight">{{ statusLabels[st] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Order Details -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informações do Pedido</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Referência</dt>
                                <dd class="text-sm font-semibold text-gray-900 font-mono">{{ order.reference }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Ref. Paciente</dt>
                                <dd class="text-sm text-gray-900">{{ order.patient_ref }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Clínica</dt>
                                <dd class="text-sm text-gray-900">{{ order.clinic?.name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Médico</dt>
                                <dd class="text-sm text-gray-900">{{ order.clinic?.doctor_name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Tipo de Impressão</dt>
                                <dd class="text-sm text-gray-900">{{ impressionLabels[order.impression_type] ?? order.impression_type }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Data de Pedido</dt>
                                <dd class="text-sm text-gray-900">{{ new Date(order.created_at).toLocaleDateString('pt-PT') }}</dd>
                            </div>
                            <div v-if="order.requested_delivery_date" class="flex justify-between">
                                <dt class="text-sm text-gray-500">Data Pretendida</dt>
                                <dd class="text-sm text-gray-900">{{ new Date(order.requested_delivery_date).toLocaleDateString('pt-PT') }}</dd>
                            </div>
                            <div v-if="order.estimated_price" class="flex justify-between border-t pt-3 mt-3">
                                <dt class="text-sm font-semibold text-gray-700">Valor Estimado</dt>
                                <dd class="text-sm font-bold text-gray-900">€{{ Number(order.estimated_price).toFixed(2) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Work Items -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Trabalhos</h3>
                        <div v-if="order.items && order.items.length" class="space-y-3">
                            <div v-for="item in order.items" :key="item.id"
                                class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <span class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 text-blue-800 rounded-full text-sm font-bold shrink-0">
                                    {{ item.tooth_fdi }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ item.work_category?.name }}</p>
                                    <p v-if="item.work_category?.parent" class="text-xs text-gray-500">{{ item.work_category.parent?.parent?.name }} › {{ item.work_category.parent?.name }}</p>
                                    <div class="flex gap-2 mt-1 flex-wrap">
                                        <span v-if="item.vita_shade" class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">{{ item.vita_shade }}</span>
                                        <span v-if="item.finishing_stage" class="text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded">{{ item.finishing_stage }}</span>
                                    </div>
                                </div>
                                <span v-if="item.unit_price" class="ml-auto text-sm font-semibold text-gray-700 shrink-0">
                                    €{{ Number(item.unit_price).toFixed(2) }}
                                </span>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-400">Nenhum trabalho registado.</p>
                    </div>
                </div>

                <!-- Barcode -->
                <div v-if="order.barcode" class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Etiqueta de Rastreio</h3>
                    <div class="flex items-center gap-4">
                        <div class="font-mono text-2xl tracking-widest text-gray-800 bg-gray-50 px-4 py-2 rounded border">
                            {{ order.barcode }}
                        </div>
                        <span class="text-xs text-gray-400">Código de rastreio interno</span>
                    </div>
                </div>

                <!-- Notes -->
                <div v-if="order.clinical_notes || order.internal_notes" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-if="order.clinical_notes" class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Notas Clínicas</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ order.clinical_notes }}</p>
                    </div>
                    <div v-if="order.internal_notes && isLabOrAdmin" class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Notas Internas (Lab)</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ order.internal_notes }}</p>
                    </div>
                </div>

                <!-- Status Update (Lab/Admin only) -->
                <div v-if="isLabOrAdmin" class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Atualizar Estado</h3>
                    <div class="flex items-center gap-4">
                        <select v-model="selectedStatus"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option v-for="(label, value) in statusLabels" :key="value" :value="value">{{ label }}</option>
                        </select>
                        <button @click="updateStatus"
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            Guardar Estado
                        </button>
                    </div>
                </div>

                <!-- PDF Download -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Documentos</h3>
                    <a :href="route('orders.pdf', order.id)"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.293 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Guia de Trabalho (PDF)
                    </a>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
