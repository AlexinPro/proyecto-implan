<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  consejo: Object,
  integrantes: Array,
  meses: Array,
  asistencia: Object,
});

const asistencia = ref(props.asistencia);
const mesSeleccionado = ref(props.meses[0]); // Por defecto primer mes

function faltas(integranteId) {
  const fechasMes = mesSeleccionado.value.fechas;
  let faltasCount = 0;
  fechasMes.forEach(fecha => {
    if (!asistencia.value[integranteId][fecha]) faltasCount++;
  });
  return faltasCount;
}

function colorSemaforo(faltas) {
  if (faltas === 0) return 'text-green-600';
  if (faltas <= 2) return 'text-yellow-500';
  return 'text-red-600';
}
</script>

<template>
  <div class="p-6 bg-white shadow rounded">
    <h1 class="text-2xl font-bold mb-6">Asistencia - {{ consejo.nombre }}</h1>

    <label class="block mb-4">
      <span>Seleccione mes:</span>
      <select v-model="mesSeleccionado" class="mt-1 p-2 border rounded">
        <option v-for="mes in meses" :key="mes.mes" :value="mes">{{ mes.mes }}</option>
      </select>
    </label>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm border border-collapse">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-2 border">Nombre</th>
            <th class="p-2 border">Correo</th>
            <th class="p-2 border">Rol</th>
            <th v-for="fecha in mesSeleccionado.fechas" :key="fecha" class="p-2 border text-center">
              {{ new Date(fecha).getDate() }}
            </th>
            <th class="p-2 border text-center">Semáforo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="integrante in integrantes" :key="integrante.id">
            <td class="p-2 border">{{ integrante.nombre }}</td>
            <td class="p-2 border">{{ integrante.correo }}</td>
            <td class="p-2 border">{{ integrante.rol }}</td>

            <td
              v-for="fecha in mesSeleccionado.fechas"
              :key="fecha"
              class="p-2 border text-center"
            >
              <input
                type="checkbox"
                v-model="asistencia[integrante.id][fecha]"
              />
            </td>

            <td class="p-2 border text-center">
              <span :class="colorSemaforo(faltas(integrante.id))">●</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
      Guardar asistencia
    </button>
  </div>
</template>
