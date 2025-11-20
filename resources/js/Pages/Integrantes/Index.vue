<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { DocumentIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  consejo: Object,
  integrantes: Array
})

const showForm = ref(false)
const editingId = ref(null) //saber si se está editando

//formulario base para inregrante
const form = useForm({
  nombre: '',
  apellido: '',
  puesto: '',
  correo: '',
  consejo_id: props.consejo.id,
})

//funcion show para abrir el modal
function openForm(consejoId) {
  editingId.value = null
  form.reset()
  form.consejo_id = consejoId
  showForm.value = true
}

// Abrir modal para editar un integrante
function editIntegrante(integrante) {
  editingId.value = integrante.id
  form.nombre = integrante.nombre
  form.apellido = integrante.apellido
  form.puesto = integrante.puesto
  form.correo = integrante.correo
  form.consejo_id = integrante.consejo_id
  showForm.value = true
}

// Guardar (crear o actualizar)
function submitForm() {
  if (editingId.value) {
    form.put(route('integrantes.update', editingId.value), {
      onSuccess: () => {
        form.reset()
        showForm.value = false
      }
    })
  } else {
    form.post(route('integrantes.store'), {
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
      <button @click="openForm(consejo.id)" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-500">
        + Agregar Integrante
      </button>
    </div>

    <p class="text-gray-700">{{ consejo.descripcion }}</p>

    <br />

    <!-- Tabla -->
    <div v-if="formulas.length" class="overflow-x-auto">
      <table class="min-w-full border border-gray-300 rounded-lg">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 border"># Fórmula</th>
            <th class="px-4 py-2 border">Integrantes</th>
            <th class="px-4 py-2 border">Cargo a ejercer</th>
            <th class="px-4 py-2 border">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(f, index) in formulas" :key="index" class="hover:bg-gray-50">
            <!-- Número -->
            <td class="px-4 py-2 border text-center">{{ index + 1 }}</td>

            <!-- Nombres -->
            <td class="px-4 py-2 border">
              <span v-if="f[0]">{{ f[0].nombre }} {{ f[0].apellido }}</span>
              <span v-else class="text-gray-400">Pendiente</span>
              <br />
              <span v-if="f[1]">{{ f[1].nombre }} {{ f[1].apellido }}</span>
              <span v-else class="text-gray-400">Pendiente</span>
            </td>

            <!-- Puestos -->
            <td class="px-4 py-2 border">
              <span v-if="f[0]">{{ f[0].puesto }}</span>
              <span v-else class="text-gray-400">Pendiente</span>
              <br />
              <span v-if="f[1]">{{ f[1].puesto }}</span>
              <span v-else class="text-gray-400">Pendiente</span>
            </td>

            <!-- Acciones -->
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <div v-if="f[0]" class="flex items-center space-x-2">
                <button @click="editIntegrante(f[0])" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700">
                  Editar
                </button>
                <button @click="deleteIntegrante(f[0].id)" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700">
                  Eliminar
                </button>
                <button @click="$inertia.get(route('docu.index', f[0].id))" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-700 flex items-center">
                  <DocumentIcon class="w-5 h-5 mr-1" />
                  Documentos
                </button>  
              </div>

              <div v-if="f[1]" class="flex items-center space-x-2 mt-2">
                <button @click="editIntegrante(f[1])" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700">
                  Editar
                </button>
                <button @click="deleteIntegrante(f[1].id)" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700">
                  Eliminar
                </button>
                <button @click="$inertia.get(route('docu.index', f[1].id))" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-700 flex items-center">
                  <DocumentIcon class="w-5 h-5 mr-1" />
                  Documentos
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <p v-else class="text-gray-500">No hay integrantes registrados en este consejo.</p>

    <!-- Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-bold mb-4">
          {{ editingId ? 'Editar Integrante' : 'Nuevo Integrante' }}
        </h2>

        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <label class="block text-sm font-medium">Nombre</label>
            <input v-model="form.nombre" type="text" class="w-full border rounded px-3 py-2" />
            <div v-if="form.errors.nombre" class="text-red-500 text-sm">{{ form.errors.nombre }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium">Apellido</label>
            <input v-model="form.apellido" type="text" class="w-full border rounded px-3 py-2" />
            <div v-if="form.errors.apellido" class="text-red-500 text-sm">{{ form.errors.apellido }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium">Cargo</label>
            <input v-model="form.puesto" type="text" class="w-full border rounded px-3 py-2" />
            <div v-if="form.errors.puesto" class="text-red-500 text-sm">{{ form.errors.puesto }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium">Correo</label>
            <input v-model="form.correo" type="email" class="w-full border rounded px-3 py-2" />
            <div v-if="form.errors.correo" class="text-red-500 text-sm">{{ form.errors.correo }}</div>
          </div>

          <input type="hidden" v-model="form.consejo_id" />

          <div class="flex justify-end space-x-2">
            <button type="button" @click="showForm = false" class="px-4 py-2 bg-gray-300 rounded">
              Cancelar
            </button>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-black">
              Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  </AuthenticatedLayout>
</template>
