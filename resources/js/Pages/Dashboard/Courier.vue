<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    pickups: Array,
    deliveries: Array,
    auth: Object,
});

function markTransit(orderId) {
    router.patch(route('orders.status', orderId), { status: 'in_transit' }, { preserveScroll: true });
}

function markDelivered(orderId) {
    router.patch(route('orders.status', orderId), { status: 'delivered' }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Estafeta — Rotas" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Painel do Estafeta</h2>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Stats Row -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl shadow p-5 text-center">
                        <p class="text-3xl font-bold text-yellow-600">{{ pickups?.length ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Recolhas Pendentes</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5 text-center">
                        <p class="text-3xl font-bold text-teal-600">{{ deliveries?.length ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Entregas Pendentes</p>
                    </div>
                </div>

                <!-- Pickups -->
                <div class="bg-white rounded-xl shadow">
                    <div class="p-5 border-b flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">Recolhas — Clínicas → Lab</h3>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-semibold">{{ pickups?.length ?? 0 }} pendentes</span>
                    </div>
                    <div v-if="!pickups?.length" class="p-6 text-center text-gray-400 text-sm">
                        Sem recolhas pendentes. 
                    </div>
                    <ul v-else class="divide-y divide-gray-100">
                        <li v-for="order in pickups" :key="order.id" class="p-4 flex items-center gap-4 hover:bg-gray-50 transition">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12h18M3 12l4-4m-4 4l4 4M21 12l-4-4m4 4l-4 4"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 font-mono">{{ order.reference }}</p>
                                <p class="text-xs text-gray-500">{{ order.clinic?.name }} · {{ order.clinic?.address }}</p>
                                <p class="text-xs text-gray-400">{{ order.items?.length ?? 0 }} trabalho(s) · Paciente: {{ order.patient_ref }}</p>
                            </div>
                            <button @click="markTransit(order.id)"
                                class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition shrink-0">
                                Em Trânsito
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Deliveries -->
                <div class="bg-white rounded-xl shadow">
                    <div class="p-5 border-b flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">Entregas — Lab → Clínicas</h3>
                        <span class="text-xs bg-teal-100 text-teal-800 px-2 py-1 rounded-full font-semibold">{{ deliveries?.length ?? 0 }} pendentes</span>
                    </div>
                    <div v-if="!deliveries?.length" class="p-6 text-center text-gray-400 text-sm">
                        Sem entregas pendentes.
                    </div>
                    <ul v-else class="divide-y divide-gray-100">
                        <li v-for="order in deliveries" :key="order.id" class="p-4 flex items-center gap-4 hover:bg-gray-50 transition">
                            <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8 5-8-5V5l8 5 8-5v2zM4 19h16"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 font-mono">{{ order.reference }}</p>
                                <p class="text-xs text-gray-500">{{ order.clinic?.name }} · {{ order.clinic?.address }}</p>
                                <p class="text-xs text-gray-400">{{ order.items?.length ?? 0 }} trabalho(s) · Paciente: {{ order.patient_ref }}</p>
                                <p v-if="order.barcode" class="text-xs font-mono mt-1 text-gray-600 bg-gray-100 px-2 py-0.5 rounded inline-block">
                                    {{ order.barcode }}
                                </p>
                            </div>
                            <button @click="markDelivered(order.id)"
                                class="px-3 py-1.5 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition shrink-0">
                                Entregue ✓
                            </button>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
