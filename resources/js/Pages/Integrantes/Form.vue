<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    show: Boolean,
    form: Object,       // useForm pasado desde Index.vue
    editingId: Number,
});

const emit = defineEmits(['close', 'submit']);

// Mostrar input tipo discapacidad
const showDiscapacidadTipo = ref(false);

// Mostrar input para autodescribir género
const showGeneroOtro = ref(false);

// Observa la opción de género (select)
watch(
  () => props.form.genero_opcion,
  (newVal) => {
    // Si seleccionó autodescribirse, mostrar input y no tocar form.genero
    if (newVal === 'Prefiero autodescribirme') {
      showGeneroOtro.value = true;
      // si el usuario aún no escribió nada, asegúrate que form.genero esté vacío
      if (!props.form.genero) props.form.genero = '';
    } else {
      showGeneroOtro.value = false;
      // Para opciones fijas, asigna el valor seleccionado al campo final 'genero'
      // (sobrescribe cualquier texto previo en form.genero)
      if (newVal) {
        props.form.genero = newVal;
      } else {
        props.form.genero = '';
      }
    }
  },
  { immediate: true }
);

// Observa discapacidad (checkbox)
watch(
    () => props.form.discapacidad,
    (newVal) => {
        showDiscapacidadTipo.value = !!newVal;
        if (!newVal) props.form.discapacidad_tipo = '';
    },
    { immediate: true }
);
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-lg font-bold mb-4">
                {{ editingId ? 'Editar Integrante' : 'Nuevo Integrante' }}
            </h2>

            <form @submit.prevent="$emit('submit')" class="space-y-4">

                <!-- Nombre -->
                <div>
                    <label for="nombre" class="block text-sm font-medium">Nombre</label>
                    <input id="nombre" v-model="form.nombre" type="text" class="w-full border rounded px-3 py-2" />
                    <p v-if="form.errors.nombre" class="text-red-500 text-sm">{{ form.errors.nombre }}</p>
                </div>

                <!-- Apellido -->
                <div>
                    <label for="apellido" class="block text-sm font-medium">Apellido</label>
                    <input id="apellido" v-model="form.apellido" type="text" class="w-full border rounded px-3 py-2" />
                    <p v-if="form.errors.apellido" class="text-red-500 text-sm">{{ form.errors.apellido }}</p>
                </div>

                <!-- Cargo -->
                <div>
                    <label for="puesto" class="block text-sm font-medium">Cargo</label>
                    <input id="puesto" v-model="form.puesto" type="text" class="w-full border rounded px-3 py-2" />
                    <p v-if="form.errors.puesto" class="text-red-500 text-sm">{{ form.errors.puesto }}</p>
                </div>

                <!-- Correo -->
                <div>
                    <label for="correo" class="block text-sm font-medium">Correo</label>
                    <input id="correo" v-model="form.correo" type="email" class="w-full border rounded px-3 py-2" />
                    <p v-if="form.errors.correo" class="text-red-500 text-sm">{{ form.errors.correo }}</p>
                </div>

                <!-- Colonia -->
                <div>
                    <label for="colonia" class="block text-sm font-medium">Colonia</label>
                    <input id="colonia" v-model="form.colonia" type="text" class="w-full border rounded px-3 py-2" />
                    <p v-if="form.errors.colonia" class="text-red-500 text-sm">{{ form.errors.colonia }}</p>
                </div>

                <!-- GÉNERO: select enlazado a genero_opcion -->
                <div>
                    <label for="genero_opcion" class="block text-sm font-medium">Género</label>
                    <select id="genero_opcion" v-model="form.genero_opcion" class="w-full border rounded px-3 py-2">
                        <option value="">Seleccione</option>
                        <option value="Mujer">Mujer</option>
                        <option value="Hombre">Hombre</option>
                        <option value="Prefiero autodescribirme">Prefiero autodescribirme</option>
                        <option value="Prefiero no responder">Prefiero no responder</option>
                    </select>
                    <p v-if="form.errors.genero" class="text-red-500 text-sm">{{ form.errors.genero }}</p>
                </div>

                <!-- Input libre SOLO si eligió autodescribirse -->
                <div v-if="showGeneroOtro">
                    <label for="genero_otro" class="block text-sm font-medium">Describa su género</label>
                    <!-- Vinculamos el input al campo final 'genero' (que se guardará en BD) -->
                    <input id="genero_otro" v-model="form.genero" type="text" class="w-full border rounded px-3 py-2"
                        placeholder="Especifique su género" />
                    <p v-if="form.errors.genero" class="text-red-500 text-sm">{{ form.errors.genero }}</p>
                </div>

                <!-- Discapacidad -->
                <div class="flex items-center space-x-2">
                    <input id="discapacidad" name="discapacidad" type="checkbox" v-model="form.discapacidad" class="w-4 h-4" />
                    <label for="discapacidad" class="text-sm font-medium">Posee discapacidad</label>
                </div>

                <!-- Tipo de discapacidad -->
                <div v-if="showDiscapacidadTipo">
                    <label for="discapacidad_tipo" class="block text-sm font-medium">Tipo de discapacidad</label>
                    <input id="discapacidad_tipo" v-model="form.discapacidad_tipo" type="text"
                        class="w-full border rounded px-3 py-2" />
                    <p v-if="form.errors.discapacidad_tipo" class="text-red-500 text-sm">{{ form.errors.discapacidad_tipo }}</p>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 bg-gray-300 rounded">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-black">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
