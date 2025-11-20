<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, computed } from 'vue'
import Form from './Form.vue'

const props = defineProps({
  consejo: Object,
  integrantes: Array,
  asistencias: Array
})

const showModal = ref(false)
const integranteSeleccionado = ref(null)

// 🔹 Abrir el modal con el integrante seleccionado
function abrirModal(integrante) {
  integranteSeleccionado.value = integrante
  showModal.value = true
}

// 🔹 Cerrar modal
function cerrarModal() {
  showModal.value = false
  integranteSeleccionado.value = null
}

// 🔹 Agrupar integrantes en fórmulas de 2
const formulas = computed(() => {
  const grouped = []
  for (let i = 0; i < props.integrantes.length; i += 2) {
    grouped.push(props.integrantes.slice(i, i + 2))
  }
  return grouped
})

// 🔹 Obtener asistencias por integrante
function obtenerAsistencias(integranteId) {
  return props.asistencias.filter(a => a.integrante_id === integranteId)
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">
          Registro de asistencias del Consejo {{ props.consejo.nombre }}
        </h1>
      </div>

      <p class="text-gray-700">{{ props.consejo.descripcion }}</p>

      <br />

      <!-- Tabla -->
      <div v-if="formulas.length" class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 rounded-lg">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 border"># Fórmula</th>
              <th class="px-4 py-2 border">Integrantes</th>
              <th class="px-4 py-2 border">Asistencias</th>
              <th class="px-4 py-2 border">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(f, index) in formulas" :key="index" class="hover:bg-gray-50">
              <!-- Número -->
              <td class="px-4 py-2 border text-center">{{ index + 1 }}</td>

              <!-- Integrantes -->
              <td class="px-4 py-2 border">
                <div v-if="f[0]">
                  {{ f[0].nombre }} {{ f[0].apellido }}
                </div>
                <div v-else class="text-gray-400">Pendiente</div>

                <div v-if="f[1]">
                  {{ f[1].nombre }} {{ f[1].apellido }}
                </div>
                <div v-else class="text-gray-400">Pendiente</div>
              </td>

              <!-- Asistencias -->
              <td class="px-4 py-2 border text-sm">
                <div v-if="f[0]">
                  <ul>
                    <li v-for="a in obtenerAsistencias(f[0].id)" :key="a.id">
                      {{ a.tipo_sesion }} - {{ a.mes }} - 
                      <span :class="a.asistio ? 'text-green-600' : 'text-red-500'">
                        {{ a.asistio ? 'Asistió' : 'Faltó' }}
                      </span>
                    </li>
                  </ul>
                  <span v-if="!obtenerAsistencias(f[0].id).length" class="text-gray-400">Sin registros</span>
                </div>

                <hr class="my-2 border-gray-300" />

                <div v-if="f[1]">
                  <ul>
                    <li v-for="a in obtenerAsistencias(f[1].id)" :key="a.id">
                      {{ a.tipo_sesion }} - {{ a.mes }} - 
                      <span :class="a.asistio ? 'text-green-600' : 'text-red-500'">
                        {{ a.asistio ? 'Asistió' : 'Faltó' }}
                      </span>
                    </li>
                  </ul>
                  <span v-if="!obtenerAsistencias(f[1].id).length" class="text-gray-400">Sin registros</span>
                </div>
              </td>

              <!-- Acciones -->
              <td class="px-6 py-4 border text-sm font-medium">
                <div v-if="f[0]" class="mb-2">
                  <button
                    @click="abrirModal(f[0])"
                    class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-800"
                  >
                    Registrar asistencia
                  </button>
                </div>

                <div v-if="f[1]">
                  <button
                    @click="abrirModal(f[1])"
                    class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-800"
                  >
                    Registrar asistencia
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p v-else class="text-gray-500">
        No hay integrantes registrados en este consejo.
      </p>

      <!-- Modal -->
      <Form
        v-if="showModal"
        :integrante="integranteSeleccionado"
        :consejo-id="props.consejo.id"
        @close="cerrarModal"
      />
    </div>
  </AuthenticatedLayout>
</template>
