<script setup>
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ClipboardDocumentIcon, CalendarIcon, FlagIcon } from '@heroicons/vue/24/solid'
import { ref, computed } from 'vue'
import Form from './Form.vue'
import HistoryModal from './History.vue'

const props = defineProps({
  consejo: Object,
  integrantes: Array,
  asistencias: Array
})

// Modal de Form
const showModal = ref(false)
function abrirModal() { showModal.value = true }
function cerrarModal() { showModal.value = false }

// Modal de Historial
const showHistorial = ref(false)
const integranteSeleccionado = ref(null)
const historialSeleccionado = ref([])

function abrirHistorial(integrante) {
  integranteSeleccionado.value = integrante
  historialSeleccionado.value = props.asistencias.filter(a => a.integrante_id === integrante.id)
  showHistorial.value = true
}

function cerrarHistorial() {
  showHistorial.value = false
  integranteSeleccionado.value = null
  historialSeleccionado.value = []
}

// Agrupar integrantes en fórmulas de 2
const formulas = computed(() => {
  const grouped = []
  for (let i = 0; i < props.integrantes.length; i += 2) {
    grouped.push(props.integrantes.slice(i, i + 2))
  }
  return grouped
})

// Semáforo
function obtenerSemaforo(integranteId) {
  const registros = props.asistencias
    .filter(a => a.integrante_id === integranteId)
    .sort((a, b) => new Date(a.fecha) - new Date(b.fecha))

  if (!registros.length) return 'verde'

  // ❗ SOLO faltas reales (no justificadas)
  const faltas = registros.filter(a => a.estado === 'falto')

  if (faltas.length === 0) return 'verde'
  if (faltas.length === 2) return 'amarillo'

  if (faltas.length === 3) {
    let consecutivas = true

    for (let i = 1; i < faltas.length; i++) {
      const diff =
        (new Date(faltas[i].fecha) - new Date(faltas[i - 1].fecha)) /
        (1000 * 60 * 60 * 24)

      if (diff > 40) consecutivas = false
    }

    return consecutivas ? 'rojo' : 'amarillo'
  }

  if (faltas.length >= 4) return 'rojo'

  return 'verde'
}

function colorClase(color) {
  return {
    verde: 'bg-green-500',
    amarillo: 'bg-yellow-400',
    rojo: 'bg-red-500'
  }[color] || 'bg-gray-300'
}
</script>

<template>
  <AuthenticatedLayout>
        <div class="mt-2 mb-4">
      <Link :href="route('consejos.asistencias', consejo.id)" class="text-gray-600 hover:underline">
        ← Volver a Consejos de Participación Ciudadana
      </Link>
    </div>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-2">Registro de asistencias del consejo: {{ props.consejo.nombre }}</h1>

      <!-- Botones -->
      <div class="flex gap-4 mb-6">
        <Link :href ="route('asistencias.evidencias', consejo.id)"
        class="flex items-center gap-2 px-4 py-2 bg-red-700 text-white rounded hover:bg-red-900">
          <ClipboardDocumentIcon class="w-5 h-5" /> Evidencia documental
        </Link>
        
        <Link :href="route('asistencia.calendar', props.consejo.id)"
          class="flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-800">
        <CalendarIcon class="w-5 h-5" /> Ver calendario
        </Link>
      </div>

      <!-- Tabla -->
      <div v-if="formulas.length" class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 rounded-lg">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 border"># Fórmula</th>
              <th class="px-4 py-2 border">Integrantes</th>
              <th class="px-4 py-2 border">Asistencias</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(f, index) in formulas" :key="index" class="hover:bg-gray-50">
              <!-- # -->
              <td class="px-4 py-2 border text-center font-semibold">{{ index + 1 }}</td>

              <!-- Integrantes -->
              <td class="px-4 py-2 border">
                <div v-if="f[0]" class="flex items-center gap-2">
                  <span class="w-3 h-3 rounded-full" :class="colorClase(obtenerSemaforo(f[0].id))"></span>
                  {{ f[0].nombre }} {{ f[0].apellido }}
                </div>
                <div v-else class="text-gray-400">Pendiente</div>
                <br />
                <div v-if="f[1]" class="flex items-center gap-2">
                  <span class="w-3 h-3 rounded-full" :class="colorClase(obtenerSemaforo(f[1].id))"></span>
                  {{ f[1].nombre }} {{ f[1].apellido }}
                </div>
                <div v-else class="text-gray-400">Pendiente</div>
              </td>

              <!-- Asistencias -->
              <td class="px-4 py-2 border text-sm">
                <div v-if="f[0]" class="mb-3">
                  <button @click="abrirHistorial(f[0])"
                    class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-800 text-sm">
                    <FlagIcon class="w-4 h-4 inline-block mr-1" /> Historial
                  </button>
                </div>
                <hr class="my-3" />
                <div v-if="f[1]">
                  <button @click="abrirHistorial(f[1])"
                    class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-800 text-sm">
                    <FlagIcon class="w-4 h-4 inline-block mr-1" /> Historial
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p v-else class="text-gray-500">No hay integrantes registrados.</p>

      <!-- Modales -->
      <Form v-if="showModal" :integrantes="props.integrantes" :consejo-id="props.consejo.id" @close="cerrarModal" />

      <HistoryModal :show="showHistorial" :integrante="integranteSeleccionado" :historial="historialSeleccionado"
        @close="cerrarHistorial" />
    </div>
  </AuthenticatedLayout>
</template>