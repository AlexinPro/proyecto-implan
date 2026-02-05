<script>
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
  components: { AuthenticatedLayout },

  props: {
    consejo: Object,
    registros: Array,
  },

  data() {
    return {
      showModal: false,
      seleccionado: null,
    };
  },

  methods: {
    formatearFecha(fecha) {
      if (!fecha) return "N/A";
      return new Date(fecha).toLocaleDateString("es-MX");
    },

    abrirValidacion(item) {
      this.seleccionado = item;
      this.showModal = true;
    },

    cerrarModal() {
      this.showModal = false;
      this.seleccionado = null;
    },

    aprobar() {
      router.post(`/legalidad/${this.seleccionado.id}/aprobar`);
      this.cerrarModal();
    },

    rechazar() {
      router.post(`/legalidad/${this.seleccionado.id}/rechazar`);
      this.cerrarModal();
    },

    estado(item) {
      return item.estatus_reeleccion || "pendiente";
    },

    vecesReelegido(item) {
      return item.ya_reelegido ? "1 vez" : "0 veces";
    }
  },
};
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">

      <h1 class="text-2xl font-bold text-center">
        Validación de Reelecciones
        <br />
        <span class="text-gray-600 text-lg">
          Consejo de {{ consejo.nombre }}
        </span>
      </h1>

      <div class="overflow-x-auto bg-white shadow rounded p-4">
        <table class="min-w-full border-collapse">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-4 py-3 border">Integrante</th>
              <th class="px-4 py-3 border">Inicio</th>
              <th class="px-4 py-3 border">Fin</th>
              <th class="px-4 py-3 border text-center">Estatus</th>
              <th class="px-4 py-3 border text-center">Reelegido</th>
              <th class="px-4 py-3 border text-center w-40">Acciones</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in registros" :key="item.id">
              <td class="px-4 py-3 border">
                {{ item.integrante?.nombre }} {{ item.integrante?.apellido }}
              </td>

              <td class="px-4 py-3 border">
                {{ formatearFecha(item.inicio_cargo) }}
              </td>

              <td class="px-4 py-3 border">
                {{ formatearFecha(item.fin_cargo) }}
              </td>

              <td class="px-4 py-3 border text-center font-semibold">
                {{ estado(item) }}
              </td>

              <td class="px-4 py-3 border text-center">
                {{ vecesReelegido(item) }}
              </td>

              <td class="px-4 py-3 border text-center">
                <button
                  v-if="estado(item) === 'pendiente'"
                  class="px-3 py-1 rounded text-white bg-blue-700"
                  @click="abrirValidacion(item)"
                >
                  Validar
                </button>

                <button
                  v-else
                  class="px-3 py-1 rounded text-white bg-gray-700"
                  @click="abrirValidacion(item)"
                >
                  Ver
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- MODAL DE VALIDACIÓN -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
      >
        <div class="bg-white rounded shadow p-6 w-96 space-y-4">
          <h2 class="text-xl font-bold text-center">
            Validar documentos
          </h2>

          <div v-if="seleccionado">
            <p><strong>Integrante:</strong>
              {{ seleccionado.integrante?.nombre }}
              {{ seleccionado.integrante?.apellido }}
            </p>

            <a
              v-if="seleccionado.doc_nombramiento"
              :href="`/storage/${seleccionado.doc_nombramiento}`"
              target="_blank"
              class="block text-blue-600 underline mt-2"
            >
              Ver Nombramiento
            </a>

            <a
              v-if="seleccionado.doc_carta_reeleccion"
              :href="`/storage/${seleccionado.doc_carta_reeleccion}`"
              target="_blank"
              class="block text-blue-600 underline mt-2"
            >
              Ver Carta de Reelección
            </a>

            <a
              v-if="seleccionado.doc_otros"
              :href="`/storage/${seleccionado.doc_otros}`"
              target="_blank"
              class="block text-blue-600 underline mt-2"
            >
              Ver Otros documentos
            </a>
          </div>

          <div class="flex justify-between mt-4">
            <button
              @click="cerrarModal"
              class="px-4 py-2 bg-gray-500 text-white rounded"
            >
              Cerrar
            </button>

            <div class="space-x-2">
              <button
                @click="rechazar"
                class="px-4 py-2 bg-red-700 text-white rounded"
              >
                Rechazar
              </button>

              <button
                @click="aprobar"
                class="px-4 py-2 bg-green-700 text-white rounded"
              >
                Aprobar
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
