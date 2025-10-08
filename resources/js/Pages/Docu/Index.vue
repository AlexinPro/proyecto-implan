<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { DocumentPlusIcon, ArrowDownTrayIcon, PrinterIcon, CircleStackIcon } from '@heroicons/vue/24/solid'

defineProps({
  integrante: Object,
  tipos: Array,
  documentos: Array
})

// Estado para manejar los archivos
const archivos = ref({})

// Formulario con Inertia
const form = useForm({
  nombre: '',
  archivo: null
})

// Subir o reemplazar documento
function subirDocumento(nombre) {
  form.nombre = nombre
  form.archivo = archivos.value[nombre] || null

  if (!form.archivo) {
    alert('Selecciona un archivo PDF antes de subirlo.')
    return
  }

  form.post(route('docu.store', integrante.id), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      form.reset()
      archivos.value[nombre] = null
    }
  })
}

// Descargar documento existente
function descargar(ruta) {
  window.open(`/storage/${ruta}`, '_blank')
}

// Imprimir documento (abrir en otra pestaña y llamar print)
function imprimir(ruta) {
  const printWindow = window.open(`/storage/${ruta}`, '_blank')
  printWindow?.addEventListener('load', () => printWindow.print())
}
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">
      Documentación de:
      <span class="text-blue-600">
        {{ integrante.nombre }} {{ integrante.apellido }}
      </span>
      <div class="md-6 mt-4 flex justify-end ">
      <PrimaryButton @click="router.post(route('docu.enviar', integrante.id))">
        <CircleStackIcon class="w-5 h-5 inline-block mr-1"/> Enviar
      </PrimaryButton>
    </div>

    </h1>

    <div class="overflow-x-auto">
      <table class="min-w-full border border-gray-300 rounded-lg">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 border text-left">Documento</th>
            <th class="px-4 py-2 border text-center">Archivo</th>
            <th class="px-4 py-2 border text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(tipo, index) in tipos"
            :key="index"
            class="hover:bg-gray-50"
          >
            <td class="px-4 py-2 border">{{ tipo }}</td>

            <!-- Mostrar nombre del PDF si existe -->
            <td class="px-4 py-2 border text-center">
              <span v-if="documentos.find(d => d.nombre === tipo)">
                {{ documentos.find(d => d.nombre === tipo)?.ruta.split('/').pop() }}
              </span>
              <span v-else class="text-gray-400 italic">No cargado</span>
            </td>

            <td class="px-4 py-2 border text-center">
              <div class="flex justify-center items-center gap-2">
                <!-- Input de archivo -->
                <input
                  type="file"
                  accept="application/pdf"
                  class="text-sm"
                  @change="e => archivos.value[tipo] = e.target.files[0]"
                />

                <!-- Botón para subir 
                <button
                  class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700"
                  @click="subirDocumento(tipo)"
                >
                  <DocumentPlusIcon class="w-5 h-5 inline-block mr-1" />
                  Subir
                </button> -->

                <!-- Si ya existe, mostrar acciones -->
                <template v-if="documentos.find(d => d.nombre === tipo)">
                  <button
                    class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-700"
                    @click="descargar(documentos.find(d => d.nombre === tipo).ruta)"
                  >
                    <ArrowDownTrayIcon class="w-5 h-5 inline-block mr-1" />
                    Descargar
                  </button>

                  <button
                    class="px-2 py-1 bg-gray-700 text-white rounded hover:bg-gray-900"
                    @click="imprimir(documentos.find(d => d.nombre === tipo).ruta)"
                  >
                    <PrinterIcon class="w-5 h-5 inline-block mr-1" />
                    Imprimir
                  </button>
                </template>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Botón final de “Enviar documentos” -->
    <div class="mt-6 flex justify-end">
      <PrimaryButton
        
        class="bg-blue-600 hover:bg-blue-700"
      >
        <CircleStackIcon class="w-5 h-5 inline-block mr-1" />
        Enviar Documentos
      </PrimaryButton>  
    </div>
  </div>
</template>
