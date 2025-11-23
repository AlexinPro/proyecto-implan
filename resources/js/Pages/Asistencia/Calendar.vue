<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  consejo: Object
})

// Año seleccionado (por defecto el actual)
const year = ref(new Date().getFullYear())

// Cambiar año
function cambiarAno(e) {
  year.value = parseInt(e.target.value)
}

// Meses
const meses = [
  'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
]

// Generar días de cada mes
function generarCalendario(mes, ano) {
  const primerDia = new Date(ano, mes, 1).getDay()
  const diasEnMes = new Date(ano, mes + 1, 0).getDate()

  const dias = []

  // Espacios vacíos antes del inicio del mes
  for (let i = 0; i < primerDia; i++) {
    dias.push(null)
  }

  // Días del mes
  for (let d = 1; d <= diasEnMes; d++) {
    dias.push(d)
  }

  return dias
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Calendario de Asistencias" />

    <div class="p-6">
      <!-- Título -->
      <h1 class="text-2xl font-bold mb-4">
        Calendario de asistencias – {{ consejo.nombre }}
      </h1>

      <!-- Selector de año -->
      <div class="mb-6">
        <label class="font-semibold text-gray-700 mr-3">Ver año:</label>
        <select
          class="border p-2 rounded"
          @change="cambiarAno"
          :value="year"
        >
          <option v-for="ano in [2024,2025,2026,2027,2028,2029]" :key="ano">
            {{ ano }}
          </option>
        </select>
      </div>

      <!-- Calendario anual -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="(mes, i) in meses"
          :key="i"
          class="border rounded-lg p-4 shadow-sm bg-white"
        >
          <!-- Nombre del mes -->
          <h2 class="text-lg font-semibold text-center mb-2">{{ mes }}</h2>

          <!-- Cabecera de días -->
          <div class="grid grid-cols-7 text-gray-600 text-xs font-bold mb-1">
            <span>Dom</span>
            <span>Lun</span>
            <span>Mar</span>
            <span>Mié</span>
            <span>Jue</span>
            <span>Vie</span>
            <span>Sáb</span>
          </div>

          <!-- Días del mes -->
          <div class="grid grid-cols-7 text-sm gap-1">
            <div
              v-for="(dia, idx) in generarCalendario(i, year)"
              :key="idx"
              class="h-8 flex items-center justify-center rounded"
              :class="dia ? 'bg-gray-50 hover:bg-gray-200 cursor-pointer' : ''"
            >
              {{ dia || '' }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
