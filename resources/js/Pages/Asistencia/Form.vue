<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  consejoId: { type: [Number, String], required: true },
  integrantes: { type: Array, required: true },
  sesion: { type: Object, required: true }
})

const emit = defineEmits(['close', 'saved'])

// Datos de asistencia
const form = ref({
  asistencias: props.integrantes.map(i => ({
    integrante_id: i.id,
    estado: 'asistio'
  }))
})

const evidencia = ref(null)
const errorArchivo = ref('')

// Validar evidencia PDF
function onFileChange(e) {
  const file = e.target.files[0]

  if (!file) {
    evidencia.value = null
    return
  }

  if (file.type !== 'application/pdf') {
    errorArchivo.value = 'Solo se permiten archivos PDF.'
    evidencia.value = null
    e.target.value = ''
    return
  }

  errorArchivo.value = ''
  evidencia.value = file
}

// Guardar asistencias de la sesión programada
function submitForm() {
  const formData = new FormData()

  form.value.asistencias.forEach((a, index) => {
    formData.append(`asistencias[${index}][integrante_id]`, a.integrante_id)
    formData.append(`asistencias[${index}][estado]`, a.estado)
  })

  if (evidencia.value) formData.append('evidencia', evidencia.value)

  router.post(
    route('sesiones.asistencia.store', {
      consejo: props.consejoId,
      sesion: props.sesion.id
    }),
    formData,
    {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        emit('saved')
        emit('close')
      }
    }
  )
}
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-xl">

      <!-- Título -->
      <h2 class="text-lg font-bold mb-4">
        Registrar asistencia
      </h2>

      <!-- Datos de la sesión -->
      <div class="grid grid-cols-2 gap-3 mb-4">

        <!-- Fecha -->
        <div>
          <label class="block text-sm font-medium mb-1">
            Fecha
          </label>

          <input
            type="date"
            :value="sesion.fecha"
            readonly
            class="w-full border rounded px-3 py-2 bg-gray-100"
          />
        </div>

        <!-- Tipo de sesión -->
        <div>
          <label class="block text-sm font-medium mb-1">
            Tipo de sesión
          </label>

          <input
            type="text"
            :value="sesion.tipo_sesion"
            readonly
            class="w-full border rounded px-3 py-2 bg-gray-100 capitalize"
          />
        </div>

      </div>

      <!-- Integrantes -->
      <div class="border rounded p-3 max-h-64 overflow-y-auto mb-4">

        <p class="text-sm font-medium mb-2">
          Integrantes
        </p>

        <div
          v-for="(i, index) in integrantes"
          :key="i.id"
          class="py-2 border-b last:border-b-0"
        >
          <p class="text-sm font-semibold mb-1">
            {{ i.nombre }} {{ i.apellido }}
          </p>

          <div class="flex gap-4 text-sm">

            <label class="flex items-center gap-1">
              <input
                type="radio"
                :name="`estado-${i.id}`"
                value="asistio"
                v-model="form.asistencias[index].estado"
              />
              Asistió
            </label>

            <label class="flex items-center gap-1">
              <input
                type="radio"
                :name="`estado-${i.id}`"
                value="falto"
                v-model="form.asistencias[index].estado"
              />
              Faltó
            </label>

            <label class="flex items-center gap-1">
              <input
                type="radio"
                :name="`estado-${i.id}`"
                value="justificada"
                v-model="form.asistencias[index].estado"
              />
              Justificada
            </label>

          </div>
        </div>

      </div>

      <!-- Evidencia -->
      <div class="mb-4">

        <label class="block text-sm font-medium mb-1">
          Evidencia documental (PDF)
        </label>

        <input
          type="file"
          accept="application/pdf"
          @change="onFileChange"
          class="w-full border rounded px-3 py-2"
        />

        <p
          v-if="errorArchivo"
          class="text-red-500 text-sm mt-1"
        >
          {{ errorArchivo }}
        </p>

      </div>

      <!-- Botones -->
      <div class="flex justify-end gap-2 pt-2">

        <button
          type="button"
          class="px-4 py-2 bg-gray-300 rounded"
          @click="emit('close')"
        >
          Cancelar
        </button>

        <button
          type="button"
          class="px-4 py-2 bg-gray-700 text-white rounded"
          @click="submitForm"
        >
          Guardar asistencia
        </button>

      </div>

    </div>
  </div>
</template>