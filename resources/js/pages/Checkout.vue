<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import { ref, computed } from 'vue'

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
    }
}

const props = defineProps<{
    items: CartItem[]
}>()

const total = computed(() =>
    props.items.reduce((sum, item) => sum + Number(item.product.price) * item.quantity, 0)
)

const form = ref({
    customer_name: '',
    customer_phone: '',
    shipping_address: '',
    city: '',
    payment_method: 'cod' as 'cod' | 'stripe',
})

const errors = ref<Record<string, string>>({})
const submitting = ref(false)

async function placeOrder() {
    submitting.value = true
    errors.value = {}

    try {
        const response = await axios.post('/api/orders', form.value)

        if (form.value.payment_method === 'stripe') {
            window.location.href = response.data.checkout_url
            return
        }

        const orderId = response.data.data.id
        router.visit(`/order/${orderId}/confirmation`)

    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {}

            if (error.response.data.message && Object.keys(errors.value).length === 0) {
                alert(error.response.data.message)
            }
        } else {
            alert(error.response?.data?.message ?? 'Failed to place order.')
        }
    } finally {
        submitting.value = false
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">

        <StoreNavbar />

        <div class="max-w-4xl mx-auto px-8 py-8">

            <h1 class="text-2xl font-bold text-gray-800 mb-6">Checkout</h1>

            <div v-if="items.length === 0" class="text-center py-24 text-gray-400">
                <p class="text-xl font-semibold">Your cart is empty</p>
                <a href="/store" class="text-[#d71208] font-semibold mt-2 inline-block">Continue Shopping</a>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Shipping Form -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Shipping Details</h2>

                    <form @submit.prevent="placeOrder" class="flex flex-col gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input v-model="form.customer_name" type="text" placeholder="Your full name"
                                class="text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            <p v-if="errors.customer_name" class="text-red-500 text-xs mt-1">{{ errors.customer_name[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input v-model="form.customer_phone" type="text" placeholder="03XX-XXXXXXX"
                                class="text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            <p v-if="errors.customer_phone" class="text-red-500 text-xs mt-1">{{ errors.customer_phone[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Address</label>
                            <textarea v-model="form.shipping_address" rows="3" placeholder="House #, street, area..."
                                class="text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            <p v-if="errors.shipping_address" class="text-red-500 text-xs mt-1">{{ errors.shipping_address[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input v-model="form.city" type="text" placeholder="Your city"
                                class="text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            <p v-if="errors.city" class="text-red-500 text-xs mt-1">{{ errors.city[0] }}</p>
                        </div>

                       
                        <!-- Payment Method Selection -->
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
    <div class="flex flex-col gap-2">

        <label class="flex items-center gap-3 border rounded-lg px-4 py-3 cursor-pointer transition"
            :class="form.payment_method === 'cod' ? 'border-[#d71208] bg-red-50' : 'border-gray-300'">
            <input type="radio" value="cod" v-model="form.payment_method" class="accent-[#d71208]" />
            <div>
                <p class="text-sm font-semibold text-gray-800">Cash on Delivery</p>
                <p class="text-xs text-gray-500">Pay with cash when your order arrives</p>
            </div>
        </label>

        <label class="flex items-center gap-3 border rounded-lg px-4 py-3 cursor-pointer transition"
            :class="form.payment_method === 'stripe' ? 'border-[#d71208] bg-red-50' : 'border-gray-300'">
            <input type="radio" value="stripe" v-model="form.payment_method" class="accent-[#d71208]" />
            <div>
                <p class="text-sm font-semibold text-gray-800">Pay with Card</p>
                <p class="text-xs text-gray-500">Secure payment via Stripe</p>
            </div>
        </label>

    </div>
    <p v-if="errors.payment_method" class="text-red-500 text-xs mt-1">{{ errors.payment_method[0] }}</p>
</div>

                        <button type="submit" :disabled="submitting"
                            class="mt-2 w-full bg-[#d71208] hover:bg-[#c31007] text-white font-semibold py-3 rounded-lg transition disabled:opacity-50 cursor-pointer">
                            {{ submitting
    ? (form.payment_method === 'stripe' ? 'Redirecting to Stripe...' : 'Placing Order...')
    : (form.payment_method === 'stripe' ? 'Proceed to Payment' : 'Place Order')
}}
                        </button>

                    </form>
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-xl shadow-sm p-6 h-fit">
                    <h2 class="font-semibold text-gray-800 mb-4">Order Summary</h2>

                    <div class="flex flex-col gap-3 mb-4">
                        <div v-for="item in items" :key="item.id" class="flex items-center gap-3">
                            <img :src="'/storage/' + item.product.image" class="w-14 h-14 object-cover rounded-lg" />
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ item.product.name }}</p>
                                <p class="text-xs text-gray-400">Qty: {{ item.quantity }}</p>
                            </div>
                            <p class="text-sm font-semibold text-gray-800">
                                Rs. {{ (Number(item.product.price) * item.quantity).toLocaleString() }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                        <p class="font-bold text-gray-800">Total</p>
                        <p class="font-bold text-[#d71208] text-lg">Rs. {{ total.toLocaleString() }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>