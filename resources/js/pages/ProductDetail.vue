<script setup lang="ts">
import { ref, computed } from 'vue'
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
        sizes: string | string[]
        color: string
        stock_quantity: number
        category: string
        image: string
    }
}>()

// size db mein JSON string ya array dono ho sakta hai — safely parse karo
const sizeList = computed<string[]>(() => {
    if (Array.isArray(props.product.sizes)) 
    return props.product.sizes

    try {
        const parsed = JSON.parse(props.product.sizes)

        return Array.isArray(parsed) ? parsed : [props.product.sizes]
    } catch {
        return props.product.sizes ? [props.product.sizes] : []
    }
})

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

        <div class="px-6 sm:px-8 py-8 max-w-5xl mx-auto">

            <a href="javascript:history.back()"
                class="text-sm text-gray-500 hover:text-red-500 mb-6 inline-block">
                ← Back
            </a>

            <div class="flex flex-col md:flex-row gap-10 mt-2">

                <!-- Image -->
                <div class="w-full md:w-1/2">
                    <div class="bg-gray-100 rounded-xl overflow-hidden aspect-square">
                        <img
                            :src="'/storage/' + product.image"
                            :alt="product.name"
                            class="w-full h-full object-contain"
                        />
                    </div>
                </div>

                <!-- Details -->
                <div class="w-full md:w-1/2 flex flex-col gap-5">

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 leading-snug">
                            {{ product.name }}
                        </h1>
                        <p class="text-[#d71208] font-bold text-2xl mt-2">
                            Rs. {{ Number(product.price).toLocaleString() }}
                        </p>
                    </div>

                    <p class="text-gray-500 text-sm leading-relaxed border-b border-gray-100 pb-5">
                        {{ product.description }}
                    </p>

                    <!-- Product info grid -->
                    <div class="grid grid-cols-2 gap-y-4 gap-x-4 text-sm">

                        <div>
                            <p class="text-gray-400 mb-1.5">Brand</p>
                            <span class="bg-gray-900 text-white text-xs px-3 py-1 rounded inline-block">
                                {{ product.brand }}
                            </span>
                        </div>

                        <div>
                            <p class="text-gray-400 mb-1.5">Color</p>
                            <span class="bg-gray-900 text-white text-xs px-3 py-1 rounded inline-block">
                                {{ product.color }}
                            </span>
                        </div>

                        <div>
                            <p class="text-gray-400 mb-1.5">In Stock</p>
                            <span class="bg-gray-900 text-white text-xs px-3 py-1 rounded inline-block">
                                {{ product.stock_quantity }}
                            </span>
                        </div>

                        <div>
                            <p class="text-gray-400 mb-1.5">Category</p>
                            <span class="bg-gray-900 text-white text-xs px-3 py-1 rounded inline-block">
                                {{ product.category }}
                            </span>
                        </div>

                        <div class="col-span-2">
                            <p class="text-gray-400 mb-1.5">Available Sizes</p>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="s in sizeList" :key="s"
                                    class="bg-gray-900 text-white text-xs px-3 py-1 rounded inline-block">
                                    {{ s }}
                                </span>
                                <span v-if="sizeList.length === 0" class="text-gray-400 text-xs">
                                    Not specified
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- Quantity Selector -->
                    <div class="flex items-center gap-4 border-t border-gray-100 pt-5">
                        <p class="text-sm font-semibold text-gray-700">Quantity</p>
                        <div class="flex items-center gap-3">
                            <button
                                @click="decrement"
                                type="button"
                                class="w-9 h-9 flex items-center justify-center border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition cursor-pointer disabled:opacity-40"
                                :disabled="quantity <= 1">
                                −
                            </button>
                            <span class="w-8 text-center font-semibold text-gray-800">
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
                        class="w-full bg-gray-900 text-white font-semibold py-3 rounded-lg hover:bg-gray-700 transition disabled:opacity-50 cursor-pointer">
                        {{ product.stock_quantity === 0 ? 'OUT OF STOCK' : (adding ? 'ADDING...' : 'ADD TO BAG') }}
                    </button>

                </div>
            </div>
        </div>

    </div>
</template>