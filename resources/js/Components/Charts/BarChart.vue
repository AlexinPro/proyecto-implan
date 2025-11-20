<script setup>
import { ref, onMounted, watch } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
  labels: Array,
  data: Array,
  title: String,
})

const chartCanvas = ref(null)
let chartInstance = null

const renderChart = () => {
  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(chartCanvas.value, {
    type: 'bar',
    data: {
      labels: props.labels,
      datasets: [
        {
          label: props.title,
          data: props.data,
          backgroundColor: 'rgba(37, 99, 235, 0.5)',
          borderColor: 'rgb(37, 99, 235)',
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
          },
        },
      },
    },
  })
}

onMounted(renderChart)
watch(() => props.data, renderChart)
</script>

<template>
  <div class="bg-white p-4 rounded-lg shadow">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>
