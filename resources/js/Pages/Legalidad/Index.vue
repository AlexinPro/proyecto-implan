<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "./Form.vue";

export default {
  components: {
    AuthenticatedLayout,
    Form,
  },

  props: {
    consejo: Object,
    integrantes: Array,
    registros: Array,
  },

  data() {
    return {
      showForm: false,
      selectedLegalidad: null,
    };
  },

  methods: {
    openCreateForm() {
      this.selectedLegalidad = null;
      this.showForm = true;
    },

    openReeleccion(item) {
      this.selectedLegalidad = item;
      this.showForm = true;
    },

    closeForm() {
      this.showForm = false;
      this.selectedLegalidad = null;
    },

    formatearFecha(fecha) {
      if (!fecha) return "N/A";
      return new Date(fecha).toLocaleDateString("es-MX");
    },

    calcularTiempoRestante(finCargo) {
      if (!finCargo) return "N/A";

      const hoy = new Date();
      const fin = new Date(finCargo);

      if (fin < hoy) return "Vencido";

      let years = fin.getFullYear() - hoy.getFullYear();
      let months = fin.getMonth() - hoy.getMonth();
      let days = fin.getDate() - hoy.getDate();

      if (days < 0) {
        months--;
        const lastMonth = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        days += lastMonth.getDate();
      }

      if (months < 0) {
        years--;
        months += 12;
      }

      const pad = (n) => String(n).padStart(2, "0");
      return `${pad(years)}:${pad(months)}:${pad(days)}`;
    },

    periodoMenorAUnMes(finCargo) {
      const tiempo = this.calcularTiempoRestante(finCargo);
      if (tiempo === "Vencido" || tiempo === "N/A") return false;

      const [years, months] = tiempo.split(":").map(Number);
      return years === 0 && months === 0;
    },

    // Se deja solo para mantener compatibilidad, pero ya NO controla la UI
    estadoReeleccion(item) {
      return item.estatus_reeleccion || null;
    },
  },
};
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">

      <h1 class="text-2xl font-bold text-center">
        Legalidad y Control Normativo
        <br />
        <span class="text-gray-600 text-lg">
          Consejo de {{ consejo.nombre }}
        </span>
      </h1>

      <div class="flex justify-end">
        <button
          @click="openCreateForm"
          class="px-4 py-2 rounded text-white font-semibold"
          style="background-color:#C7A447;"
        >
          Crear periodo
        </button>
      </div>

      <div class="overflow-x-auto bg-white shadow rounded p-4">
        <table class="min-w-full border-collapse">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-4 py-3 border">Integrante</th>
              <th class="px-4 py-3 border">Inicio</th>
              <th class="px-4 py-3 border">Fin</th>
              <th class="px-4 py-3 border text-center">Periodo hábil</th>
              <th class="px-4 py-3 border text-center w-40">Acciones</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in registros" :key="item.id" class="hover:bg-gray-50">

              <td class="px-4 py-3 border">
                {{ item.integrante?.nombre }} {{ item.integrante?.apellido }}
              </td>

              <td class="px-4 py-3 border">
                {{ formatearFecha(item.inicio_cargo) }}
              </td>

              <td class="px-4 py-3 border">
                {{ formatearFecha(item.fin_cargo) }}
              </td>

              <td
                class="px-4 py-3 border text-center font-mono font-semibold rounded"
                :class="{
                  'text-red-700': calcularTiempoRestante(item.fin_cargo) === 'Vencido',
                  'bg-yellow-200 text-yellow-900': periodoMenorAUnMes(item.fin_cargo)
                }"
              >
                {{ calcularTiempoRestante(item.fin_cargo) }}
              </td>

              <!-- 🔹 BOTÓN COMO LO TENÍAS (SIN RESTRICCIONES VISUALES) -->
              <td class="px-4 py-3 border text-center">
                <button
                  class="px-3 py-1 rounded text-white"
                  style="background-color:#7A1F32;"
                  @click="openReeleccion(item)"
                >
                  Iniciar proceso
                </button>
              </td>

            </tr>
          </tbody>
        </table>
      </div>

      <Form
        v-if="showForm"
        :consejo="consejo"
        :integrantes="integrantes"
        :editData="selectedLegalidad"
        @close="closeForm"
      />

    </div>
  </AuthenticatedLayout>
</template>