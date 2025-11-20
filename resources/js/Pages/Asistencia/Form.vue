<script setup>
import { unref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  consejoId: {
    type: [Number, String],
    required: true,
  },
  integrante: {
    type: [Object, Function],
    default: null,
  }
})

// 👇 Emit correcto (coincide con el @close del index)
const emit = defineEmits(['close'])

const integranteActual = computed(() => unref(props.integrante))

const form = useForm({
  integrante_id: integranteActual.value ? integranteActual.value.id : '',
  tipo_sesion: '',
  fecha: '',
  asistio: true,
})

if (integranteActual.value) {
  form.integrante_id = integranteActual.value.id
}

function submitForm() {
  const integranteObj = integranteActual.value
  if (!integranteObj || !integranteObj.id) {
    console.error('No se encontró el integrante seleccionado.')
    return
  }
  if (!props.consejoId) {
    console.error('No se proporcionó consejoId.')
    return
  }

  form.integrante_id = integranteObj.id

  form.post(route('asistencias.store', { consejo: props.consejoId }), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      emit('close') // 👈 Cierra el modal al guardar
    },
    onError: (errors) => {
      console.warn('Errores de validación:', errors)
    }
  })
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
      <h2 class="text-lg font-bold mb-3">Registrar Asistencia</h2>

      <p class="text-sm text-gray-700 mb-4">
        <strong>Integrante:</strong>
        {{ integranteActual ? (integranteActual.nombre + ' ' + integranteActual.apellido) : '—' }}
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
          <div v-if="form.errors.tipo_sesion" class="text-red-500 text-sm">{{ form.errors.tipo_sesion }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Fecha</label>
          <input type="date" v-model="form.fecha" class="w-full border rounded px-3 py-2" />
          <div v-if="form.errors.fecha" class="text-red-500 text-sm">{{ form.errors.fecha }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Asistió</label>
          <select v-model="form.asistio" class="w-full border rounded px-3 py-2">
            <option :value="true">Sí</option>
            <option :value="false">No</option>
          </select>
          <div v-if="form.errors.asistio" class="text-red-500 text-sm">{{ form.errors.asistio }}</div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <!-- 👇 Emite el evento close al cancelar -->
          <button type="button" @click="$emit('close')" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</template>
