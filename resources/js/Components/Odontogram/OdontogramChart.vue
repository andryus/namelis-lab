<template>
  <div class="odontogram-wrapper select-none">
    <!-- Header -->
    <div class="flex items-center justify-between mb-2 px-1">
      <div class="flex items-center gap-2">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Odontograma — Numeração FDI/ISO 3950</span>
        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">32 dentes</span>
      </div>
      <button
        v-if="selectedTeeth.length > 0"
        @click="clearSelection"
        class="text-xs text-slate-400 hover:text-red-500 transition-colors"
      >
        Limpar seleção
      </button>
    </div>

    <!-- SVG Odontogram -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-md">
      <svg
        viewBox="0 0 880 380"
        class="w-full h-auto odontogram-svg"
        xmlns="http://www.w3.org/2000/svg"
      >
        <defs>
          <!-- Enamel gradient — normal tooth -->
          <linearGradient id="enamel-normal" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%"   stop-color="#ffffff"/>
            <stop offset="40%"  stop-color="#f0f4f8"/>
            <stop offset="100%" stop-color="#dde3ea"/>
          </linearGradient>
          <!-- Enamel gradient — hovered -->
          <linearGradient id="enamel-hover" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%"   stop-color="#eff6ff"/>
            <stop offset="50%"  stop-color="#dbeafe"/>
            <stop offset="100%" stop-color="#bfdbfe"/>
          </linearGradient>
          <!-- Enamel gradient — selected -->
          <linearGradient id="enamel-selected" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%"   stop-color="#3b82f6"/>
            <stop offset="50%"  stop-color="#2563eb"/>
            <stop offset="100%" stop-color="#1d4ed8"/>
          </linearGradient>
          <!-- Gum tissue gradient -->
          <linearGradient id="gum-upper" x1="0%" y1="100%" x2="0%" y2="0%">
            <stop offset="0%"   stop-color="#fda4af" stop-opacity="0.7"/>
            <stop offset="100%" stop-color="#fb7185" stop-opacity="0.3"/>
          </linearGradient>
          <linearGradient id="gum-lower" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%"   stop-color="#fda4af" stop-opacity="0.7"/>
            <stop offset="100%" stop-color="#fb7185" stop-opacity="0.3"/>
          </linearGradient>
          <!-- Drop shadow filter -->
          <filter id="tooth-shadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="1" stdDeviation="1.5" flood-color="#94a3b8" flood-opacity="0.3"/>
          </filter>
          <filter id="tooth-shadow-selected" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#2563eb" flood-opacity="0.5"/>
          </filter>
        </defs>

        <!-- Background -->
        <rect width="880" height="380" fill="#f1f5f9" rx="16"/>

        <!-- Subtle grid -->
        <rect x="12" y="12" width="856" height="356" fill="white" rx="12" opacity="0.6"/>

        <!-- Arch background — upper (gum tissue pink) -->
        <path
          d="M 170 168 C 210 130, 300 105, 440 98 C 580 105, 670 130, 710 168 L 720 172 C 680 138, 588 110, 440 103 C 292 110, 200 138, 160 172 Z"
          fill="url(#gum-upper)" opacity="0.8"
        />
        <!-- Arch background — lower -->
        <path
          d="M 170 212 C 210 250, 300 275, 440 282 C 580 275, 670 250, 710 212 L 720 208 C 680 248, 588 280, 440 287 C 292 280, 200 248, 160 208 Z"
          fill="url(#gum-lower)" opacity="0.8"
        />

        <!-- Midline separator -->
        <line x1="440" y1="32" x2="440" y2="348" stroke="#cbd5e1" stroke-width="1.5" stroke-dasharray="5,4" opacity="0.7"/>

        <!-- Quadrant labels -->
        <text x="300" y="26" text-anchor="middle" font-size="9.5" fill="#94a3b8" font-family="system-ui" font-weight="700" letter-spacing="0.5">Q1 — SUPERIOR DIREITO</text>
        <text x="580" y="26" text-anchor="middle" font-size="9.5" fill="#94a3b8" font-family="system-ui" font-weight="700" letter-spacing="0.5">Q2 — SUPERIOR ESQUERDO</text>
        <text x="300" y="370" text-anchor="middle" font-size="9.5" fill="#94a3b8" font-family="system-ui" font-weight="700" letter-spacing="0.5">Q4 — INFERIOR DIREITO</text>
        <text x="580" y="370" text-anchor="middle" font-size="9.5" fill="#94a3b8" font-family="system-ui" font-weight="700" letter-spacing="0.5">Q3 — INFERIOR ESQUERDO</text>

        <!-- "D" / "E" lateral labels -->
        <text x="148" y="192" text-anchor="middle" font-size="10" fill="#64748b" font-family="system-ui" font-weight="700">D</text>
        <text x="732" y="192" text-anchor="middle" font-size="10" fill="#64748b" font-family="system-ui" font-weight="700">E</text>

        <!-- ========== UPPER TEETH ========== -->
        <g
          v-for="tooth in upperTeeth"
          :key="'u-' + tooth.id"
          class="tooth-group cursor-pointer"
          @click="!disabled && toggleTooth(tooth.id)"
          @mouseenter="hoveredTooth = tooth.id"
          @mouseleave="hoveredTooth = null"
          :filter="isSelected(tooth.id) ? 'url(#tooth-shadow-selected)' : 'url(#tooth-shadow)'"
        >
          <!-- Root shape -->
          <path
            :d="rootPath(tooth, 'upper')"
            :fill="isSelected(tooth.id) ? '#bfdbfe' : '#e0e7ef'"
            :stroke="isSelected(tooth.id) ? '#3b82f6' : '#b8c4d0'"
            stroke-width="0.8"
            opacity="0.7"
          />

          <!-- Crown shape — anatomical path -->
          <path
            :d="crownPath(tooth)"
            :fill="toothGradient(tooth.id)"
            :stroke="toothStroke(tooth.id)"
            :stroke-width="isSelected(tooth.id) ? 1.8 : 1"
            class="tooth-crown"
          />

          <!-- Occlusal surface detail -->
          <path
            :d="occlusalDetail(tooth, 'upper')"
            :fill="isSelected(tooth.id) ? 'rgba(255,255,255,0.18)' : 'rgba(180,195,210,0.35)'"
            :stroke="isSelected(tooth.id) ? 'rgba(255,255,255,0.3)' : 'rgba(150,170,190,0.4)'"
            stroke-width="0.7"
          />

          <!-- Tooth number — above -->
          <text
            :x="tooth.cx"
            :y="tooth.y - 7"
            text-anchor="middle"
            :font-size="isSelected(tooth.id) ? '10.5' : '9.5'"
            :fill="isSelected(tooth.id) ? '#1d4ed8' : hoveredTooth === tooth.id ? '#3b82f6' : '#64748b'"
            :font-weight="isSelected(tooth.id) ? '800' : '600'"
            font-family="system-ui, sans-serif"
          >{{ tooth.id }}</text>
        </g>

        <!-- ========== LOWER TEETH ========== -->
        <g
          v-for="tooth in lowerTeeth"
          :key="'l-' + tooth.id"
          class="tooth-group cursor-pointer"
          @click="!disabled && toggleTooth(tooth.id)"
          @mouseenter="hoveredTooth = tooth.id"
          @mouseleave="hoveredTooth = null"
          :filter="isSelected(tooth.id) ? 'url(#tooth-shadow-selected)' : 'url(#tooth-shadow)'"
        >
          <!-- Root shape -->
          <path
            :d="rootPath(tooth, 'lower')"
            :fill="isSelected(tooth.id) ? '#bfdbfe' : '#e0e7ef'"
            :stroke="isSelected(tooth.id) ? '#3b82f6' : '#b8c4d0'"
            stroke-width="0.8"
            opacity="0.7"
          />

          <!-- Crown shape -->
          <path
            :d="crownPath(tooth)"
            :fill="toothGradient(tooth.id)"
            :stroke="toothStroke(tooth.id)"
            :stroke-width="isSelected(tooth.id) ? 1.8 : 1"
            class="tooth-crown"
          />

          <!-- Occlusal surface detail -->
          <path
            :d="occlusalDetail(tooth, 'lower')"
            :fill="isSelected(tooth.id) ? 'rgba(255,255,255,0.18)' : 'rgba(180,195,210,0.35)'"
            :stroke="isSelected(tooth.id) ? 'rgba(255,255,255,0.3)' : 'rgba(150,170,190,0.4)'"
            stroke-width="0.7"
          />

          <!-- Tooth number — below -->
          <text
            :x="tooth.cx"
            :y="tooth.y + tooth.h + 13"
            text-anchor="middle"
            :font-size="isSelected(tooth.id) ? '10.5' : '9.5'"
            :fill="isSelected(tooth.id) ? '#1d4ed8' : hoveredTooth === tooth.id ? '#3b82f6' : '#64748b'"
            :font-weight="isSelected(tooth.id) ? '800' : '600'"
            font-family="system-ui, sans-serif"
          >{{ tooth.id }}</text>
        </g>
      </svg>
    </div>

    <!-- Arch labels below SVG -->
    <div class="mt-1 px-1 flex justify-between">
      <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Maxilar Superior</span>
      <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mandíbula Inferior</span>
    </div>

    <!-- Selected teeth summary -->
    <transition name="fade">
      <div v-if="selectedTeeth.length > 0" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
        <div class="flex items-center justify-between flex-wrap gap-2">
          <span class="text-sm font-semibold text-blue-800">
            {{ selectedTeeth.length }} dente{{ selectedTeeth.length > 1 ? 's' : '' }} selecionado{{ selectedTeeth.length > 1 ? 's' : '' }}
          </span>
          <div class="flex flex-wrap gap-1.5">
            <span
              v-for="id in sortedSelectedTeeth"
              :key="id"
              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-600 text-white cursor-pointer hover:bg-red-500 transition-colors"
              title="Clique para remover"
              @click="toggleTooth(id)"
            >{{ id }} ×</span>
          </div>
        </div>
        <!-- Bridge indicator -->
        <div v-if="selectedTeeth.length >= 2" class="mt-2 flex items-center gap-2">
          <input id="is-bridge" v-model="isBridge" type="checkbox"
            class="rounded border-blue-300 text-blue-600"
            @change="$emit('bridge-change', isBridge)"/>
          <label for="is-bridge" class="text-sm text-blue-700">
            Trabalho em ponte
            <span v-if="isBridge" class="text-xs text-blue-500">
              — pilares: {{ bridgePillars.join(', ') }}, pôntico: {{ bridgePontic.join(', ') || '(nenhum)' }}
            </span>
          </label>
        </div>
      </div>
    </transition>

    <!-- Legend -->
    <div class="mt-2 flex items-center gap-5 px-1">
      <div class="flex items-center gap-1.5">
        <div class="w-5 h-5 rounded-md shadow-sm border border-slate-300" style="background: linear-gradient(135deg,#fff,#dde3ea)"></div>
        <span class="text-xs text-slate-500">Normal</span>
      </div>
      <div class="flex items-center gap-1.5">
        <div class="w-5 h-5 rounded-md shadow-sm border border-blue-300" style="background: linear-gradient(135deg,#eff6ff,#bfdbfe)"></div>
        <span class="text-xs text-slate-500">Hover</span>
      </div>
      <div class="flex items-center gap-1.5">
        <div class="w-5 h-5 rounded-md shadow-sm border border-blue-700" style="background: linear-gradient(135deg,#3b82f6,#1d4ed8)"></div>
        <span class="text-xs text-slate-500">Selecionado</span>
      </div>
      <div class="flex items-center gap-1.5 ml-2">
        <div class="w-5 h-3 rounded-sm" style="background: #e0e7ef; border: 1px solid #b8c4d0"></div>
        <span class="text-xs text-slate-400">Raiz</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// ─── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  disabled:   { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue', 'bridge-change'])

