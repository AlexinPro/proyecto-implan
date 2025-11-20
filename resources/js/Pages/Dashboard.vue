<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import BarChart from '@/Components/Charts/BarChart.vue'

const props = defineProps({
  labels: Array,
  data: Array,
})
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      Bienvenido a tu herramienta de gestión digital.
    </template>

    <!-- Título -->
    <div class="p-4 bg-white rounded-lg shadow-xs mb-4">
      <h1 class="text-2xl font-bold">Instituto Municipal de Planeación
        para el Desarrollo del Estado de Puebla
      </h1>
    </div>

    <!-- Tarjetas -->
    <div class="p-4 bg-white rounded-lg shadow-xs mb-6">
      <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        <!-- Aquí tus cards actuales -->
      </div>
    </div>

    <!-- 📊 Gráfica -->
    <div class="p-4 bg-white rounded-lg shadow-xs">
      <h2 class="text-xl font-semibold mb-3">Integrantes por Consejo</h2>
      <BarChart :labels="props.labels" :data="props.data" title="Integrantes por Consejo" />
    </div>
  </AuthenticatedLayout>
</template>
