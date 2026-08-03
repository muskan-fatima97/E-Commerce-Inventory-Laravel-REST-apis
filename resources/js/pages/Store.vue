<script setup lang="ts">
import { ref, onMounted } from 'vue'
import StoreNavbar from '@/components/StoreNavbar.vue'

defineOptions({ layout: null })

const products = ref<any[]>([])
const loading = ref(false)
const selectedCategory = ref('Men')

const categories = ['Men', 'Women', 'Kids']

async function fetchProducts(category: string) {
    loading.value = true

    try {
        const response = await fetch(`/api/product?category=${category}`)
        const json = await response.json()
        products.value = json.data.data
    } catch (error) {
        console.error('Failed to fetch products', error)
    } finally {
        loading.value = false
    }
}

function selectCategory(category: string) {
    selectedCategory.value = category
    window.history.pushState({}, '', `/store?category=${category}`)
    fetchProducts(category)
}

onMounted(() => {
    // fetchProducts(selectedCategory.value)
    const params = new URLSearchParams(window.location.search)
    const categoryFromUrl = params.get('category') || 'Men'
    selectedCategory.value = categoryFromUrl
    fetchProducts(categoryFromUrl)
})
</script>

<template>
    <div class="min-h-screen bg-white">

        <StoreNavbar />

        <div class="px-16 py-8">

            <div class="flex gap-4 mb-8">
                <button v-for="cat in categories" :key="cat" @click="selectCategory(cat)"
                    class="px-12 py-2 border border-[#d71208] cursor-pointer text-[#d71208] rounded-sm font-semibold text-[16px] uppercase tracking-wide hover:bg-red-50 transition"
                    :class="selectedCategory === cat
                        ? 'px-12 py-2 border border-[#d71208] text-[#d71208] bg-red-50 rounded-sm font-semibold text-[16px] uppercase '
                        : 'border border-[#d71208] text-[#d71208] hover:border-[#c31006] hover:text-[#c31006] cursor-pointer'">
                    {{ cat }}
                </button>
            </div>

            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-900 uppercase">
                    {{ selectedCategory }}
                </h2>
                <p class="text-gray-500 text-sm mt-1">
                    {{ products.length }} results
                </p>
            </div>

            <div v-if="loading" class="flex justify-center items-center mt-24">
                <div class="w-10 h-10 border-4 border-red-600 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <div v-else-if="products.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <a v-for="product in products" :key="product.id" :href="`/product/${product.id}`"
                    class="bg-white rounded-xl border border-gray-200 hover:shadow-lg transition duration-300 flex flex-col overflow-hidden">
                    <div class="w-full h-64 bg-gray-100 overflow-hidden">
                        <img :src="'/storage/' + product.image" :alt="product.name"
                            class="w-full h-full object-contain hover:scale-105 transition-transform duration-300" />
                    </div>

                    <div class="p-4 flex flex-col flex-1">
                                                <div class="mt-3 flex items-center justify-between">

                        <span class="text-md text-red-500 font-medium uppercase mb-1">
                            {{ product.brand }}
                        </span>
                         <span class="text-xs text-gray-400 border border-gray-200 px-2 py-1 rounded">
                                Size: {{ product.sizes?.join(', ') }}
                            </span>
                        </div>
                        <h3 class="text-md font-semibold text-gray-800 mb-1">
                            {{ product.name }}
                        </h3>
                        <!-- <p class="text-md text-gray-400 line-clamp-2 flex-1">
                            {{ product.description }}
                        </p> -->
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-[#d71208] font-bold">
                               Rs. {{ Number(product.price ).toLocaleString()}}
                            </span>
                           
                        </div>
                    </div>
                </a>
            </div>

            <div v-else class="flex flex-col items-center justify-center mt-24 text-gray-400">
                <p class="text-xl font-semibold">No products available</p>
                <p class="text-sm mt-1">Try selecting a different category</p>
            </div>

        </div>
    </div>
</template>