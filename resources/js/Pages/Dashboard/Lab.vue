<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    queue: Array,
    stats: Object,
});

const statusLabel = {
    at_lab: 'No Laboratório',
    in_production: 'Em Produção',
    quality_check: 'Controlo Qualidade',
};
const statusColor = {
    at_lab: 'bg-indigo-100 text-indigo-800',
    in_production: 'bg-purple-100 text-purple-800',
    quality_check: 'bg-orange-100 text-orange-800',
};
</script>

<template>
    <Head title="Dashboard Laboratório" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Fila de Produção</h2>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-indigo-50 rounded-xl p-5 border border-indigo-100">
                    <p class="text-sm text-indigo-600 font-medium">No Laboratório</p>
                    <p class="text-3xl font-bold text-indigo-700 mt-1">{{ stats.at_lab }}</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-5 border border-purple-100">
                    <p class="text-sm text-purple-600 font-medium">Em Produção</p>
                    <p class="text-3xl font-bold text-purple-700 mt-1">{{ stats.in_production }}</p>
                </div>
                <div class="bg-orange-50 rounded-xl p-5 border border-orange-100">
                    <p class="text-sm text-orange-600 font-medium">Controlo Qualidade</p>
                    <p class="text-3xl font-bold text-orange-700 mt-1">{{ stats.quality_check }}</p>
                </div>
            </div>

            <!-- Work queue -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-800">Trabalhos Ativos</h3>
                </div>
                <div class="divide-y divide-slate-50">
                    <div v-for="order in queue" :key="order.id" class="px-6 py-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="font-mono font-bold text-blue-700">{{ order.reference }}</span>
                                    <span :class="['inline-flex px-2 py-0.5 rounded-full text-xs font-semibold', statusColor[order.status]]">
                                        {{ statusLabel[order.status] }}
                                    </span>
                                    <span v-if="order.impression_type === 'digital'" class="inline-flex px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">Digital</span>
                                    <span v-else class="inline-flex px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600">Físico</span>
                                </div>
                                <p class="text-sm text-slate-600">{{ order.clinic?.name }} — <span class="font-mono text-xs text-slate-400">{{ order.patient_ref }}</span></p>
                                <div v-if="order.items?.length" class="flex flex-wrap gap-1.5 mt-2">
                                    <span v-for="item in order.items" :key="item.id" class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-xs text-slate-600">
                                        <span class="font-bold text-slate-700">{{ item.tooth_fdi }}</span>
                                        {{ item.work_category?.name }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right text-sm text-slate-400 whitespace-nowrap ml-4">
                                <p>Entrega:</p>
                                <p class="font-medium text-slate-600">{{ order.requested_delivery_date ? new Date(order.requested_delivery_date).toLocaleDateString('pt-PT') : '—' }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="!queue?.length" class="px-6 py-10 text-center text-slate-400">
                        Sem trabalhos em fila de momento.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
