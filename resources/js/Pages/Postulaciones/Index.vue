<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import Form from './Form.vue'

const props = defineProps({
  postulaciones: { type: Array, default: () => [] },
  consejos: { type: Array, default: () => [] }
})

const showModal = ref(false)
const showExpediente = ref(false)
const selected = ref(null)

const abrirExpediente = (p) => {
  selected.value = p
  showExpediente.value = true
}

const cerrarExpediente = () => {
  selected.value = null
  showExpediente.value = false
}

const estatusClass = (estatus) => {
  if (estatus === 'pendiente') return 'bg-yellow-100 text-yellow-800'
  if (estatus === 'aprobada') return 'bg-green-100 text-green-800'
  if (estatus === 'no_aprobada') return 'bg-red-100 text-red-800'
  return 'bg-gray-100 text-gray-800'
}

const estatusLabel = (estatus) => {
  if (estatus === 'pendiente') return 'Pendiente'
  if (estatus === 'aprobada') return 'Aprobada'
  if (estatus === 'no_aprobada') return 'No Aprobada'
  return 'Desconocido'
}
</script>

<template>
  <AuthenticatedLayout>

    <div class="mt-2 mb-4">
      <Link :href="route('dashboard')" class="text-gray-600 hover:underline">
        ← Volver a Consejos de Participación Ciudadana
      </Link>
    </div>

    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">
        Sección de Postulaciones
      </h1>
    </div>

    <div class="mb-4 flex justify-between">
      <Link :href="route('postulaciones.validacion')"
        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
        Panel de validación
      </Link>
    </div>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-4 flex justify-end">
          <button @click="showModal = true" class="px-4 py-2 text-white rounded-md" style="background-color:#636569;">
            Nueva postulación
          </button>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-6">

          <div v-if="!postulaciones.length" class="text-gray-500">
            No hay postulaciones registradas.
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-4 py-2 text-left text-sm font-semibold">Nombre</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold">Consejo</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold">Expediente</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold">Estatus</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold">Fecha Postulación</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold">Fecha Resolución</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-gray-200">
                <tr v-for="p in postulaciones" :key="p.id">

                  <td class="px-4 py-2">
                    {{ p.nombre }} {{ p.apellidos }}
                  </td>

                  <td class="px-4 py-2">
                    {{ p.consejo?.nombre }}
                  </td>

                  <td class="px-4 py-2">
                    <button @click="abrirExpediente(p)" class="text-indigo-600 hover:underline">
                      Ver expediente
                    </button>
                  </td>

                  <td class="px-4 py-2">
                    <span :class="['px-2 py-1 text-xs rounded-full', estatusClass(p.estatus)]">
                      {{ estatusLabel(p.estatus) }}
                    </span>
                  </td>

                  <td class="px-4 py-2 text-sm">
                    {{ p.fecha_postulacion
                      ? new Date(p.fecha_postulacion).toLocaleDateString()
                    : '-' }}
                  </td>

                  <td class="px-4 py-2 text-sm">
                    {{ p.fecha_validacion
                      ? new Date(p.fecha_validacion).toLocaleDateString()
                      : '-' }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

    <!-- Modal expediente -->
    <div v-if="showExpediente" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

      <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">

        <button @click="cerrarExpediente" class="absolute top-3 right-3 text-gray-500">✕</button>

        <h3 class="text-lg font-semibold mb-4">
          Expediente
        </h3>

        <div v-if="selected?.documentos?.length">

          <ul class="space-y-2">
            <li v-for="doc in selected.documentos" :key="doc.id"
              class="flex justify-between items-center border-b pb-1">

              <span class="text-sm font-medium capitalize">
                {{ doc.tipo.replaceAll('_', ' ') }}
              </span>

              <a :href="`/storage/${doc.archivo}`" target="_blank" class="text-indigo-600 hover:underline text-sm">
                Ver PDF
              </a>

            </li>
          </ul>

        </div>

        <div v-else class="text-gray-500">
          No hay documentos cargados.
        </div>

      </div>
    </div>

    <Form v-if="showModal" :consejos="consejos" @close="showModal = false" />

  </AuthenticatedLayout>
</template>