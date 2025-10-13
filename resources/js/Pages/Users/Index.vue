<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  consejos: Array
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Asistencia por Consejo" />

    <div class="p-6 bg-white shadow rounded">
      <h1 class="text-2xl font-bold mb-4">Asistencia por Consejo</h1>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <Link
          v-for="consejo in consejos"
          :key="consejo.id"
          :href="route('usuarios.asistencia.resumen', consejo.id)"
          class="p-4 shadow rounded-lg flex items-center justify-center
                 font-semibold text-center text-white bg-blue-600 hover:bg-blue-800 transition"
        >
          {{ consejo.nombre }}
        </Link>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
