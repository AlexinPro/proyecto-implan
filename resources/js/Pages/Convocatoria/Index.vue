<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, computed } from 'vue'
import { CheckCircleIcon, XCircleIcon, ClipboardDocumentCheckIcon } from '@heroicons/vue/24/solid'

defineProps({
  consejo: Object,
})

// Estado de año y mes
const selectedYear = ref('')
const selectedMonth = ref('')

// Lista de años
const years = [2025, 2026, 2027, 2028, 2029, 2030, 2031, 2032, 2033, 2034]

// Lista de meses
const months = [
  'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
]

// Ejemplo de publicaciones
const publicaciones = ref([
  { id: 1, convocatoria: false, descripcion: 'Planeación anual', sesiones: true, fecha: '2025-01-10' },
  { id: 2, convocatoria: true, descripcion: 'Revisión trimestral', sesiones: false, fecha: '2025-02-15' },
])

// Mostrar tabla solo si hay selección de año y mes
const mostrarTabla = computed(() => selectedYear.value && selectedMonth.value)

// Función para alternar iconos
function toggleCampo(item, campo) {
  item[campo] = !item[campo]
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <!-- Sección de filtros -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Convocatorias</h2>

          <p class="text-gray-600 mb-6">
           Consejo de participación ciudadana de:
            <span class="font-semibold text-gray-800">{{ consejo?.nombre }}</span>
          </p>
        <div class="grid grid-cols-2 gap-4 max-w-lg">
          <div>
            <label class="block text-gray-700 mb-1">Seleccione un año</label>
            <select v-model="selectedYear" class="w-full border-gray-300 rounded-lg shadow-sm">
              <option value="">-- Año --</option>
              <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
            </select>
          </div>

          <div>
            <label class="block text-gray-700 mb-1">Seleccione un mes</label>
            <select v-model="selectedMonth" class="w-full border-gray-300 rounded-lg shadow-sm">
              <option value="">-- Mes --</option>
              <option v-for="month in months" :key="month" :value="month">{{ month }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Tabla -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Publicaciones</h2>

        <div v-if="mostrarTabla" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Convocatoria</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Descripción</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Sesiones</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Fecha de Publicación</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
              <tr v-for="item in publicaciones" :key="item.id" class="hover:bg-gray-50">
                <td class="px-6 py-4">
                  <button @click="toggleCampo(item, 'convocatoria')" class="focus:outline-none">
                    <CheckCircleIcon v-if="item.convocatoria" class="w-6 h-6 text-green-500" />
                    <XCircleIcon v-else class="w-6 h-6 text-red-500" />
                  </button>
                </td>

                <td class="px-6 py-4">{{ item.descripcion }}</td>

                <td class="px-6 py-4">
                  <button @click="toggleCampo(item, 'sesiones')" class="focus:outline-none">
                    <CheckCircleIcon v-if="item.sesiones" class="w-6 h-6 text-green-500" />
                    <XCircleIcon v-else class="w-6 h-6 text-red-500" />
                  </button>
                </td>

                <td class="px-6 py-4">{{ item.fecha }}</td>

                <td class="px-6 py-4 text-center">
                  <button class="text-green-600 hover:text-green-800 font-medium">
                    <ClipboardDocumentCheckIcon class="w-6 h-6 inline" />
                    Subir
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="text-gray-500 text-sm">
          Seleccione un año y mes para mostrar las publicaciones.
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
