<script setup lang="ts">
import axios from 'axios'
import { Sun, Cloud, CloudRain, CloudSnow, CloudLightning, CloudFog } from 'lucide-vue-next'
import { ref, onMounted, watch } from 'vue'

interface City {
    name: string
    lat: number
    lon: number
}

const cities: City[] = [
    { name: 'Lahore', lat: 31.5497, lon: 74.3436 },
    { name: 'Islamabad', lat: 33.6844, lon: 73.0479 },
    { name: 'Bahawalpur', lat: 29.3956, lon: 71.6836 },
    { name: 'Karachi', lat: 24.8607, lon: 67.0011 },
    { name: 'Faisalabad', lat: 31.4504, lon: 73.1350 },
    { name: 'Multan', lat: 30.1575, lon: 71.5249 },
    { name: 'Peshawar', lat: 34.0151, lon: 71.5249 },
    { name: 'Quetta', lat: 30.1798, lon: 66.9750 },
]

const selectedCity = ref<City>(cities[0])
const temperature = ref<number | null>(null)
const weatherCode = ref<number | null>(null)
const loading = ref(false)

async function fetchWeather() {
    loading.value = true

    try {
        const response = await axios.get('/api/weather', {
            params: {
                lat: selectedCity.value.lat,
                lon: selectedCity.value.lon,
                city: selectedCity.value.name,
            },
        })
        temperature.value = response.data.temperature
        weatherCode.value = response.data.weather_code
    } catch (error) {
        console.error('Weather fetch failed', error)
        temperature.value = null
    } finally {
        loading.value = false
    }
}

function onCityChange(event: Event) {
    const target = event.target as HTMLSelectElement
    const city = cities.find((c) => c.name === target.value)

    if (city) {
        selectedCity.value = city
    }
}

watch(selectedCity, fetchWeather)
onMounted(fetchWeather)

// Open-Meteo weather codes ko icon mein map karna
function getWeatherIcon(code: number | null) {
    if (code === null) return Sun

    if (code === 0) return Sun

    if ([1, 2, 3].includes(code)) return Cloud

    if ([45, 48].includes(code)) return CloudFog
    
    if ([51, 53, 55, 61, 63, 65, 80, 81, 82].includes(code)) return CloudRain

    if ([71, 73, 75, 77, 85, 86].includes(code)) return CloudSnow

    if ([95, 96, 99].includes(code)) return CloudLightning

    return Sun
}
</script>

<template>
    <div class="flex items-center gap-2 text-sm text-gray-700">
        <component :is="getWeatherIcon(weatherCode)" class="w-4 h-4 text-[#d71208]" />

        <span v-if="loading" class="text-xs text-gray-400">...</span>
        <span v-else-if="temperature !== null" class="font-semibold whitespace-nowrap">
            {{ temperature }}°C
        </span>

        <select
            :value="selectedCity.name"
            @change="onCityChange"
            class="bg-transparent text-xs text-gray-600 border-none focus:outline-none cursor-pointer"
        >
            <option v-for="city in cities" :key="city.name" :value="city.name">
                {{ city.name }}
            </option>
        </select>
    </div>
</template>