// ─── State ────────────────────────────────────────────────────────────────────
const hoveredTooth = ref(null)
const isBridge = ref(false)

// ─── Computed ─────────────────────────────────────────────────────────────────
const selectedTeeth = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
})
const sortedSelectedTeeth = computed(() => [...selectedTeeth.value].sort((a, b) => a - b))

const bridgePillars = computed(() => {
  if (selectedTeeth.value.length < 2) return []
  const sorted = [...selectedTeeth.value].sort((a, b) => a - b)
  return [sorted[0], sorted[sorted.length - 1]]
})
const bridgePontic = computed(() => {
  if (!isBridge.value || bridgePillars.value.length < 2) return []
  const [min, max] = bridgePillars.value
  const result = []
  for (let i = min + 1; i < max; i++) {
    if (!selectedTeeth.value.includes(i)) result.push(i)
  }
  return result
})

// ─── Tooth Definitions ────────────────────────────────────────────────────────
// Each tooth: { id, x, y, w, h, cx (center x), type, quadrant }
// SVG viewBox: 0 0 880 380
// Upper arch: y between 65-95 (molars higher, incisors lower)
// Lower arch: y between 225-260 (molars lower, incisors higher)
// Gap between arches: ~40px centered at y=190

const upperTeeth = [
  // Q1 — Upper Right (FDI 18→11, left side of SVG)
  { id: 18, x: 162, y: 60,  w: 38, h: 46, cx: 181, type: 'molar',    quadrant: 1 },
  { id: 17, x: 203, y: 63,  w: 38, h: 46, cx: 222, type: 'molar',    quadrant: 1 },
  { id: 16, x: 244, y: 67,  w: 38, h: 46, cx: 263, type: 'molar',    quadrant: 1 },
  { id: 15, x: 285, y: 72,  w: 30, h: 40, cx: 300, type: 'premolar', quadrant: 1 },
  { id: 14, x: 318, y: 76,  w: 30, h: 40, cx: 333, type: 'premolar', quadrant: 1 },
  { id: 13, x: 351, y: 80,  w: 28, h: 44, cx: 365, type: 'canine',   quadrant: 1 },
  { id: 12, x: 382, y: 84,  w: 25, h: 38, cx: 394, type: 'incisor',  quadrant: 1 },
  { id: 11, x: 410, y: 87,  w: 27, h: 40, cx: 423, type: 'incisor',  quadrant: 1 },
  // Q2 — Upper Left (FDI 21→28, right side of SVG)
  { id: 21, x: 443, y: 87,  w: 27, h: 40, cx: 456, type: 'incisor',  quadrant: 2 },
  { id: 22, x: 473, y: 84,  w: 25, h: 38, cx: 485, type: 'incisor',  quadrant: 2 },
  { id: 23, x: 501, y: 80,  w: 28, h: 44, cx: 515, type: 'canine',   quadrant: 2 },
  { id: 24, x: 532, y: 76,  w: 30, h: 40, cx: 547, type: 'premolar', quadrant: 2 },
  { id: 25, x: 565, y: 72,  w: 30, h: 40, cx: 580, type: 'premolar', quadrant: 2 },
  { id: 26, x: 598, y: 67,  w: 38, h: 46, cx: 617, type: 'molar',    quadrant: 2 },
  { id: 27, x: 639, y: 63,  w: 38, h: 46, cx: 658, type: 'molar',    quadrant: 2 },
  { id: 28, x: 680, y: 60,  w: 38, h: 46, cx: 699, type: 'molar',    quadrant: 2 },
]

