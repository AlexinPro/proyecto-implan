<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { CheckCircleIcon, XCircleIcon, BookmarkIcon, ArrowUpTrayIcon } from '@heroicons/vue/24/solid'
import { computed, watch } from 'vue'

const props = defineProps({
  consejo: Object,
  convocatorias: Array,
})

// Formulario
const form = useForm({
  tipo_sesion: '',
  fecha: '',
  documento: null,
  estado_convocatoria: false,
  estado_sesion: false,
})

// Regla: no se puede sesionar sin convocar
watch(() => form.estado_convocatoria, (val) => {
  if (!val) form.estado_sesion = false
})

// Mostrar documento solo si hay estado
const mostrarDocumento = computed(() => {
  return form.estado_convocatoria || form.estado_sesion
})

// Subir archivo
function handleFileChange(e) {
  form.documento = e.target.files[0]
}

// Guardar
function submit() {
  form.post(route('convocatorias.store', { consejo: props.consejo.id }), {
    forceFormData: true,
    onSuccess: () => form.reset(),
    onError: (errors) => console.log(errors),
  })
}

// Orden DESC (más reciente primero) sin mutar props
const convocatoriasOrdenadas = computed(() => {
  return [...props.convocatorias].sort(
    (a, b) => new Date(b.fecha) - new Date(a.fecha)
  )
})
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 bg-white rounded-lg shadow-md">
      <h1 class="text-2xl font-bold mb-4 text-gray-800">
        Consejo de Participación Ciudadana de {{ props.consejo.nombre }}
      </h1>

      <!-- FORMULARIO -->
      <form @submit.prevent="submit" class="space-y-4 mb-8">

        <!-- ESTADOS (primero) -->
        <div class="flex items-center gap-6">
          <label class="flex items-center space-x-2">
            <input
              type="checkbox"
              v-model="form.estado_convocatoria"
              class="rounded border-gray-300 text-green-600 focus:ring-green-500"
            />
            <span class="text-gray-700">Convocatoria realizada</span>
          </label>

          <label class="flex items-center space-x-2">
            <input
              type="checkbox"
              v-model="form.estado_sesion"
              :disabled="!form.estado_convocatoria"
              class="rounded border-gray-300 text-green-600 focus:ring-green-500"
            />
            <span class="text-gray-700">Sesión realizada</span>
          </label>
        </div>

        <!-- Tipo de sesión -->
        <div>
          <label class="block font-semibold mb-1 text-gray-700">Tipo de sesión</label>
          <select
            v-model="form.tipo_sesion"
            class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg p-2"
            required
          >
            <option disabled value="">Selecciona el tipo de sesión</option>
            <option value="ordinaria">Ordinaria</option>
            <option value="extraordinaria">Extraordinaria</option>
            <option value="solemne">Solemne</option>
          </select>
        </div>

        <!-- Fecha -->
        <div>
          <label class="block font-semibold mb-1 text-gray-700">Fecha</label>
          <input
            v-model="form.fecha"
            type="date"
            class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg p-2"
            required
          />
        </div>

        <!-- Documento (condicional) -->
        <div v-if="mostrarDocumento">
          <label class="block font-semibold mb-1 text-gray-700">Documento (PDF)</label>
          <div
            class="relative flex items-center border border-gray-300 rounded-lg p-2 bg-gray-50 hover:bg-gray-100 cursor-pointer transition"
          >
            <ArrowUpTrayIcon class="w-5 h-5 text-green-600 mr-2" />
            <label class="flex-1 cursor-pointer text-gray-700">
              <span v-if="!form.documento">Seleccionar archivo...</span>
              <span v-else>{{ form.documento.name }}</span>
              <input
                type="file"
                @change="handleFileChange"
                class="hidden"
                accept=".pdf,.doc,.docx"
              />
            </label>
          </div>
        </div>

        <!-- Guardar -->
        <div class="flex justify-end">
          <button
            type="submit"
            class="flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white font-semibold rounded-xl shadow-md hover:bg-green-700 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-150"
          >
            <BookmarkIcon class="w-5 h-5" />
            Guardar
          </button>
        </div>
      </form>
    </div>

    <!-- TABLA -->
    <div class="p-6 bg-white rounded-lg shadow-md">
      <table class="min-w-full border">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 text-left text-gray-700">Tipo de sesión</th>
            <th class="p-2 text-left text-gray-700">Fecha</th>
            <th class="p-2 text-left text-gray-700">Documento</th>
            <th class="p-2 text-center text-gray-700">Convocatoria</th>
            <th class="p-2 text-center text-gray-700">Sesión</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in convocatoriasOrdenadas"
            :key="item.id"
            class="border-t hover:bg-gray-50 transition"
          >
            <td class="p-2 capitalize">{{ item.tipo_sesion }}</td>
            <td class="p-2">{{ item.fecha }}</td>
            <td class="p-2">
              <a
                v-if="item.documento"
                :href="`/storage/${item.documento}`"
                target="_blank"
                class="text-green-600 hover:text-green-800 hover:underline font-medium"
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
