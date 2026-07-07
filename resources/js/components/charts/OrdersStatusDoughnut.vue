<script setup lang="ts">
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'
import { computed } from 'vue'
import { Doughnut } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps<{
    labels: string[]
    data: number[]
}>()

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [
        {
            data: props.data,
            backgroundColor: ['#facc15', '#3b82f6', '#a855f7', '#22c55e', '#ef4444'],
            borderWidth: 0,
        },
    ],
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' as const },
    },
}
</script>

<template>
    <div class="h-64">
        <Doughnut :data="chartData" :options="chartOptions" />
    </div>
</template>