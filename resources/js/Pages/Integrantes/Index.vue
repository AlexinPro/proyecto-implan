<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { DocumentIcon } from '@heroicons/vue/24/solid'

import Form from './Form.vue'

const props = defineProps({
  consejo: Object,
  integrantes: Array
})

const showForm = ref(false)
const editingId = ref(null)

// Formulario base
const form = useForm({
  nombre: '',
  apellido: '',
  puesto: '',
  correo: '',
  genero: '',
  colonia: '',
  discapacidad: false,
  discapacidad_tipo: '',
  consejo_id: props.consejo.id,
})

// Abrir modal para nuevo integrante
function openForm() {
  editingId.value = null
  form.reset()
  form.discapacidad = false
  form.discapacidad_tipo = ''
  showForm.value = true
}

// Abrir modal para editar integrante
function editIntegrante(integrante) {
  editingId.value = integrante.id
  form.nombre = integrante.nombre
  form.apellido = integrante.apellido
  form.puesto = integrante.puesto
  form.correo = integrante.correo
  form.genero = integrante.genero
  form.colonia = integrante.colonia
  form.discapacidad = integrante.discapacidad ? true : false
  form.discapacidad_tipo = integrante.discapacidad_tipo ?? ''
  form.consejo_id = integrante.consejo_id
  showForm.value = true
}

// Guardar (crear o actualizar)
function submitForm() {
  // Convertir boolean a string para que pase validación en Laravel
  form.discapacidad = form.discapacidad ? 'si' : 'no'

  if (editingId.value) {
    form.put(route('integrantes.update', editingId.value), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        showForm.value = false
      }
    })
  } else {
    form.post(route('integrantes.store'), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        showForm.value = false
      }
    })
  }
}


// Eliminar integrante
function deleteIntegrante(id) {
  if (confirm('¿Seguro que deseas eliminar este integrante?')) {
    form.delete(route('integrantes.destroy', id))
  }
}

// Agrupar integrantes en fórmulas de 2
const formulas = computed(() => {
  const grouped = []
  for (let i = 0; i < props.integrantes.length; i += 2) {
    grouped.push(props.integrantes.slice(i, i + 2))
  }
  return grouped
})
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6">

      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">
          Integrantes del Consejo de Participación Ciudadana de {{ consejo.nombre }}
        </h1>
        <button @click="openForm()" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-500">
          + Agregar Integrante
        </button>
      </div>

      <p class="text-gray-700">{{ consejo.descripcion }}</p>
      <br />

      <!-- Tabla de fórmulas -->
      <div v-if="formulas.length" class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 rounded-lg">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 border"># Fórmula</th>
              <th class="px-4 py-2 border">Integrantes</th>
              <th class="px-4 py-2 border">Cargo</th>

              <!-- NUEVAS COLUMNAS -->
              <th class="px-4 py-2 border">Género</th>
              <th class="px-4 py-2 border">Discapacidad</th>

              <th class="px-4 py-2 border">Acciones</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(f, i) in formulas" :key="i" class="hover:bg-gray-50">
              <td class="px-4 py-2 border text-center">{{ i + 1 }}</td>

              <!-- INTEGRANTES -->
              <td class="px-4 py-2 border">
                <span v-if="f[0]">{{ f[0].nombre }} {{ f[0].apellido }}</span>
                <span v-else class="text-gray-400">Pendiente</span>
                <br />

                <span v-if="f[1]">{{ f[1].nombre }} {{ f[1].apellido }}</span>
                <span v-else class="text-gray-400">Pendiente</span>
              </td>

              <!-- CARGOS -->
              <td class="px-4 py-2 border">
                <span v-if="f[0]">{{ f[0].puesto }}</span>
                <span v-else class="text-gray-400">Pendiente</span>
                <br />

                <span v-if="f[1]">{{ f[1].puesto }}</span>
                <span v-else class="text-gray-400">Pendiente</span>
              </td>

              <!-- GÉNERO -->
              <td class="px-4 py-2 border">
                <span v-if="f[0]">{{ f[0].genero }}</span>
                <span v-else class="text-gray-400">Pendiente</span>
                <br />

                <span v-if="f[1]">{{ f[1].genero }}</span>
                <span v-else class="text-gray-400">Pendiente</span>
              </td>

              <!-- DISCAPACIDAD -->
              <td class="px-4 py-2 border">
                <span v-if="f[0]">
                  <span v-if="f[0].discapacidad === 'si'">
                    Sí: {{ f[0].discapacidad_tipo }}
                  </span>
                  <span v-else>No</span>
                </span>
                <span v-else class="text-gray-400">Pendiente</span>
                <br />

                <span v-if="f[1]">
                  <span v-if="f[1].discapacidad === 'si'">
                    Sí: {{ f[1].discapacidad_tipo }}
                  </span>
                  <span v-else>No</span>
                </span>
                <span v-else class="text-gray-400">Pendiente</span>
              </td>

              <!-- ACCIONES -->
              <td class="px-4 py-2 border">
                <div v-if="f[0]" class="flex items-center space-x-2">
                  <button @click="editIntegrante(f[0])"
                    class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700">
                    Editar
                  </button>
                  <button @click="deleteIntegrante(f[0].id)"
                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700">
                    Eliminar
                  </button>
                  <button @click="$inertia.get(route('docu.index', f[0].id))"
                    class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-700 flex items-center">
                    <DocumentIcon class="w-5 h-5 mr-1" />
                    Documentos
                  </button>
                </div>

                <div v-if="f[1]" class="flex items-center space-x-2 mt-2">
                  <button @click="editIntegrante(f[1])"
                    class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700">
                    Editar
                  </button>
                  <button @click="deleteIntegrante(f[1].id)"
                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700">
                    Eliminar
                  </button>
                  <button @click="$inertia.get(route('docu.index', f[1].id))"
                    class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-700 flex items-center">
                    <DocumentIcon class="w-5 h-5 mr-1" />
                    Documentos
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

      </div>

      <p v-else class="text-gray-500">No hay integrantes registrados.</p>

    </div>

    <!-- Formulario como componente -->
    <Form :show="showForm" :form="form" :editingId="editingId" @close="showForm = false" @submit="submitForm" />

  </AuthenticatedLayout>
</template>
