<script setup>
import { computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  consejoId: {
    type: [Number, String],
    required: true,
  },
  integrantes: {
    type: Array,
    required: true,
  },
  fecha: {
    type: String,
    required: true,
  },
})

const emit = defineEmits(['close'])

const form = useForm({
  integrante_id: '',
  tipo_sesion: '',
  fecha: props.fecha,
  asistio: true,
  //evidencia: null,
})

// si se abre el modal con otra fecha, se actualiza el form
watch(() => props.fecha, (value) => {
  form.fecha = value
})

function submitForm() {
  form.post(route('asistencias.store', { consejo: props.consejoId }), {
    forceFormData: true, //  Necesario para enviar archivos
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      emit('close') // Cerrar modal al guardar
    },
  })
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">

      <h2 class="text-lg font-bold mb-4">Registrar asistencia</h2>

      <form @submit.prevent="submitForm" class="space-y-4">

        <!-- SELECT INTEGRANTE -->
        <div>
          <label class="block text-sm font-medium mb-1">Integrante</label>
          <select v-model="form.integrante_id" class="w-full border rounded px-3 py-2">
            <option disabled value="">Seleccione un integrante</option>
            <option v-for="i in props.integrantes" :key="i.id" :value="i.id">
              {{ i.nombre }} {{ i.apellido }}
            </option>
          </select>
          <div v-if="form.errors.integrante_id" class="text-red-500 text-sm">
            {{ form.errors.integrante_id }}
          </div>
        </div>

        <!-- TIPO SESIÓN -->
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

        <!-- FECHA -->
        <div>
          <label class="block text-sm font-medium mb-1">Fecha</label>
          <input type="date" v-model="form.fecha" class="w-full border rounded px-3 py-2" />
          <div v-if="form.errors.fecha" class="text-red-500 text-sm">
            {{ form.errors.fecha }}
          </div>
        </div>

        <!-- ASISTIO -->
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

       

        <!-- BOTONES -->
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 bg-gray-300 rounded">
            Cancelar
          </button>

          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
            Guardar
          </button>
        </div>
      </form>

    </div>
  </div>
</template>
