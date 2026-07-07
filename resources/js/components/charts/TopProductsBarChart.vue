<script setup lang="ts">

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from 'chart.js'
import { computed } from 'vue'
import { Bar } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const props = defineProps<{
    labels: string[]
    data: number[]
}>()

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [
        {
            label: 'Units Sold',
            data: props.data,
            backgroundColor: '#111827',
            borderRadius: 6,
        },
    ],
}))

const chartOptions = {
    indexAxis: 'y' as const,
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
    },
}
</script>

<template>
    <div class="h-64">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>