const lowerTeeth = [
  // Q4 — Lower Right (FDI 48→41, left side of SVG)
  { id: 48, x: 162, y: 270, w: 38, h: 46, cx: 181, type: 'molar',    quadrant: 4 },
  { id: 47, x: 203, y: 267, w: 38, h: 46, cx: 222, type: 'molar',    quadrant: 4 },
  { id: 46, x: 244, y: 263, w: 38, h: 46, cx: 263, type: 'molar',    quadrant: 4 },
  { id: 45, x: 285, y: 259, w: 30, h: 40, cx: 300, type: 'premolar', quadrant: 4 },
  { id: 44, x: 318, y: 255, w: 30, h: 40, cx: 333, type: 'premolar', quadrant: 4 },
  { id: 43, x: 351, y: 251, w: 28, h: 44, cx: 365, type: 'canine',   quadrant: 4 },
  { id: 42, x: 382, y: 247, w: 25, h: 38, cx: 394, type: 'incisor',  quadrant: 4 },
  { id: 41, x: 410, y: 244, w: 27, h: 40, cx: 423, type: 'incisor',  quadrant: 4 },
  // Q3 — Lower Left (FDI 31→38, right side of SVG)
  { id: 31, x: 443, y: 244, w: 27, h: 40, cx: 456, type: 'incisor',  quadrant: 3 },
  { id: 32, x: 473, y: 247, w: 25, h: 38, cx: 485, type: 'incisor',  quadrant: 3 },
  { id: 33, x: 501, y: 251, w: 28, h: 44, cx: 515, type: 'canine',   quadrant: 3 },
  { id: 34, x: 532, y: 255, w: 30, h: 40, cx: 547, type: 'premolar', quadrant: 3 },
  { id: 35, x: 565, y: 259, w: 30, h: 40, cx: 580, type: 'premolar', quadrant: 3 },
  { id: 36, x: 598, y: 263, w: 38, h: 46, cx: 617, type: 'molar',    quadrant: 3 },
  { id: 37, x: 639, y: 267, w: 38, h: 46, cx: 658, type: 'molar',    quadrant: 3 },
  { id: 38, x: 680, y: 270, w: 38, h: 46, cx: 699, type: 'molar',    quadrant: 3 },
]

