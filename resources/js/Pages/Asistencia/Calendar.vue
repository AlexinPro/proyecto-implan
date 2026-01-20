<script setup>
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Calendar } from 'v-calendar'
import 'v-calendar/dist/style.css'
import Form from './Form.vue'

// Props desde el controlador
const props = defineProps({
  sesiones: Array,
  consejo: Object,
  integrantes: Array
})

//funcion cerrar modal
function cerrarModal() {
  showForm.value = false
}

const showForm = ref(false)
const fechaSeleccionada = ref(null)
const selectedDate = ref(null)

// ✅ Fix de desfase: generar fecha sin timezone
function formatearFechaLocal(date) {
  const y = date.getFullYear()
  const m = String(date.getMonth() + 1).padStart(2, '0')
  const d = String(date.getDate()).padStart(2, '0')
  return `${y}-${m}-${d}`
}

// Abrir formulario al hacer clic en un día
function abrirFormulario(day) {
  const fecha = formatearFechaLocal(day.date)   // 🔥 fecha correcta sin UTC
  fechaSeleccionada.value = fecha
  selectedDate.value = fecha
  showForm.value = true
}

// Colores de sesiones + fix de fechas
const calendarAttributes = computed(() => {
  return props.sesiones.map(s => {
    let color = ''

    switch (s.tipo_sesion) {
      case 'ordinaria': color = 'yellow'; break
      case 'solemne': color = 'green'; break
      case 'extraordinaria': color = 'red'; break
    }

    return {
      key: s.id,
      dates: new Date(`${s.fecha}T00:00:00`),   // 🔥 Mantener string evita desfase
      highlight: {
        fillMode: 'solid',
        color: color,
      },
      dot: {
        color: color,
        class: 'opacity-80'
      },
      popover: {
        label: `Sesión ${s.tipo_sesion} (${s.fecha})`
      }
    }
  })
})

// Actualizar calendario después de guardar
function actualizarCalendario(data) {
  props.sesiones.push({
    id: Date.now(),
    fecha: data.fecha,
    tipo_sesion: data.tipo_sesion
  })
  showForm.value = false
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 w-full flex justify-center">

      <!-- Calendario grande -->
      <div class="w-[85%] max-w-5xl">
        <Calendar v-model="selectedDate" :attributes="calendarAttributes" @dayclick="abrirFormulario"
          class="big-calendar" />
      </div>

    </div>

    <!-- FORMULARIO MODAL -->
    <Form v-if="showForm" :integrantes="integrantes" 
    :consejo-id="consejo.id" 
    :fecha="fechaSeleccionada"
      @close="cerrarModal" 
      @saved="cerrarModal" />
  </AuthenticatedLayout>
</template>

<style scoped>
.big-calendar {
  font-size: 1.35rem;
}

:deep(.vc-day-content) {
  width: 3.2rem !important;
  height: 3.2rem !important;
  font-size: 1.2rem !important;
}

:deep(.vc-day:hover > .vc-day-content) {
  background: #e5e7eb !important;
  border-radius: 10px;
  transition: 0.15s ease;
}

:deep(.vc-title) {
  font-size: 1.6rem !important;
  font-weight: 600 !important;
}

:deep(.vc-arrow) {
  transform: scale(1.4);
}
</style>
