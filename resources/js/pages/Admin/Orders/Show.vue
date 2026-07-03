<script setup lang="ts">
import axios from 'axios'
import { ref } from 'vue'

interface OrderItem {
    id: number
    product_name: string
    price: string
    quantity: number
    product?: {
        image: string
    }
}

interface Order {
    id: number
    customer_name: string
    customer_phone: string
    shipping_address: string
    city: string
    total_amount: string
    status: string
    payment_method: string
    created_at: string
    items: OrderItem[]
}

const props = defineProps<{
    order: Order
}>()

const status = ref(props.order.status)
const updating = ref(false)

const statusOptions = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']

const statusColors: Record<string, string> = {

    pending: 'bg-yellow-100 text-yellow-700',
    processing: 'bg-blue-100 text-blue-700',
    shipped: 'bg-purple-100 text-purple-700',
    delivered: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-700',
}

async function updateStatus() {

    updating.value = true

    try {
        await axios.patch(`/api/orders/${props.order.id}/status`, { status: status.value })
        alert('Status updated successfully.')
    } catch (error) {
        console.error(error)
        alert('Failed to update status.')
    } finally {
        updating.value = false
    }
}
</script>


<template>

    <div class="max-w-4xl">

        <a href="/admin/orders" class="text-sm text-gray-500 hover:text-red-500 mb-4 inline-block">
            ← Back to Orders
        </a>

        <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col gap-6">

            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Order #{{ order.id }}</h2>
                    <p class="text-sm text-gray-400">{{ new Date(order.created_at).toLocaleString() }}</p>
                </div>
                <span class="text-xs px-3 py-1.5 rounded-full font-medium capitalize" :class="statusColors[order.status]">
                    {{ order.status }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-b border-gray-100 py-4">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Customer</p>
                    <p class="text-gray-800 font-medium">{{ order.customer_name }}</p>
                    <p class="text-gray-500 text-sm">{{ order.customer_phone }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Shipping Address</p>
                    <p class="text-gray-800">{{ order.shipping_address }}</p>
                    <p class="text-gray-500 text-sm">{{ order.city }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Payment Method</p>
                    <p class="text-gray-800 uppercase">{{ order.payment_method }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Total Amount</p>
                    <p class="text-[#d71208] font-bold text-lg">Rs. {{ Number(order.total_amount).toLocaleString() }}</p>
                </div>
            </div>

            <div>
                <p class="text-sm font-semibold text-gray-700 mb-3">Items</p>
                <div class="flex flex-col gap-3">
                    <div v-for="item in order.items" :key="item.id"
                        class="flex items-center justify-between border border-gray-100 rounded-lg px-4 py-3">
                        <div class="flex items-center gap-3">
                            <img v-if="item.product?.image" :src="'/storage/' + item.product.image"
                                class="w-12 h-12 object-cover rounded-lg" />
                            <div>
                                <p class="text-gray-800 font-medium text-sm">{{ item.product_name }}</p>
                                <p class="text-gray-400 text-xs">Qty: {{ item.quantity }} × Rs. {{ Number(item.price).toLocaleString() }}</p>
                            </div>
                        </div>
                        <p class="text-gray-800 font-semibold text-sm">
                            Rs. {{ (Number(item.price) * item.quantity).toLocaleString() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-100 pt-4">
                <label class="text-sm font-medium text-gray-700">Update Status:</label>
                <select v-model="status"
                    class="border text-gray-800 border-gray-300 rounded-lg px-3 py-2 text-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option v-for="s in statusOptions" :key="s" :value="s">{{ s }}</option>
                </select>
                <button @click="updateStatus" :disabled="updating"
                    class="bg-[#d71208] hover:bg-[#c31007] text-white text-sm font-semibold px-4 py-2 rounded-lg transition disabled:opacity-50 cursor-pointer">
                    {{ updating ? 'Updating...' : 'Update' }}
                </button>
            </div>

        </div>
    </div>
</template>