// ─── Anatomical Path Generators ───────────────────────────────────────────────

/**
 * Crown SVG path — anatomical shapes per tooth type
 * All shapes use relative Bezier curves fitted to the bounding box (x,y,w,h)
 */
function crownPath(tooth) {
  const { x, y, w, h, type } = tooth
  const r = x + w   // right edge
  const b = y + h   // bottom edge
  const mx = x + w / 2  // center x
  const pad = 2

  switch (type) {
    case 'molar': {
      // Rectangular with rounded corners, slight waist at midpoint
      const cw = w * 0.12
      return [
        `M ${x + 6} ${y + pad}`,
        `C ${x + pad} ${y + pad}, ${x + pad} ${y + 6}, ${x + pad} ${y + 10}`,
        `L ${x + pad} ${b - 10}`,
        `C ${x + pad} ${b - 4}, ${x + 8} ${b - pad}, ${x + 12} ${b - pad}`,
        `L ${r - 12} ${b - pad}`,
        `C ${r - 8} ${b - pad}, ${r - pad} ${b - 4}, ${r - pad} ${b - 10}`,
        `L ${r - pad} ${y + 10}`,
        `C ${r - pad} ${y + 6}, ${r - pad} ${y + pad}, ${r - 6} ${y + pad}`,
        `C ${mx + cw} ${y + pad}, ${mx + cw} ${y + pad}, ${mx} ${y + pad}`,
        `C ${mx - cw} ${y + pad}, ${mx - cw} ${y + pad}, ${x + 6} ${y + pad}`,
        'Z',
      ].join(' ')
    }
    case 'premolar': {
      // Shorter rectangular with one visible cusp bump at top center
      const bumpH = 4
      return [
        `M ${x + 5} ${y + pad}`,
        `C ${x + pad} ${y + pad}, ${x + pad} ${y + 5}, ${x + pad} ${y + 9}`,
        `L ${x + pad} ${b - 8}`,
        `C ${x + pad} ${b - 3}, ${x + 7} ${b - pad}, ${x + 11} ${b - pad}`,
        `L ${r - 11} ${b - pad}`,
        `C ${r - 7} ${b - pad}, ${r - pad} ${b - 3}, ${r - pad} ${b - 8}`,
        `L ${r - pad} ${y + 9}`,
        `C ${r - pad} ${y + 5}, ${r - pad} ${y + pad}, ${r - 5} ${y + pad}`,
        `L ${mx + 5} ${y + pad}`,
        `Q ${mx} ${y - bumpH} ${mx - 5} ${y + pad}`,
        `L ${x + 5} ${y + pad}`,
        'Z',
      ].join(' ')
    }
    case 'canine': {
      // Pointed/triangular top — classic canine cusp
      return [
        `M ${mx} ${y}`,
        `C ${mx + 8} ${y + 8}, ${r - pad} ${y + 12}, ${r - pad} ${y + 16}`,
        `L ${r - pad} ${b - 10}`,
        `C ${r - pad} ${b - 4}, ${r - 7} ${b - pad}, ${r - 11} ${b - pad}`,
        `L ${x + 11} ${b - pad}`,
        `C ${x + 7} ${b - pad}, ${x + pad} ${b - 4}, ${x + pad} ${b - 10}`,
        `L ${x + pad} ${y + 16}`,
        `C ${x + pad} ${y + 12}, ${mx - 8} ${y + 8}, ${mx} ${y}`,
        'Z',
      ].join(' ')
    }
    case 'incisor':
    default: {
      // Trapezoidal — wider at bottom (incisal edge), narrower neck
      const neckIn = 4
      return [
        `M ${x + neckIn + 3} ${y + pad}`,
        `C ${x + pad} ${y + pad}, ${x + pad} ${y + 8}, ${x + pad} ${y + 14}`,
        `L ${x + pad} ${b - 6}`,
        `C ${x + pad} ${b - 2}, ${x + 5} ${b - pad}, ${x + 8} ${b - pad}`,
        `L ${r - 8} ${b - pad}`,
        `C ${r - 5} ${b - pad}, ${r - pad} ${b - 2}, ${r - pad} ${b - 6}`,
        `L ${r - pad} ${y + 14}`,
        `C ${r - pad} ${y + 8}, ${r - pad} ${y + pad}, ${r - neckIn - 3} ${y + pad}`,
        `L ${x + neckIn + 3} ${y + pad}`,
        'Z',
      ].join(' ')
    }
  }
}

