<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import StoreNavbar from '@/components/StoreNavbar.vue'

defineOptions({ layout: null })

const props = defineProps<{
    product: {
        id: number
        name: string
        description: string
        price: string
        brand: string
        size: string
        color: string
        stock_quantity: number
        category: string
        image: string
    }
}>()

const quantity = ref(1)

function increment() {
    if (quantity.value < props.product.stock_quantity) {
        quantity.value++
    }
}

function decrement() {
    if (quantity.value > 1) {
        quantity.value--
    }
}

const adding = ref(false)

async function addToBag() {
    adding.value = true
    try {
        await axios.post('/api/cart', {
            product_id: props.product.id,
            quantity: quantity.value,
        })
        alert('Product added to cart!')
    } catch (error) {
        console.error(error)
        alert('Failed to add product to cart.')
    } finally {
        adding.value = false
    }
}
</script>

<template>
    <div class="min-h-screen bg-white">

        <StoreNavbar />

        <div class="px-8 py-8 max-w-5xl mx-auto">

            <a href="javascript:history.back()"
                class="text-sm text-gray-500 hover:text-red-500 mb-6 inline-block">
                Back
            </a>

            <div class="flex flex-col md:flex-row gap-10 mt-4">

                <div class="w-full md:w-1/2">
                    <div class="bg-gray-100 rounded-xl overflow-hidden h-96">
                        <img
                            :src="'/storage/' + product.image"
                            :alt="product.name"
                            class="w-full h-full object-cover"
                        />
                    </div>
                </div>

                <div class="w-full md:w-1/2 flex flex-col gap-4">

                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ product.name }}
                    </h1>

                    <p class="text-[#d71208] font-bold text-2xl">
                        Rs. {{ Number(product.price ).toLocaleString()}}
                    </p>

                    <p class="text-gray-500 text-sm leading-relaxed">
                        {{ product.description }}
                    </p>

                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-1">Brand:</p>
                        <span class="bg-gray-900 text-white text-xs px-3 py-1 rounded">
                            {{ product.brand }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-1">Size:</p>
                        <span class="bg-gray-900 text-white text-xs px-3 py-1 rounded">
                            {{ product.size }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-1">Color:</p>
                        <span class="bg-gray-900 text-white text-xs px-3 py-1 rounded">
                            {{ product.color }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-500">
                        Stock Left
                        <span class="font-bold text-gray-800">{{ product.stock_quantity }}</span>
                    </p>

                    <p class="text-xs text-gray-400 uppercase tracking-wide">
                        Category: {{ product.category }}
                    </p>

                    <!-- Quantity Selector -->
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-1">Quantity:</p>
                        <div class="flex items-center gap-3">
                            <button
                                @click="decrement"
                                type="button"
                                class="w-9 h-9 flex items-center justify-center border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition cursor-pointer disabled:opacity-40"
                                :disabled="quantity <= 1">
                                −
                            </button>
                            <span class="w-10 text-center font-semibold text-gray-800">
                                {{ quantity }}
                            </span>
                            <button
                                @click="increment"
                                type="button"
                                class="w-9 h-9 flex items-center justify-center border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition cursor-pointer disabled:opacity-40"
                                :disabled="quantity >= product.stock_quantity">
                                +
                            </button>
                        </div>
                    </div>

                    <button
                        @click="addToBag"
                        :disabled="adding || product.stock_quantity === 0"
                        class="w-full bg-gray-900 text-white font-semibold py-3 rounded-lg hover:bg-gray-700 transition mt-2 disabled:opacity-50 cursor-pointer">
                        {{ product.stock_quantity === 0 ? 'OUT OF STOCK' : (adding ? 'ADDING...' : 'ADD TO BAG') }}
                    </button>

                </div>
            </div>
        </div>

    </div>
</template>