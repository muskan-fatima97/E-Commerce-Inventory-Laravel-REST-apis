<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import StoreNavbar from '@/components/StoreNavbar.vue'

defineOptions({ layout: null })

interface CartItem {
    id: number
    quantity: number
    product: {
        id: number
        name: string
        price: string
        image: string
        stock_quantity: number
    }
}

const props = defineProps<{
    items: CartItem[]
}>()

const items = ref<CartItem[]>(props.items)

const total = computed(() =>
    items.value.reduce((sum, item) => sum + Number(item.product.price) * item.quantity, 0)
)

async function updateQuantity(item: CartItem, newQty: number) {
    if (newQty < 1 || newQty > item.product.stock_quantity) return

    try {
        await axios.patch(`/api/cart/${item.id}`, { quantity: newQty })
        item.quantity = newQty
    } catch (error) {
        console.error(error)
        alert('Failed to update quantity.')
    }
}

async function removeItem(item: CartItem) {
    const confirmed = window.confirm('Remove this item from cart?')

    if (!confirmed) return

    try {
        await axios.delete(`/api/cart/${item.id}`)
        items.value = items.value.filter((i) => i.id !== item.id)
    } catch (error) {
        console.error(error)
        alert('Failed to remove item.')
    }
}

function goToCheckout() {
    router.visit('/checkout')
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">

        <StoreNavbar />

        <div class="max-w-4xl mx-auto px-4 sm:px-8 py-6 sm:py-8">

            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Your Cart</h1>

            <div v-if="items.length === 0" class="text-center py-24 text-gray-400">
                <p class="text-xl font-semibold">Your cart is empty</p>
                <a href="/store" class="text-[#d71208] font-semibold mt-2 inline-block">Continue Shopping</a>
            </div>

            <div v-else class="flex flex-col gap-4">

                <div v-for="item in items" :key="item.id"
                    class="bg-white rounded-xl shadow-sm p-4 flex flex-col lg:flex-row lg:items-center gap-4">

                    <div class="flex items-center gap-4">
                        <img :src="'/storage/' + item.product.image" :alt="item.product.name"
                            class="w-20 h-20 object-cover rounded-lg shrink-0" />

                        <div class="flex-1 min-w-0 lg:hidden">
                            <h3 class="font-semibold text-gray-800 truncate">{{ item.product.name }}</h3>
                            <p class="text-[#d71208] font-bold text-sm">
                                Rs. {{ Number(item.product.price).toLocaleString() }}
                            </p>
                        </div>
                    </div>

                    <div class="hidden lg:block flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-800 truncate">{{ item.product.name }}</h3>
                        <p class="text-[#d71208] font-bold text-sm">
                            Rs. {{ Number(item.product.price).toLocaleString() }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between lg:justify-end lg:gap-6 gap-4">

                        <div class="flex items-center gap-2">
                            <button @click="updateQuantity(item, item.quantity - 1)"
                                class="w-8 h-8 text-gray-800 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-100 cursor-pointer">
                                −
                            </button>
                            <span class="w-8 text-center font-semibold text-gray-800">{{ item.quantity }}</span>
                            <button @click="updateQuantity(item, item.quantity + 1)"
                                class="w-8 h-8 text-gray-800 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-100 cursor-pointer">
                                +
                            </button>
                        </div>

                        <p class="lg:w-24 text-right font-semibold text-gray-800">
                            Rs. {{ (Number(item.product.price) * item.quantity).toLocaleString() }}
                        </p>

                        <button @click="removeItem(item)" class="text-red-500 hover:text-red-700 text-sm cursor-pointer">
                            Remove
                        </button>
                    </div>

                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-4">
                    <p class="text-lg font-bold text-gray-800">
                        Total: <span class="text-[#d71208]">Rs. {{ total.toLocaleString() }}</span>
                    </p>
                    <button @click="goToCheckout"
                        class="w-full sm:w-auto bg-gray-900 text-white font-semibold px-6 py-3 rounded-lg hover:bg-gray-700 transition cursor-pointer">
                        Proceed to Checkout
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>