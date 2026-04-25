<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import OdontogramChart from '@/Components/Odontogram/OdontogramChart.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    patient_ref: '',
    impression_type: 'physical',
    stl_external_url: '',
    requested_delivery_date: '',
    clinical_notes: '',
    teeth: [],        // selected FDI numbers
    items: [],        // built items per tooth
});

// ─── Odontogram state ────────────────────────────────────────────────────────
const selectedTeeth = ref([]);

// When teeth selection changes, sync items array
watch(selectedTeeth, (teeth) => {
    // Add new teeth
    teeth.forEach(fdi => {
        if (!form.items.find(i => i.tooth_fdi === fdi)) {
            form.items.push({ tooth_fdi: fdi, category_l1: null, category_l2: null, category_l3: null, vita_shade: '', finishing_stage: 'direct', notes: '' });
        }
    });
    // Remove deselected teeth
    form.items = form.items.filter(i => teeth.includes(i.tooth_fdi));
    form.teeth = teeth;
});

// ─── Category tree helpers ────────────────────────────────────────────────────
const level1 = computed(() => props.categories?.filter(c => c.level === 1) ?? []);
function level2For(l1Id) { return props.categories?.filter(c => c.level === 2 && c.parent_id === l1Id) ?? []; }
function level3For(l2Id) { return props.categories?.filter(c => c.level === 3 && c.parent_id === l2Id) ?? []; }

function onL1Change(item) { item.category_l2 = null; item.category_l3 = null; }
function onL2Change(item) { item.category_l3 = null; }

// ─── Vita shades ─────────────────────────────────────────────────────────────
const vitaClassic = ['A1','A2','A3','A3.5','A4','B1','B2','B3','B4','C1','C2','C3','C4','D2','D3','D4'];

const isValid = computed(() =>
    form.patient_ref.trim() &&
    selectedTeeth.value.length > 0 &&
    form.items.every(i => i.category_l3)
);

function submit() {
    form.post(route('orders.store'));
}
</script>

<template>
    <Head title="Novo Pedido" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <a :href="route('dashboard')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    ← Voltar
                </a>
                <h2 class="text-xl font-semibold text-gray-800">Novo Pedido</h2>
            </div>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
            <form @submit.prevent="submit" class="space-y-8">

                <!-- Step 1: Patient + Impression type -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-5 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                        Dados Gerais
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Referência do Paciente <span class="text-red-500">*</span></label>
                            <input v-model="form.patient_ref" type="text" placeholder="Ex: PAC-001 ou iniciais"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-400': form.errors.patient_ref }" />
                            <p v-if="form.errors.patient_ref" class="mt-1 text-xs text-red-500">{{ form.errors.patient_ref }}</p>
                            <p class="mt-1 text-xs text-slate-400">Não use nomes completos em documentos de transporte (RGPD)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Data de Entrega Pretendida</label>
                            <input v-model="form.requested_delivery_date" type="date"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                    </div>

                    <!-- Impression type -->
                    <div class="mt-5">
                        <label class="block text-sm font-medium text-slate-700 mb-3">Tipo de Impressão <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label :class="['flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all', form.impression_type === 'physical' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300']">
                                <input v-model="form.impression_type" type="radio" value="physical" class="mt-0.5 accent-blue-600" />
                                <div>
                                    <p class="font-semibold text-sm text-slate-800">Impressão Física</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Estafeta vai recolher à clínica</p>
                                </div>
                            </label>
                            <label :class="['flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all', form.impression_type === 'digital' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300']">
                                <input v-model="form.impression_type" type="radio" value="digital" class="mt-0.5 accent-blue-600" />
                                <div>
                                    <p class="font-semibold text-sm text-slate-800">Impressão Digital</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Ficheiro STL / URL externa</p>
                                </div>
                            </label>
                        </div>
                        <div v-if="form.impression_type === 'digital'" class="mt-3">
                            <input v-model="form.stl_external_url" type="url" placeholder="URL do ficheiro STL externo (opcional)"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                    </div>
                </div>

                <!-- Step 2: Odontogram -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-5 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">2</span>
                        Odontograma — Selecione os Dentes
                        <span class="text-xs font-normal text-slate-500 ml-1">(numeração FDI)</span>
                    </h3>
                    <OdontogramChart v-model="selectedTeeth" />
                    <p v-if="form.errors.teeth" class="mt-2 text-xs text-red-500">{{ form.errors.teeth }}</p>
                </div>

                <!-- Step 3: Work per tooth -->
                <div v-if="form.items.length > 0" class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-5 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">3</span>
                        Trabalho por Dente
                    </h3>

                    <div class="space-y-5">
                        <div v-for="item in form.items" :key="item.tooth_fdi"
                            class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="w-9 h-9 rounded-lg bg-blue-600 text-white font-bold text-sm flex items-center justify-center">{{ item.tooth_fdi }}</span>
                                <span class="text-sm font-semibold text-slate-700">Dente {{ item.tooth_fdi }}</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <!-- L1 -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Categoria <span class="text-red-500">*</span></label>
                                    <select v-model="item.category_l1" @change="onL1Change(item)"
                                        class="w-full border border-slate-300 bg-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                        <option :value="null">— Selecione —</option>
                                        <option v-for="c in level1" :key="c.id" :value="c.id">{{ c.name }}</option>
                                    </select>
                                </div>
                                <!-- L2 -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Tipo <span class="text-red-500">*</span></label>
                                    <select v-model="item.category_l2" @change="onL2Change(item)" :disabled="!item.category_l1"
                                        class="w-full border border-slate-300 bg-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                                        <option :value="null">— Selecione —</option>
                                        <option v-for="c in level2For(item.category_l1)" :key="c.id" :value="c.id">{{ c.name }}</option>
                                    </select>
                                </div>
                                <!-- L3 -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Material <span class="text-red-500">*</span></label>
                                    <select v-model="item.category_l3" :disabled="!item.category_l2"
                                        class="w-full border border-slate-300 bg-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                                        <option :value="null">— Selecione —</option>
                                        <option v-for="c in level3For(item.category_l2)" :key="c.id" :value="c.id">{{ c.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                <!-- Vita shade -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Cor VITA Classic</label>
                                    <select v-model="item.vita_shade"
                                        class="w-full border border-slate-300 bg-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">— Sem cor —</option>
                                        <option v-for="s in vitaClassic" :key="s" :value="s">{{ s }}</option>
                                    </select>
                                </div>
                                <!-- Finishing stage -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Etapa de Finalização</label>
                                    <select v-model="item.finishing_stage"
                                        class="w-full border border-slate-300 bg-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="direct">Direta</option>
                                        <option value="biscuit">Biscoito</option>
                                        <option value="metallic">Metálico</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Notes -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">4</span>
                        Notas Clínicas
                    </h3>
                    <textarea v-model="form.clinical_notes" rows="3" placeholder="Observações, instruções especiais, referências de cor adicionais..."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-between">
                    <a :href="route('dashboard')" class="text-sm text-slate-500 hover:text-slate-700">Cancelar</a>
                    <button type="submit"
                        :disabled="!isValid || form.processing"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 text-white rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
                        <span v-if="form.processing">A processar...</span>
                        <span v-else>Criar Pedido</span>
                    </button>
                </div>

            </form>
        </div>
    </AuthenticatedLayout>
</template>
