<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { defineComponent, h } from 'vue'

defineProps({
  consejos: Array,
  origen: String,
})

const PetsIcon = defineComponent({
  name: 'PetsIcon',
  render() {
    return h(
      'span',
      { class: 'material-icons text-white text-5xl' },
      'pets'
    )
  }
})

const SportsSoccerIcon = defineComponent({
  name: 'SportsSoccerIcon',
  render() {
    return h(
      'span',
      { class: 'material-symbols-outlined text-white text-5xl' },
      'sports_soccer'
    )
  }
})

const AccesibleIcon = defineComponent({
  name: 'AccesibleIcon',
  render() {
    return h(
      'span',
      { class: 'material-symbols-outlined text-white text-5xl' },
      'accessible_forward'
    )
  }
})

import {
  SunIcon,
  GlobeAmericasIcon,
  FaceSmileIcon,
  ShieldCheckIcon,
  UserGroupIcon,
  UsersIcon,
  FingerPrintIcon,
  HeartIcon,
  BuildingOfficeIcon,
  BuildingOffice2Icon,
  HandRaisedIcon,
  TruckIcon,
  WrenchScrewdriverIcon,
  BriefcaseIcon,
  ShieldExclamationIcon,
  AcademicCapIcon,
  MapPinIcon,
} from '@heroicons/vue/24/solid'

const icons = {
  'Asuntos Indígenas': SunIcon,
  'Bienestar': FaceSmileIcon,
  'Bienestar Animal': PetsIcon,
  'Centro Histórico y Patrimonio Edificado': BuildingOfficeIcon,
  'Cultura': FingerPrintIcon,
  'Deporte': SportsSoccerIcon,
  'Derechos Humanos e Igualdad entre Géneros': HandRaisedIcon,
  'Desarrollo Urbano': BuildingOffice2Icon,
  'Desempeño Gubernamental': BriefcaseIcon,
  'Discapacidad': AccesibleIcon,
  'Ecología y Medio Ambiente': GlobeAmericasIcon,
  'Educación': AcademicCapIcon,
  'Juventud': UserGroupIcon,
  'Niñez y la Adolescencia': UsersIcon,
  'Obras y Servicios Públicos': WrenchScrewdriverIcon,
  'Movilidad': TruckIcon,
  'Personas en Situación de Vulnerabilidad': ShieldExclamationIcon,
  'Protección Civil': ShieldCheckIcon,
  'Salud': HeartIcon,
  'Seguridad Pública': ShieldCheckIcon,
  'Turismo': MapPinIcon,
  'Vialidad y Transporte': TruckIcon,
}
</script>

<template>
  <AuthenticatedLayout>

    <Head title="Archivo Digital" />

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

      <!-- 🔥 TITULO DINAMICO -->
      <h1 class="text-2xl font-bold mb-6 text-center">
        {{ origen === 'asistencias'
          ? 'Asistencias y Participación'
          : origen === 'convocatorias'
            ? 'Convocatorias y Publicaciones'
            : origen === 'reportes'
              ? 'Reportes'
              : 'Consejos de Participación Ciudadana'
        }}
      </h1>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <Link v-for="consejo in consejos" :key="consejo.id" :href="origen === 'convocatorias'
            ? route('convocatorias.index', { consejo: consejo.id })
            : origen === 'asistencias'
              ? route('asistencias.index', { consejo: consejo.id })
              : origen === 'reportes'
                ? route('reportes.index', { consejo: consejo.id })
                : route('consejos.integrantes', consejo.id)
          " class="p-4 shadow rounded-lg flex flex-col items-center justify-center
                 font-semibold text-center text-white bg-gray-600 hover:bg-black transition">
        <component :is="icons[consejo.nombre]" class="w-10 h-10 text-white mb-2" />

        {{ consejo.nombre }}
        </Link>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
