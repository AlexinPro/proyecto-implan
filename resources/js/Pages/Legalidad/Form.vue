<script>
import { router } from "@inertiajs/vue3";

export default {
  props: {
    consejo: Object,
    integrantes: Array,
    editData: Object,
  },

  data() {
    return {
      form: {
        integrante_id: "",
        inicio_cargo: "",
        fin_cargo: "",
        periodo_habil: "",
        archivo: null,
      },
    };
  },

  computed: {
    esReeleccion() {
      return !!this.editData;
    },
  },

  mounted() {
    if (this.editData) {
      this.form.integrante_id = this.editData.integrante_id;
      this.form.inicio_cargo = this.editData.inicio_cargo;
      this.form.fin_cargo = this.editData.fin_cargo;
      this.form.periodo_habil = this.editData.periodo_habil;
    }
  },

  methods: {
    calcularPeriodo() {
      if (!this.form.inicio_cargo || !this.form.fin_cargo) return;

      const inicio = new Date(this.form.inicio_cargo);
      const fin = new Date(this.form.fin_cargo);
      const diffTime = Math.abs(fin - inicio);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      this.form.periodo_habil = `${diffDays} días`;
    },

    handleFile(e) {
      this.form.archivo = e.target.files[0];
    },

    submitForm() {
      const data = new FormData();

      Object.keys(this.form).forEach((key) => {
        if (this.form[key] !== null) {
          data.append(key, this.form[key]);
        }
      });

      if (this.esReeleccion) {
        router.post(
          `/legalidad/${this.editData.id}/reeleccion`,
          data
        );
      } else {
        router.post(
          `/legalidad/${this.consejo.id}`,
          data
        );
      }

      this.$emit("close");
    },
  },
};
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded shadow p-6 w-96">

      <!-- TÍTULO -->
      <h2 class="text-xl font-bold mb-4 text-center">
        {{ esReeleccion ? 'Inicio de proceso de reelección' : 'Crear periodo' }}
      </h2>

      <form @submit.prevent="submitForm">

        <!-- INTEGRANTE -->
        <label class="block mb-2 font-semibold">Integrante</label>
        <select
          v-model="form.integrante_id"
          class="w-full border rounded px-3 py-2 mb-3 bg-gray-100"
          :disabled="esReeleccion"
        >
          <option value="">Seleccione...</option>
          <option
            v-for="i in integrantes"
            :key="i.id"
            :value="i.id"
          >
            {{ i.nombre }} {{ i.apellido }}
          </option>
        </select>

        <!-- FECHA INICIO -->
        <label class="block mb-2 font-semibold">Fecha inicio</label>
        <input
          type="date"
          v-model="form.inicio_cargo"
          class="w-full border rounded px-3 py-2 mb-3 bg-gray-100"
          :disabled="esReeleccion"
          @change="calcularPeriodo"
        />

        <!-- FECHA FIN -->
        <label class="block mb-2 font-semibold">Fecha fin</label>
        <input
          type="date"
          v-model="form.fin_cargo"
          class="w-full border rounded px-3 py-2 mb-3 bg-gray-100"
          :disabled="esReeleccion"
          @change="calcularPeriodo"
        />

      <!-- ARCHIVO (SOLO REELECCIÓN) -->
        <div v-if="esReeleccion">
          <label class="block mb-2 font-semibold">
            Documento de inicio de reelección
          </label>
          <input
            type="file"
            @change="handleFile"
            class="w-full border rounded px-3 py-2 mb-4"
            required
          />
        </div>

        <!-- BOTONES -->
        <div class="flex justify-end mt-4">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 bg-gray-500 text-white rounded mr-2"
          >
            Cancelar
          </button>

          <button
            type="submit"
            class="px-4 py-2 text-white rounded"
            :style="{ backgroundColor: esReeleccion ? '#7A1F32' : '#C7A447' }"
          >
            Guardar
          </button>
        </div>

      </form>
    </div>
  </div>
</template>


