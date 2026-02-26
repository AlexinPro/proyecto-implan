<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, watchEffect } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const page = usePage()

const props = defineProps({
  postulaciones: { type: Array, default: () => [] }
})

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

/* Flash messages */
watchEffect(() => {
  if (page.props.flash?.success) {
    Swal.fire({
      icon: 'success',
      title: 'Correcto',
      text: page.props.flash.success
    })
  }
  if (page.props.flash?.error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: page.props.flash.error
    })
  }
})

const aprobar = (id) => {
  Swal.fire({
    title: 'Fecha resolutiva',
    input: 'date',
    inputLabel: 'Capture la fecha de resolución',
    inputValidator: (value) => {
      if (!value) return 'Debe capturar la fecha'
    },
    showCancelButton: true,
    confirmButtonText: 'Aprobar'
  }).then(result => {
    if (!result.isConfirmed) return

    router.patch(
      route('postulaciones.aprobar', id),
      { fecha_validacion: result.value }
    )
  })
}

const rechazar = (id) => {
  Swal.fire({
    title: 'Fecha resolutiva',
    input: 'date',
    inputLabel: 'Seleccione la fecha de resolución',
    inputValidator: (value) => {
      if (!value) return 'Debe capturar la fecha'
    },
    showCancelButton: true,
    confirmButtonText: 'Rechazar'
  }).then(result => {
    if (!result.isConfirmed) return

    router.patch(
      route('postulaciones.rechazar', id),
      { fecha_validacion: result.value }
    )
  })
}
</script>

<template>
  <AuthenticatedLayout>

    <template #header>
      <h2 class="text-xl font-semibold">Panel de Validación</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div v-if="!postulaciones.length" class="text-gray-500">
          No hay postulaciones pendientes.
        </div>

        <div v-else class="bg-white shadow sm:rounded-lg p-6">

          <table class="min-w-full divide-y divide-gray-200">

            <thead>
              <tr>
                <th class="px-4 py-2 text-left">Nombre</th>
                <th class="px-4 py-2 text-left">Consejo</th>
                <th class="px-4 py-2 text-left">Expediente</th>
                <th class="px-4 py-2 text-left">Resolución</th>
                <th class="px-4 py-2 text-left">Estatus</th>
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

                <td class="px-4 py-2 space-x-3">

                  <button @click="aprobar(p.id)" class="text-green-600 font-bold text-lg">
                    ✔
                  </button>

                  <button @click="rechazar(p.id)" class="text-red-600 font-bold text-lg">
                    ✖
                  </button>

                </td>

                <td class="px-4 py-2">
                  <span :class="['px-2 py-1 text-xs rounded-full', estatusClass(p.estatus)]">
                    {{ estatusLabel(p.estatus) }}
                  </span>
                </td>

              </tr>

            </tbody>
          </table>

        </div>
      </div>
    </div>

    <!-- Modal Expediente -->
    <div v-if="showExpediente" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

      <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">

        <button @click="cerrarExpediente" class="absolute top-3 right-3 text-gray-500">
          ✕
        </button>

        <h3 class="text-lg font-semibold mb-4">
          Expediente
        </h3>

        <div v-if="selected?.documentos?.length">

          <ul class="space-y-2">

            <li v-for="doc in selected.documentos" :key="doc.id"
              class="flex justify-between items-center border-b pb-1">

              <span class="text-sm capitalize">
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

  </AuthenticatedLayout>
</template>