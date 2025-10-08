<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>

	<Head title="Dashboard" />

	<AuthenticatedLayout>
		<template #header>
			Bienvenido a tu herramienta de gestión digital.
		</template>

		<div class="p-4 bg-white rounded-lg shadow-xs">
			<h1 class="text-2xl font-bold mb-4">Instituto Municipal de Planeación 
				para el Desarrollo del Estado de Puebla</h1>
		</div>
	</AuthenticatedLayout>
</template>
