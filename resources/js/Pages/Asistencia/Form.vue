<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  consejo: Object,
  integrante: Object
})

const emit = defineEmits(['cerrar'])

const form = useForm({
  integrante_id: props.integrante.id,
  tipo_sesion: '',
  fecha: '',
  asistio: true,
})

// Generar el nombre completo
const nombreCompleto = computed(() =>
  `${props.integrante.nombre} ${props.integrante.apellido}`
)

function submitForm() {
  form.post(route('asistencias.store', { consejo: props.consejo.id }), {
    onSuccess: () => {
      form.reset()
      emit('cerrar')
    }
  })
}
</script>

<template>
  <!-- Fondo oscuro -->
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
      <h2 class="text-lg font-bold mb-4 text-center">Registrar Asistencia</h2>

      <p class="mb-2 text-gray-700 text-center">
        <strong>Integrante:</strong> {{ nombreCompleto }}
      </p>

      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Tipo de Sesión</label>
          <select v-model="form.tipo_sesion" class="w-full border rounded px-3 py-2">
            <option disabled value="">Seleccione tipo</option>
            <option value="ordinaria">Ordinaria</option>
            <option value="solemne">Solemne</option>
            <option value="extraordinaria">Extraordinaria</option>
          </select>
          <div v-if="form.errors.tipo_sesion" class="text-red-500 text-sm">
            {{ form.errors.tipo_sesion }}
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Fecha</label>
          <input type="date" v-model="form.fecha" class="w-full border rounded px-3 py-2" />
          <div v-if="form.errors.fecha" class="text-red-500 text-sm">
            {{ form.errors.fecha }}
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Asistió</label>
          <select v-model="form.asistio" class="w-full border rounded px-3 py-2">
            <option :value="true">Sí</option>
            <option :value="false">No</option>
          </select>
          <div v-if="form.errors.asistio" class="text-red-500 text-sm">
            {{ form.errors.asistio }}
          </div>
        </div>

        <div class="flex justify-end space-x-2 pt-4">
          <button
            type="button"
            @click="$emit('cerrar')"
            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-800"
          >
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
