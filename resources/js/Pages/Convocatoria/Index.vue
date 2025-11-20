<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { CheckCircleIcon, XCircleIcon, BookmarkIcon } from '@heroicons/vue/24/solid'

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
      <h1 class="text-2xl font-bold mb-4 text-gray-800">
        Consejo de Participación Ciudadana de {{ props.consejo.nombre }}
      </h1>

      <!-- Formulario -->
      <form @submit.prevent="submit" class="space-y-4 mb-8">
        <div>
          <label class="block font-semibold mb-1 text-gray-700">Nombre de la convocatoria</label>
          <input v-model="form.nombre" type="text" class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg p-2" />
        </div>

        <div>
          <label class="block font-semibold mb-1 text-gray-700">Fecha</label>
          <input v-model="form.fecha" type="date" class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg p-2" />
        </div>

        <div>
          <label class="block font-semibold mb-1 text-gray-700">Documento (PDF)</label>
          <div class="relative flex items-center border border-gray-300 rounded-lg p-2 bg-gray-50 hover:bg-gray-100 cursor-pointer transition">
            <ArrowUpTrayIcon class="w-5 h-5 text-green-600 mr-2" />
            <label class="flex-1 cursor-pointer text-gray-700">
              <span v-if="!form.documento">Seleccionar archivo...</span>
              <span v-else>{{ form.documento.name }}</span>
              <input type="file" @change="handleFileChange" class="hidden" accept=".pdf,.doc,.docx" />
            </label>
          </div>
        </div>

        <div class="flex items-center gap-6">
          <label class="flex items-center space-x-2">
            <input type="checkbox" v-model="form.estado_convocatoria" class="rounded border-gray-300 text-green-600 focus:ring-green-500" />
            <span class="text-gray-700">Convocatoria realizada</span>
          </label>
          <label class="flex items-center space-x-2">
            <input type="checkbox" v-model="form.estado_sesion" class="rounded border-gray-300 text-green-600 focus:ring-green-500" />
            <span class="text-gray-700">Sesión realizada</span>
          </label>
        </div>

        <!-- Botón Guardar -->
        <div class="flex justify-end">
          <button
            type="submit"
            class="flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white font-semibold rounded-xl shadow-md hover:bg-green-700 hover:shadow-lg 
            transform hover:-translate-y-0.5 transition-all duration-150">
            <BookmarkIcon class="w-5 h-5" />
            Guardar
          </button>
        </div>
      </form>
    </div>

    <!-- Tabla -->
    <div class="p-6 bg-white rounded-lg shadow-md">
      <table class="min-w-full border">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 text-left text-gray-700">Nombre</th>
            <th class="p-2 text-left text-gray-700">Fecha</th>
            <th class="p-2 text-left text-gray-700">Documento</th>
            <th class="p-2 text-center text-gray-700">Convocatoria</th>
            <th class="p-2 text-center text-gray-700">Sesión</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in convocatorias" :key="item.id" class="border-t hover:bg-gray-50 transition">
            <td class="p-2">{{ item.nombre }}</td>
            <td class="p-2">{{ item.fecha }}</td>
            <td class="p-2">
              <a
                v-if="item.documento"
                :href="`/storage/${item.documento}`"
                target="_blank"
                class="text-green-600 hover:text-green-800 hover:underline font-medium">
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
