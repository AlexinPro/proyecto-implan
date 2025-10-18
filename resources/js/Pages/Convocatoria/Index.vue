<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { CheckCircleIcon, XCircleIcon, CloudArrowUpIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  consejo: Object,
  convocatorias: Array,
})

// Formulario
const form = useForm({
  nombre: '',
  fecha: '',
  documento: null,
  estado_convocatoria: false,
  estado_sesion: false,
})

// Subir archivo
function handleFileChange(e) {
  form.documento = e.target.files[0]
}

// Guardar convocatoria
function submit() {
  form.post(route('convocatorias.store', props.consejo.id), {
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 bg-white rounded-lg shadow-md">
      <h1 class="text-2xl font-bold mb-4">
        Consejo de Participación Ciudadana de {{ props.consejo.nombre }}
      </h1>

      <!-- Formulario -->
      <form @submit.prevent="submit" class="space-y-4 mb-6">
        <div>
          <label class="block font-semibold">Nombre de la convocatoria</label>
          <input v-model="form.nombre" type="text" class="w-full border rounded p-2" />
        </div>

        <div>
          <label class="block font-semibold">Fecha</label>
          <input v-model="form.fecha" type="date" class="w-full border rounded p-2" />
        </div>

        <div>
          <label class="block font-semibold">Documento</label>
          <input type="file" @change="handleFileChange" class="w-full border rounded p-2" />
        </div>

        <div class="flex items-center gap-6">
          <label class="flex items-center space-x-2">
            <input type="checkbox" v-model="form.estado_convocatoria" />
            <span>Convocatoria realizada</span>
          </label>
          <label class="flex items-center space-x-2">
            <input type="checkbox" v-model="form.estado_sesion" />
            <span>Sesión realizada</span>
          </label>
        </div>
        

        <button type="submit" class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
          <CloudArrowUpIcon class="w-5 h-5 mr-2" />
          Guardar 
        </button>
      </form>
    </div>

      <!-- Tabla -->
      <div class="p-6 bg-white rounded-lg shadow-md">
        <table class="min-w-full border">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-2 text-left">Nombre</th>
              <th class="p-2 text-left">Fecha</th>
              <th class="p-2 text-left">Documento</th>
              <th class="p-2 text-center">Convocatoria</th>
              <th class="p-2 text-center">Sesión</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in convocatorias" :key="item.id" class="border-t">
              <td class="p-2">{{ item.nombre }}</td>
              <td class="p-2">{{ item.fecha }}</td>
              <td class="p-2">
                <a
                  v-if="item.documento"
                  :href="`/storage/${item.documento}`"
                  target="_blank"
                  class="text-indigo-600 hover:underline"
                >
                  Ver documento
                </a>
                <span v-else class="text-gray-400">Sin documento</span>
              </td>
              <td class="p-2 text-center">
                <CheckCircleIcon v-if="item.estado_convocatoria" class="text-green-500 w-6 h-6 mx-auto" />
                <XCircleIcon v-else class="text-red-500 w-6 h-6 mx-auto" />
              </td>
              <td class="p-2 text-center">
                <CheckCircleIcon v-if="item.estado_sesion" class="text-green-500 w-6 h-6 mx-auto" />
                <XCircleIcon v-else class="text-red-500 w-6 h-6 mx-auto" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    
  </AuthenticatedLayout>
</template>
