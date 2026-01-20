<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, computed } from 'vue'
import { ArrowDownTrayIcon, DocumentTextIcon } from '@heroicons/vue/24/solid'

// Props
const props = defineProps({
  consejo: Object,
  convocatorias: Array,
  asistencias: Array,
  integrantes: Array,
  bajas: Array,
  reporteAsistencias: Array,
  hayDatos: Boolean,
})

const tipoReporte = ref('')

// ================= DOCUMENTOS =================
const tiposDocumentos = [
  { label: 'INE', key: 'ine' },
  { label: 'Comprobante domiciliario', key: 'comprobante_domiciliario' },
  { label: 'Bajo protesta (Art. 170)', key: 'bajo_protesta_art_170' },
  { label: 'Curriculum Vitae', key: 'curriculum_vitae' },
  { label: 'Integración de fórmula', key: 'integracion_formula' },
  { label: 'Carta de motivos', key: 'carta_motivos' },
  { label: 'Cumplimiento de normatividad', key: 'cumplimiento_normatividad' },
]

function tieneDocumento(integrante, key) {
  if (!integrante || !Array.isArray(integrante.documentos)) return false
  return integrante.documentos.some(doc => doc.tipo === key)
}

// ================= FECHAS =================
function formatearFecha(fecha) {
  if (!fecha) return '-'

  const d = new Date(fecha)

  return d.toLocaleDateString('es-MX', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  })
}

// ================= CONVOCATORIAS =================
const meses = [
  'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
]

function obtenerMes(fecha) {
  return meses[new Date(fecha).getMonth()]
}

const tiposSesion = ['ordinaria', 'solemne', 'extraordinaria']

const tablaConvocatoriasPivot = computed(() => {
  const estructura = {}

  tiposSesion.forEach(tipo => {
    estructura[tipo] = {}
    meses.forEach(mes => {
      estructura[tipo][mes] = { convocada: '', realizada: '' }
    })
  })

  props.convocatorias.forEach(c => {
    const mes = obtenerMes(c.fecha)
    estructura[c.tipo_sesion][mes].convocada = c.estado_convocatoria ? 'si' : 'no'
    estructura[c.tipo_sesion][mes].realizada = c.estado_sesion ? 'si' : 'no'
  })

  return estructura
})

//ARCHIVOS DE BAJAS DE INTEGRANTES
function obtenerNombreArchivo(ruta) {
  if (!ruta) return '-'
  return ruta.split('/').pop()
}

function obtenerUrlPublica(ruta) {
  if (!ruta) return '#'
  return `/storage/${ruta}`
}



// ================= EXPORTACIONES =================
function exportarConvocatoriasExcel() {
  const tabla = document.querySelector('#tabla-convocatorias-pivot')
  const blob = new Blob(['\ufeff' + tabla.outerHTML], {
    type: 'application/vnd.ms-excel'
  })

  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `convocatorias_${props.consejo.nombre}.xls`
  link.click()
  URL.revokeObjectURL(link.href)
}

function exportarDocumentosExcel() {
  const tabla = document.querySelector('#tabla-documentos')
  if (!tabla) return

  const blob = new Blob(['\ufeff' + tabla.outerHTML], {
    type: 'application/vnd.ms-excel'
  })

  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `documentos_${props.consejo.nombre}.xls`
  link.click()
  URL.revokeObjectURL(link.href)
}

// ================= DATATABLES =================
import DataTable from 'datatables.net-vue3'
import DataTablesCore from 'datatables.net-dt'
import 'datatables.net-dt/css/dataTables.dataTables.min.css'
import 'datatables.net-buttons-dt/css/buttons.dataTables.min.css'
import Buttons from 'datatables.net-buttons'
import ExcelHTML5 from 'datatables.net-buttons/js/buttons.html5.js'
import JSZip from 'jszip'

