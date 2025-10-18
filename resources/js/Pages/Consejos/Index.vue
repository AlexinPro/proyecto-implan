<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3'
import { HomeIcon, UserGroupIcon, BookOpenIcon } from '@heroicons/vue/24/solid'

defineProps({
  consejos: Array,
  origen: String,
})

/*const icons = {
  "Asuntos indigenas":HomeIcon,
  2:UserGroupIcon,
  3:BookOpenIcon,
}*/

</script>

<template>
  <AuthenticatedLayout>

    <Head title="Archivo Digital" />

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
      <h1 class="text-2xl font-bold mb-4">
        Consejos de Participación Ciudadana
      </h1>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <Link v-for="consejo in consejos" :key="consejo.id" 
        :href="origen === 'convocatorias'
           ? route('convocatorias.index', { consejo: consejo.id })
           : route('consejos.integrantes', consejo.id)"
          class="p-4 shadow rounded-lg flex items-center justify-center
         font-semibold text-center text-white bg-gray-600 hover:bg-black transition">
        {{ consejo.nombre }}
        </Link>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
