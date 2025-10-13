<script setup>
import { ref } from 'vue'

const props = defineProps({
  consejo: Object,
  integrantes: Array,
  meses: Array,
  asistencia: Object,
})

const asistencia = ref(props.asistencia)
const mesSeleccionado = ref(props.meses[0]) // octubre por defecto

function faltas(integranteId) {
  const fechas = mesSeleccionado.value.fechas
  return fechas.filter(fecha => !asistencia.value[integranteId][fecha]).length
}

function colorSemaforo(faltas) {
  if (faltas === 0) return 'text-green-600'
  if (faltas <= 2) return 'text-yellow-500'
  return 'text-red-600'
}
</script>

<template>
  <div class="p-6 bg-white shadow rounded">
    <h1 class="text-2xl font-bold mb-4">Asistencia - {{ consejo.nombre }}</h1>

    <!-- Selector de mes -->
    <div class="mb-4">
      <label class="font-semibold mr-2">Seleccionar mes:</label>
      <select v-model="mesSeleccionado" class="border p-2 rounded">
        <option v-for="mes in meses" :key="mes.mes" :value="mes">
          {{ mes.mes }}
        </option>
      </select>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm border border-collapse">
        <thead>
          <tr class="bg-gray-100 text-center">
            <th class="border p-2">Integrantes</th>
            <th class="border p-2">Cargo a ejercer</th>
            <th
              v-for="fecha in mesSeleccionado.fechas"
              :key="fecha"
              class="border p-2"
            >
              {{ new Date(fecha).getDate() }}
            </th>
            <th class="border p-2">Semáforo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="integrante in integrantes" :key="integrante.id">
            <td class="border p-2">{{ integrante.nombre }}</td>
            <td class="border p-2">{{ integrante.cargo }}</td>

            <td
              v-for="fecha in mesSeleccionado.fechas"
              :key="fecha"
              class="border text-center"
            >
              <input
                type="checkbox"
                v-model="asistencia[integrante.id][fecha]"
              />
            </td>

            <td class="border text-center">
              <span :class="colorSemaforo(faltas(integrante.id))">●</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Botón guardar (futuro) -->
    <button
      class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
    >
      Guardar asistencia
    </button>
  </div>
</template>
