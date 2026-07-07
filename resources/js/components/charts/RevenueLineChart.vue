<script setup lang="ts">
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement,
    Filler,
} from 'chart.js'
import { computed } from 'vue'
import { Line } from 'vue-chartjs'


ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement, Filler)

const props = defineProps<{
    labels: string[]
    data: number[]
}>()

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [
        {
            label: 'Revenue (Rs.)',
            data: props.data,
            borderColor: '#d71208',
            backgroundColor: 'rgba(215, 18, 8, 0.1)',
            fill: true,
            tension: 0.3,
            pointRadius: 3,
        },
    ],
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value: number) => 'Rs. ' + Number(value).toLocaleString(),
            },
        },
    },
}
</script>

<template>
    <div class="h-72">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>