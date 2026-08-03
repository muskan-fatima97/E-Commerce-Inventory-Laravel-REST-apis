<script setup lang="ts">
import StoreNavbar from '@/components/StoreNavbar.vue'

defineOptions({ layout: null })

interface OrderItem {
    id: number
    product_name: string
    price: string
    quantity: number
}

interface Order {
    id: number
    customer_name: string
    total_amount: string
    shipping_address: string
    city: string
    items: OrderItem[]
}

defineProps<{
    order: Order
}>()
</script>

<template>
    <div class="min-h-screen bg-gray-50">

        <StoreNavbar />

        <div class="max-w-xl mx-auto px-8 py-16 text-center">

            <div class="bg-white rounded-2xl shadow-sm p-8">

                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                    ✓
                </div>

                <h1 class="text-2xl font-bold text-gray-800 mb-2">Order Placed Successfully!</h1>
                <p class="text-gray-500 mb-6">Thank you, {{ order.customer_name }}. Your order #{{ order.id }} has been received.</p>

                <div class="text-left bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-600 mb-2">Shipping to: {{ order.shipping_address }}, {{ order.city }}</p>
                    <div class="flex flex-col gap-1 mt-3">
                        <div v-for="item in order.items" :key="item.id" class="flex justify-between text-sm text-gray-700">
                            <span>{{ item.product_name }} × {{ item.quantity }}</span>
                            <span>Rs. {{ (Number(item.price) * item.quantity).toLocaleString() }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between font-bold text-gray-800 mt-3 pt-3 border-t border-gray-200">
                        <span>Total</span>
                        <span>Rs. {{ Number(order.total_amount).toLocaleString() }}</span>
                    </div>
                </div>

                <p class="text-gray-500 mb-4">
    Payment Method: <span class="font-semibold text-gray-800">
        {{ order.payment_method === 'stripe' ? 'Card Payment' : 'Cash on Delivery' }}
    </span>
    <span v-if="order.payment_status === 'paid'" class="ml-2 text-green-600 font-semibold">✓ Paid</span>
</p>

                <a href="/store" class="inline-block bg-gray-900 text-white font-semibold px-6 py-3 rounded-lg hover:bg-gray-700 transition">
                    Continue Shopping
                </a>

            </div>

        </div>
    </div>
</template>