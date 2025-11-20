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

// ✅ Abrir modal
function abrirModal(integrante) {
  integranteSeleccionado.value = integrante
  showModal.value = true
}

// ✅ Cerrar modal
function cerrarModal() {
  showModal.value = false
  integranteSeleccionado.value = null
}

// ✅ Agrupar en fórmulas de 2
const formulas = computed(() => {
  const grouped = []
  for (let i = 0; i < props.integrantes.length; i += 2) {
    grouped.push(props.integrantes.slice(i, i + 2))
  }
  return grouped
})

// ✅ Asistencias de un integrante
function obtenerAsistencias(id) {
  return props.asistencias.filter(a => a.integrante_id === id)
}

// ✅ SEMÁFORO — reglas completas
function obtenerSemaforo(integranteId) {
  const registros = props.asistencias
    .filter(a => a.integrante_id === integranteId)
    .sort((a, b) => new Date(a.fecha) - new Date(b.fecha))

  if (!registros.length) return 'verde'

  const faltas = registros.filter(a => !a.asistio)

  if (faltas.length === 0) return 'verde'

  // 1) Dos faltas → Amarillo
  if (faltas.length === 2) return 'amarillo'

  // 2) Tres faltas no seguidas → Amarillo
  if (faltas.length === 3) {
    let consecutivas = true
    for (let i = 1; i < faltas.length; i++) {
      const diff = (new Date(faltas[i].fecha) - new Date(faltas[i - 1].fecha)) / (1000 * 60 * 60 * 24)
      if (diff > 40) consecutivas = false
    }
    if (!consecutivas) return 'amarillo'
  }

  // 3) Tres consecutivas → Rojo
  if (faltas.length >= 3) {
    let consecutivas = true
    for (let i = 1; i < faltas.length; i++) {
      const diff = (new Date(faltas[i].fecha) - new Date(faltas[i - 1].fecha)) / (1000 * 60 * 60 * 24)
      if (diff > 40) consecutivas = false
    }
    if (consecutivas) return 'rojo'
  }

  // 4) Cuatro faltas totales → Rojo
  if (faltas.length >= 4) return 'rojo'

  return 'verde'
}

// ✅ Clase CSS para el color del semáforo
function colorClase(color) {
  return {
    verde: "bg-green-500",
    amarillo: "bg-yellow-400",
    rojo: "bg-red-500",
  }[color] || "bg-gray-300"
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6">

      <h1 class="text-2xl font-bold mb-2">
        Registro de asistencias — {{ props.consejo.nombre }}
      </h1>

      <p class="text-gray-600">{{ props.consejo.descripcion }}</p>

      <br />

      <!-- Tabla de fórmulas -->
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

              <!-- # de fórmula -->
              <td class="px-4 py-2 border text-center font-semibold">
                {{ index + 1 }}
              </td>

              <!-- Integrantes con semáforo -->
              <td class="px-4 py-2 border">

                <!-- Integrante 1 -->
                <div v-if="f[0]" class="flex items-center gap-2">
                  <span class="w-3 h-3 rounded-full" :class="colorClase(obtenerSemaforo(f[0].id))"></span>
                  {{ f[0].nombre }} {{ f[0].apellido }}
                </div>
                <div v-else class="text-gray-400">Pendiente</div>

                <br />

                <!-- Integrante 2 -->
                <div v-if="f[1]" class="flex items-center gap-2">
                  <span class="w-3 h-3 rounded-full" :class="colorClase(obtenerSemaforo(f[1].id))"></span>
                  {{ f[1].nombre }} {{ f[1].apellido }}
                </div>
                <div v-else class="text-gray-400">Pendiente</div>

              </td>

              <!-- Asistencias -->
              <td class="px-4 py-2 border text-sm">

                <!-- Integrante 1 -->
                <div v-if="f[0]">
                  <strong class="text-gray-700">Historial:</strong>
                  <ul>
                    <li v-for="a in obtenerAsistencias(f[0].id)" :key="a.id">
                      {{ a.tipo_sesion }} – {{ a.mes }} –
                      <span :class="a.asistio ? 'text-green-600' : 'text-red-500'">
                        {{ a.asistio ? 'Asistió' : 'Faltó' }}
                      </span>
                    </li>
                  </ul>

                  <div class="mt-1 flex items-center gap-2">
                    <span class="font-semibold">Semáforo:</span>
                    <span class="w-3 h-3 rounded-full" :class="colorClase(obtenerSemaforo(f[0].id))"></span>
                  </div>

                  <span v-if="!obtenerAsistencias(f[0].id).length" class="text-gray-400">Sin registros</span>
                </div>

                <hr class="my-3" />

                <!-- Integrante 2 -->
                <div v-if="f[1]">
                  <strong class="text-gray-700">Historial:</strong>
                  <ul>
                    <li v-for="a in obtenerAsistencias(f[1].id)" :key="a.id">
                      {{ a.tipo_sesion }} – {{ a.mes }} –
                      <span :class="a.asistio ? 'text-green-600' : 'text-red-500'">
                        {{ a.asistio ? 'Asistió' : 'Faltó' }}
                      </span>
                    </li>
                  </ul>

                  <div class="mt-1 flex items-center gap-2">
                    <span class="font-semibold">Semáforo:</span>
                    <span class="w-3 h-3 rounded-full" :class="colorClase(obtenerSemaforo(f[1].id))"></span>
                  </div>

                  <span v-if="!obtenerAsistencias(f[1].id).length" class="text-gray-400">Sin registros</span>
                </div>

              </td>

              <!-- Acciones -->
              <td class="px-6 py-4 border text-sm font-medium">

                <!-- Integrante 1 -->
                <div v-if="f[0]" class="mb-2">
                  <button @click="abrirModal(f[0])"
                    class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-800">
                    Registrar asistencia
                  </button>
                </div>

                <!-- Integrante 2 -->
                <div v-if="f[1]">
                  <button @click="abrirModal(f[1])"
                    class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-800">
                    Registrar asistencia
                  </button>
                </div>

              </td>

            </tr>
          </tbody>
        </table>
      </div>

      <p v-else class="text-gray-500">No hay integrantes registrados.</p>

      <!-- Modal -->
      <Form v-if="showModal" :integrante="integranteSeleccionado" :consejo-id="props.consejo.id"
        @close="cerrarModal" />

    </div>
  </AuthenticatedLayout>
</template>