/**
 * Root path — tapered shape below/above the crown
 */
function rootPath(tooth, arch) {
  const { x, y, w, h, type } = tooth
  const rootH = type === 'molar' ? 28 : type === 'premolar' ? 24 : 26
  const rootNarrow = type === 'molar' ? 0.55 : 0.45
  const pad = 4

  if (arch === 'upper') {
    // Root goes upward from crown top
    const rx0 = x + w * 0.2
    const rx1 = x + w * 0.8
    const ry0 = y + 2
    const rtipX = x + w * 0.5
    const rtipY = y - rootH
    if (type === 'molar') {
      // Two roots
      const rw = w * 0.3
      return [
        `M ${rx0} ${ry0}`,
        `C ${rx0 - 2} ${ry0 - 10}, ${rx0 - rw} ${rtipY + 8}, ${rx0 - rw / 2} ${rtipY}`,
        `C ${rx0} ${rtipY + 4}, ${rx0 + 2} ${ry0 - 6}, ${rx0 + 4} ${ry0}`,
        'Z',
        `M ${rx1} ${ry0}`,
        `C ${rx1 + 2} ${ry0 - 10}, ${rx1 + rw} ${rtipY + 8}, ${rx1 + rw / 2} ${rtipY}`,
        `C ${rx1} ${rtipY + 4}, ${rx1 - 2} ${ry0 - 6}, ${rx1 - 4} ${ry0}`,
        'Z',
      ].join(' ')
    }
    return [
      `M ${rx0} ${ry0}`,
      `C ${rx0 - 2} ${ry0 - 12}, ${rtipX - 4} ${rtipY + 4}, ${rtipX} ${rtipY}`,
      `C ${rtipX + 4} ${rtipY + 4}, ${rx1 + 2} ${ry0 - 12}, ${rx1} ${ry0}`,
      'Z',
    ].join(' ')
  } else {
    // Root goes downward from crown bottom
    const rx0 = x + w * 0.2
    const rx1 = x + w * 0.8
    const ry0 = y + h - 2
    const rtipX = x + w * 0.5
    const rtipY = y + h + rootH
    if (type === 'molar') {
      const rw = w * 0.3
      return [
        `M ${rx0} ${ry0}`,
        `C ${rx0 - 2} ${ry0 + 10}, ${rx0 - rw} ${rtipY - 8}, ${rx0 - rw / 2} ${rtipY}`,
        `C ${rx0} ${rtipY - 4}, ${rx0 + 2} ${ry0 + 6}, ${rx0 + 4} ${ry0}`,
        'Z',
        `M ${rx1} ${ry0}`,
        `C ${rx1 + 2} ${ry0 + 10}, ${rx1 + rw} ${rtipY - 8}, ${rx1 + rw / 2} ${rtipY}`,
        `C ${rx1} ${rtipY - 4}, ${rx1 - 2} ${ry0 + 6}, ${rx1 - 4} ${ry0}`,
        'Z',
      ].join(' ')
    }
    return [
      `M ${rx0} ${ry0}`,
      `C ${rx0 - 2} ${ry0 + 12}, ${rtipX - 4} ${rtipY - 4}, ${rtipX} ${rtipY}`,
      `C ${rtipX + 4} ${rtipY - 4}, ${rx1 + 2} ${ry0 + 12}, ${rx1} ${ry0}`,
      'Z',
    ].join(' ')
  }
}

