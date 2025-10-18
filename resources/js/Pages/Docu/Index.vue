<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3'
import {
  EyeIcon,
  CloudArrowUpIcon,
  ArrowDownTrayIcon,
  PaperClipIcon,
  BellIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/solid'
import { ref } from 'vue'

const props = defineProps({
  integrante: Object,
})

// Formulario de subida
const form = useForm({
  ine: null,
  comprobante_domiciliario: null,
  bajo_protesta_art_170: null,
  integracion_formula: null,
  curriculum_vitae: null,
  carta_motivos: null,
  cumplimiento_normatividad: null,
})

// Estado local de los nombres de archivo seleccionados
const selectedFiles = ref({})

// Mensaje de éxito
const successMessage = ref('')

// Enviar documentos
const submitForm = () => {
  form.post(route('docu.store', props.integrante.id), {
    forceFormData: true,
    onSuccess: () => {
      form.reset()
      selectedFiles.value = {}
      successMessage.value = 'Documentos guardados correctamente.'
      setTimeout(() => (successMessage.value = ''), 3000)
    },
  })
}

// Buscar documento existente
const getDoc = (tipo) => props.integrante.documentos.find((d) => d.tipo === tipo)

// Obtener nombre limpio del archivo (sin ruta)
const getCleanFileName = (doc) => {
  if (!doc || !doc.archivo) return ''
  const fullName = doc.archivo.split('/').pop() // ejemplo: iOZV2xPExa3wJpONZKz97h7NlUcnoIdIDqfxBekL.pdf
  const extension = fullName.split('.').pop()
  const tipoLabel = doc.tipo.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) // ejemplo: "ine" → "Ine"
  return `${tipoLabel}.${extension}` // devuelve "Ine.pdf"
}

// Generar URL pública
const getPublicUrl = (ruta) => `/storage/${ruta.replace('public/', '')}`

// Documentos requeridos
const documentos = [
  { key: 'ine', label: 'INE' },
  { key: 'comprobante_domiciliario', label: 'Comprobante domiciliario' },
  { key: 'bajo_protesta_art_170', label: 'Bajo protesta art. 170' },
  { key: 'integracion_formula', label: 'Integración de fórmula' },
  { key: 'curriculum_vitae', label: 'Currículum Vitae' },
  { key: 'carta_motivos', label: 'Carta de motivos' },
  { key: 'cumplimiento_normatividad', label: 'Cumplimiento de normatividad' },
]

// Al seleccionar archivo
const handleFileSelect = (event, key) => {
  const file = event.target.files[0]
  if (file) {
    form[key] = file
    selectedFiles.value[key] = file.name
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">
        Documentos de {{ integrante.nombre }} {{ integrante.apellido }}
      </h1>

      <!-- Mensaje de éxito -->
      <transition name="fade">
        <div
          v-if="successMessage"
          class="mb-4 flex items-center gap-2 bg-green-100 text-green-800 border border-green-300 px-4 py-2 rounded"
        >
          <CheckCircleIcon class="h-5 w-5 text-green-700" />
          <span>{{ successMessage }}</span>
        </div>
      </transition>

      <form @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="doc in documentos"
            :key="doc.key"
            class="border p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition"
          >
            <label class="block text-sm font-medium mb-2">
              {{ doc.label }}
            </label>

            <!-- Botón discreto de carga -->
            <div class="flex items-center space-x-2">
              <label
                class="inline-flex items-center px-2 py-1 text-xs bg-white border border-gray-300 rounded cursor-pointer hover:bg-gray-200 transition"
              >
                <PaperClipIcon class="w-3 h-3 mr-1 text-gray-600" />
                Cargar
                <input
                  type="file"
                  class="hidden"
                  @change="handleFileSelect($event, doc.key)"
                />
              </label>

              <!-- Mostrar nombre si se acaba de cargar -->
              <span
                v-if="selectedFiles[doc.key]"
                class="text-xs text-gray-700 truncate max-w-[140px]"
                >{{ selectedFiles[doc.key] }}</span>
            </div>

            <!-- Estado del documento -->
            <div class="mt-3">
              <div v-if="getDoc(doc.key)">
                <p class="text-green-700 text-sm mb-2 flex items-center gap-1">
                  <CheckCircleIcon class="w-4 h-4" />
                  <span>{{ getCleanFileName(getDoc(doc.key)) }}</span>
                </p>

                <div class="flex space-x-3">
                  <!-- Ver documento -->
                  <a
                    :href="getPublicUrl(getDoc(doc.key).archivo)"
                    target="_blank"
                    class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm"
                  >
                    <EyeIcon class="w-4 h-4 mr-1" />
                    Ver
                  </a>

                  <!-- Descargar documento -->
                  <a
                    :href="route('docu.download', getDoc(doc.key).id)"
                    class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm"
                  >
                    <ArrowDownTrayIcon class="w-4 h-4 mr-1" />
                    Descargar
                  </a>
                </div>
              </div>

              <div v-else-if="!selectedFiles[doc.key]">
                <BellIcon class="w-4 h-4 text-yellow-500 inline-block mr-1" />
                <p class="text-gray-500 text-sm inline-block">Aún no se ha subido</p>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <button
            type="submit"
            class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 flex items-center"
          >
            <CloudArrowUpIcon class="h-5 w-5 mr-1" />
            Guardar documentos
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
