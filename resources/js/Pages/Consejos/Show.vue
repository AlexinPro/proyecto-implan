<script setup>
defineProps({
  consejo: Object
})
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">{{ consejo.nombre }}</h1>
    <p class="mb-6 text-gray-600">{{ consejo.descripcion }}</p>

    <h2 class="text-xl font-semibold mb-3">Integrantes</h2>
    <ul class="space-y-2">
      <li v-for="integrante in consejo.integrantes" :key="integrante.id" class="p-3 border rounded">
        <p class="font-medium">{{ integrante.nombre }}</p>
        <p class="text-sm text-gray-500">{{ integrante.cargo }}</p>
        <p class="text-sm text-gray-500">{{ integrante.correo }}</p>
      </li>
    </ul>
  </div>
</template>
