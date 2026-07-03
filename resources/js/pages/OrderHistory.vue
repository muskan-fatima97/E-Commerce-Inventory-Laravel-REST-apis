<script setup lang="ts">
import { router } from '@inertiajs/vue3'
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
    total_amount: string
    status: string
    created_at: string
    items: OrderItem[]
}

defineProps<{
    orders: {
        data: Order[]
        links: Array<any>
    }
}>()

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-700',
    processing: 'bg-blue-100 text-blue-700',
    shipped: 'bg-purple-100 text-purple-700',
    delivered: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-700',
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">

        <StoreNavbar />

        <div class="max-w-3xl mx-auto px-8 py-8">

            <h1 class="text-2xl font-bold text-gray-800 mb-6">My Orders</h1>

            <div v-if="orders.data.length === 0" class="text-center py-24 text-gray-400">
                <p class="text-xl font-semibold">No orders yet</p>
                <a href="/store" class="text-[#d71208] font-semibold mt-2 inline-block">Start Shopping</a>
            </div>

            <div v-else class="flex flex-col gap-4">
                <div v-for="order in orders.data" :key="order.id"
                    class="bg-white rounded-xl shadow-sm p-5">

                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="font-semibold text-gray-800">Order #{{ order.id }}</p>
                            <p class="text-xs text-gray-400">{{ new Date(order.created_at).toLocaleDateString() }}</p>
                        </div>
                        <span class="text-xs px-3 py-1 rounded-full font-medium capitalize" :class="statusColors[order.status]">
                            {{ order.status }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-1 border-t border-gray-100 pt-3">
                        <div v-for="item in order.items" :key="item.id" class="flex justify-between text-sm text-gray-600">
                            <span>{{ item.product_name }} × {{ item.quantity }}</span>
                            <span>Rs. {{ (Number(item.price) * item.quantity).toLocaleString() }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between font-bold text-gray-800 mt-3 pt-3 border-t border-gray-100">
                        <span>Total</span>
                        <span>Rs. {{ Number(order.total_amount).toLocaleString() }}</span>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center gap-1 mt-2">
                    <template v-for="link in orders.links" :key="link.label">
                        <button v-if="link.url" @click="router.get(link.url, {}, { preserveState: true })"
                            v-html="link.label"
                            class="px-3 py-1.5 text-sm rounded-lg border cursor-pointer"
                            :class="link.active ? 'bg-[#d71208] text-white border-[#d71208]' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'" />
                        <span v-else v-html="link.label"
                            class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300" />
                    </template>
                </div>
            </div>

        </div>
    </div>
</template>