/**
 * Occlusal surface detail path — fissure/crest patterns
 */
function occlusalDetail(tooth, arch) {
  const { x, y, w, h, type } = tooth
  const pad = 6
  const ix = x + pad
  const iy = y + pad
  const iw = w - pad * 2
  const ih = h - pad * 2

  switch (type) {
    case 'molar': {
      // Cross fissure pattern (4-cusp molar)
      const mx = ix + iw / 2
      const my = iy + ih / 2
      return [
        `M ${mx} ${iy}`,
        `Q ${mx + iw * 0.25} ${my - ih * 0.1} ${ix + iw} ${iy}`,
        `Q ${mx + iw * 0.25} ${my + ih * 0.1} ${ix + iw} ${iy + ih}`,
        `Q ${mx - iw * 0.25} ${my + ih * 0.1} ${ix} ${iy + ih}`,
        `Q ${mx - iw * 0.25} ${my - ih * 0.1} ${ix} ${iy}`,
        `Q ${mx} ${my - ih * 0.3} ${mx} ${iy}`,
        'Z',
      ].join(' ')
    }
    case 'premolar': {
      // Two-cusp buccal/lingual line
      const mx = ix + iw / 2
      return [
        `M ${mx} ${iy}`,
        `Q ${mx + 4} ${iy + ih * 0.4} ${mx + 2} ${iy + ih}`,
        `Q ${mx - 2} ${iy + ih * 0.6} ${mx - 2} ${iy + ih}`,
        `Q ${mx - 3} ${iy + ih * 0.4} ${mx} ${iy}`,
        'Z',
      ].join(' ')
    }
    case 'canine': {
      // Single cusp ridge
      const mx = ix + iw / 2
      return [
        `M ${mx} ${iy}`,
        `L ${mx + 4} ${iy + ih * 0.5}`,
        `L ${mx} ${iy + ih * 0.9}`,
        `L ${mx - 4} ${iy + ih * 0.5}`,
        'Z',
      ].join(' ')
    }
    default: {
      // Incisor — mamelons (3 small bumps at incisal edge)
      const bumpY = arch === 'upper' ? iy + ih - 3 : iy + 2
      const q1 = ix + iw * 0.2
      const q2 = ix + iw * 0.5
      const q3 = ix + iw * 0.8
      const bump = arch === 'upper' ? 4 : -4
      return [
        `M ${q1 - 4} ${bumpY}`,
        `Q ${q1} ${bumpY + bump} ${q1 + 4} ${bumpY}`,
        `M ${q2 - 4} ${bumpY}`,
        `Q ${q2} ${bumpY + bump} ${q2 + 4} ${bumpY}`,
        `M ${q3 - 4} ${bumpY}`,
        `Q ${q3} ${bumpY + bump} ${q3 + 4} ${bumpY}`,
      ].join(' ')
    }
  }
}

// ─── Style Methods ────────────────────────────────────────────────────────────
function isSelected(id) {
  return selectedTeeth.value.includes(id)
}

function toothGradient(id) {
  if (isSelected(id)) return 'url(#enamel-selected)'
  if (hoveredTooth.value === id) return 'url(#enamel-hover)'
  return 'url(#enamel-normal)'
}

function toothStroke(id) {
  if (isSelected(id)) return '#1d4ed8'
  if (hoveredTooth.value === id) return '#60a5fa'
  return '#b0bec8'
}

// ─── Interactions ─────────────────────────────────────────────────────────────
function toggleTooth(id) {
  const current = [...selectedTeeth.value]
  const idx = current.indexOf(id)
  if (idx === -1) {
    current.push(id)
  } else {
    current.splice(idx, 1)
    if (current.length < 2) isBridge.value = false
  }
  selectedTeeth.value = current
}

function clearSelection() {
  selectedTeeth.value = []
  isBridge.value = false
}
</script>

<style scoped>
.odontogram-svg {
  display: block;
}

.tooth-group {
  transition: opacity 0.12s ease;
}

.tooth-group:hover {
  opacity: 0.88;
}

.tooth-crown {
  transition: fill 0.15s ease, stroke 0.15s ease;
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
