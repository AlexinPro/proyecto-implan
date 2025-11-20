<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted, watch, nextTick } from 'vue'
import { ArrowDownTrayIcon } from '@heroicons/vue/24/solid'

// DataTables JS
import 'datatables.net'
import 'datatables.net-buttons'
import 'datatables.net-buttons/js/buttons.html5.js'
import 'datatables.net-buttons/js/buttons.print.js'
import 'datatables.net-responsive-dt'

const props = defineProps({
  consejo: Object,
  convocatorias: Array,
  asistencias: Array,
  hayDatos: Boolean,
})

const tipoReporte = ref('')

// Re-inicializa DataTable cuando cambia el tipo de reporte
watch(tipoReporte, async (newVal) => {
  if (!newVal) return
  await nextTick()

  const tableId = newVal === 'convocatorias' ? '#convocatoriasTable' : '#asistenciasTable'

  if ($.fn.DataTable.isDataTable(tableId)) {
    $(tableId).DataTable().destroy()
  }

  $(tableId).DataTable({
    dom: 'Bfrtip',
    buttons: ['excelHtml5'],
    responsive: true,
    paging: true,
    searching: true,
    info: false,
  })
})

// Función para exportar Excel desde DataTables
const exportExcel = () => {
  const tableId = tipoReporte.value === 'convocatorias' ? '#convocatoriasTable' : '#asistenciasTable'
  $(tableId).DataTable().button('.buttons-excel').trigger()
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 bg-white rounded-lg shadow-md">
      <h1 class="text-2xl font-bold mb-4">
        Reportes del Consejo de Participación Ciudadana de {{ consejo.nombre }}
      </h1>

      <!-- Si no hay datos -->
      <div v-if="!hayDatos" class="text-center text-gray-500 p-10 border rounded-lg bg-gray-50">
        No hay información disponible para generar reportes de este consejo.
      </div>

      <!-- Si hay datos -->
      <div v-else>
        <!-- Selector de reporte -->
        <div class="mb-4">
          <label class="block font-semibold mb-2">Selecciona el tipo de reporte</label>
          <select
            v-model="tipoReporte"
            class="border rounded-lg p-2 w-full sm:w-1/3 focus:ring focus:ring-indigo-300"
          >
            <option disabled value="">-- Selecciona --</option>
            <option value="convocatorias">Convocatorias</option>
            <option value="asistencias">Asistencias</option>
          </select>
        </div>

        <!-- Botón Exportar Excel -->
        <div v-if="tipoReporte" class="mb-4 text-right">
          <button
            @click="exportExcel"
            class="flex items-center bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition ml-auto"
          >
            <ArrowDownTrayIcon class="w-5 h-5 mr-2" />
            Exportar Excel
          </button>
        </div>

        <!-- Tabla de Convocatorias -->
        <div v-if="tipoReporte === 'convocatorias'">
          <h2 class="text-lg font-semibold mb-2">Listado de Convocatorias</h2>
          <table id="convocatoriasTable" class="min-w-full border text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 text-left">Nombre</th>
                <th class="p-2 text-left">Fecha</th>
                <th class="p-2 text-center">Estado Convocatoria</th>
                <th class="p-2 text-center">Estado Sesión</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in convocatorias" :key="item.id" class="border-t">
                <td class="p-2">{{ item.nombre }}</td>
                <td class="p-2">{{ item.fecha }}</td>
                <td class="p-2 text-center">{{ item.estado_convocatoria ? 'Realizada' : 'Pendiente' }}</td>
                <td class="p-2 text-center">{{ item.estado_sesion ? 'Realizada' : 'Pendiente' }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Tabla de Asistencias -->
        <div v-if="tipoReporte === 'asistencias'">
          <h2 class="text-lg font-semibold mb-2">Listado de Asistencias</h2>
          <table id="asistenciasTable" class="min-w-full border text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 text-left">Integrante</th>
                <th class="p-2 text-left">Sesión</th>
                <th class="p-2 text-left">Fecha</th>
                <th class="p-2 text-center">Asistencia</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in asistencias" :key="item.id" class="border-t">
                <td class="p-2">{{ item.integrante }}</td>
                <td class="p-2">{{ item.tipo_sesion }}</td>
                <td class="p-2">{{ item.fecha }}</td>
                <td class="p-2 text-center">{{ item.asistencia ? 'Asistió' : 'Faltó' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
