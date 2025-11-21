<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    show: Boolean,
    form: Object,
    editingId: Number,
});

const emit = defineEmits(['close', 'submit']);

// Control para mostrar campo de tipo de discapacidad
const showDiscapacidadTipo = ref(false);

// Si el checkbox de discapacidad cambia, mostrar el campo
watch(
    () => props.form.discapacidad,
    (newVal) => {
        showDiscapacidadTipo.value = !!newVal;
        if (!newVal) {
            props.form.discapacidad_tipo = '';
        }
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
                    <div v-if="form.errors.nombre" class="text-red-500 text-sm">{{ form.errors.nombre }}</div>
                </div>

                <!-- Apellido -->
                <div>
                    <label for="apellido" class="block text-sm font-medium">Apellido</label>
                    <input id="apellido" v-model="form.apellido" type="text" class="w-full border rounded px-3 py-2" />
                    <div v-if="form.errors.apellido" class="text-red-500 text-sm">{{ form.errors.apellido }}</div>
                </div>

                <!-- Género -->
                <div>
                    <label for="genero" class="block text-sm font-medium">Género</label>
                    <select id="genero" v-model="form.genero" class="w-full border rounded px-3 py-2">
                        <option value="">Seleccione</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="No binario">No binario</option>
                        <option value="Otro">Otro</option>
                    </select>
                    <div v-if="form.errors.genero" class="text-red-500 text-sm">{{ form.errors.genero }}</div>
                </div>

                <!-- Colonia -->
                <div>
                    <label for="colonia" class="block text-sm font-medium">Colonia</label>
                    <input id="colonia" v-model="form.colonia" type="text" class="w-full border rounded px-3 py-2" />
                    <div v-if="form.errors.colonia" class="text-red-500 text-sm">{{ form.errors.colonia }}</div>
                </div>

                <!-- Discapacidad -->
                <div class="flex items-center space-x-2">
                    <input id="discapacidad" type="checkbox" v-model="form.discapacidad" class="w-4 h-4" />
                    <label for="discapacidad" class="text-sm font-medium">Posee discapacidad</label>
                </div>

                <!-- Tipo de discapacidad -->
                <div v-if="showDiscapacidadTipo">
                    <label for="discapacidad_tipo" class="block text-sm font-medium">Tipo de discapacidad</label>
                    <input id="discapacidad_tipo" v-model="form.discapacidad_tipo" type="text"
                        class="w-full border rounded px-3 py-2" />
                    <div v-if="form.errors.discapacidad_tipo" class="text-red-500 text-sm">{{
                        form.errors.discapacidad_tipo }}</div>
                </div>

                <!-- Cargo -->
                <div>
                    <label for="puesto" class="block text-sm font-medium">Cargo</label>
                    <input id="puesto" v-model="form.puesto" type="text" class="w-full border rounded px-3 py-2" />
                    <div v-if="form.errors.puesto" class="text-red-500 text-sm">{{ form.errors.puesto }}</div>
                </div>

                <!-- Correo -->
                <div>
                    <label for="correo" class="block text-sm font-medium">Correo</label>
                    <input id="correo" v-model="form.correo" type="email" class="w-full border rounded px-3 py-2" />
                    <div v-if="form.errors.correo" class="text-red-500 text-sm">{{ form.errors.correo }}</div>
                </div>

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