window.JSZip = JSZip
DataTable.use(DataTablesCore)
DataTable.use(Buttons)
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 bg-white rounded-lg shadow-md">

      <h1 class="text-2xl font-bold mb-6">
        Reportes del Consejo de Participación Ciudadana de {{ consejo.nombre }}
      </h1>

      <div v-if="!hayDatos" class="text-center text-gray-500 p-10 border rounded-lg bg-gray-50">
        No hay información disponible para generar reportes de este consejo.
      </div>

      <div v-else>

        <!-- SELECTOR -->
        <div class="mb-4">
          <label class="block font-semibold mb-2">Selecciona el tipo de reporte</label>
          <select v-model="tipoReporte" class="border rounded-lg p-2 w-full sm:w-1/3">
            <option disabled value="">--Selecciona--</option>
            <option value="convocatorias">Convocatorias</option>
            <option value="asistencias">Asistencias</option>
            <option value="documentos">Documentos</option>
            <option value="bajas">Bajas de integrantes</option>
          </select>
        </div>

        <!-- CONVOCATORIAS -->
        <div v-if="tipoReporte === 'convocatorias'">
          <h2 class="text-lg font-semibold mb-4">Convocatorias por Mes</h2>

          <table id="tabla-convocatorias-pivot" class="min-w-full border text-sm">
            <thead>
              <tr>
                <th class="border p-2 bg-gray-100">Tipo de sesión</th>
                <th v-for="mes in meses" :key="mes" class="border p-2 bg-gray-100 text-center" colspan="2">
                  {{ mes }}
                </th>
              </tr>
              <tr>
                <th></th>
                <template v-for="mes in meses" :key="mes">
                  <th class="border p-2 bg-gray-200">Convocada</th>
                  <th class="border p-2 bg-gray-200">Realizada</th>
                </template>
              </tr>
            </thead>

            <tbody>
              <tr v-for="tipo in tiposSesion" :key="tipo">
                <td class="border p-2 capitalize font-semibold">{{ tipo }}</td>
                <template v-for="mes in meses" :key="mes">
                  <td class="border p-2 text-center">{{ tablaConvocatoriasPivot[tipo][mes].convocada }}</td>
                  <td class="border p-2 text-center">{{ tablaConvocatoriasPivot[tipo][mes].realizada }}</td>
                </template>
              </tr>
            </tbody>
          </table>

          <div class="text-right mt-4">
            <button @click="exportarConvocatoriasExcel"
              class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex ml-auto">
              <ArrowDownTrayIcon class="w-5 h-5 mr-2" />
              Exportar a Excel
            </button>
          </div>
        </div>

        <!-- ASISTENCIAS -->
        <!-- ASISTENCIAS -->
        <div v-if="tipoReporte === 'asistencias'">
          <h2 class="text-lg font-semibold mb-2">
            Reporte de Asistencias por Integrante y Tipo de Sesión
          </h2>

          <DataTable :data="props.reporteAsistencias" :columns="[
            { title: 'Integrante', data: 'integrante' },
            { title: 'Ordinaria', data: 'ordinaria' },
            { title: 'Extraordinaria', data: 'extraordinaria' },
            { title: 'Solemne', data: 'solemne' },
            { title: 'Total', data: 'total' },
          ]" class="display stripe hover" :options="{ dom: 'Bfrtip', buttons: ['excelHtml5'] }" />
        </div>

        <!-- DOCUMENTOS -->
        <div v-if="tipoReporte === 'documentos'">
          <h2 class="text-lg font-semibold mb-2">Checklist de Documentos</h2>

          <table id="tabla-documentos" class="min-w-full border text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 text-left">Integrante</th>
                <th v-for="doc in tiposDocumentos" :key="doc.key" class="p-2 text-center">
                  {{ doc.label }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="integrante in props.integrantes" :key="integrante.id" class="border-t">
                <td class="p-2">{{ integrante.nombre }} {{ integrante.apellido }}</td>
                <td v-for="doc in tiposDocumentos" :key="doc.key" class="p-2 text-center">
                  <span v-if="tieneDocumento(integrante, doc.key)" class="text-green-700 font-semibold">si</span>
                  <span v-else class="text-red-700 font-semibold">no</span>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="text-right mt-4">
            <button @click="exportarDocumentosExcel"
              class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex ml-auto">
              <ArrowDownTrayIcon class="w-5 h-5 mr-2" />
              Exportar a Excel
            </button>
          </div>
        </div>

        <!-- BAJAS -->
        <div v-if="tipoReporte === 'bajas'">
          <h2 class="text-lg font-semibold mb-4">Integrantes dados de baja</h2>

          <table class="min-w-full border text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 text-left">Integrante</th>
                <th class="p-2 text-left">Motivo</th>
                <th class="p-2 text-left">Fecha de baja</th>
                <th class="p-2 text-left">Evidencia de baja</th>
              </tr>
            </thead>

            <tbody>
              <tr v-if="props.bajas.length === 0">
                <td colspan="4" class="p-4 text-center text-gray-500">
                  No hay integrantes dados de baja en este consejo.
                </td>
              </tr>

              <tr v-for="baja in props.bajas" :key="baja.id" class="border-t">
                <td class="p-2">
                  {{ baja.nombre }} {{ baja.apellido }}
                </td>

                <td class="p-2 capitalize">
                  <span v-if="baja.motivo === 'inasistencia'">Inasistencia</span>
                  <span v-else-if="baja.motivo === 'sancion'">Sanción</span>
                  <span v-else-if="baja.motivo === 'fin_periodo'">No realizó proceso de reelección</span>
                  <span v-else>Renuncia</span>
                </td>

                <td class="p-2">
                  {{ formatearFecha(baja.fecha_baja) }}
                </td>

                <td class="p-2">
                  <template v-if="baja.evidencia_pdf">
                    <a :href="obtenerUrlPublica(baja.evidencia_pdf)" target="_blank"
                      class="text-red-700 underline hover:text-red-900">
                      {{ obtenerNombreArchivo(baja.evidencia_pdf) }}
                    </a>
                  </template>

                  <span v-else class="text-gray-500">
                    Sin archivo
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
