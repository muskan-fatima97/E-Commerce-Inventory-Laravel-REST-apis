<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import { ref } from 'vue'

interface OrderItem {
    id: number
    product_name: string
    price: string
    quantity: number
}

interface Order {
    id: number
    customer_name: string
    customer_phone: string
    city: string
    total_amount: string
    status: string
    created_at: string
    items: OrderItem[]
}

const props = defineProps<{
    orders: {
        data: Order[]
        links: Array<any>
        total: number
    }
    filters: {
        status?: string
    }
}>()

const statusFilter = ref(props.filters.status ?? '')

const statusOptions = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-700',
    processing: 'bg-blue-100 text-blue-700',
    shipped: 'bg-purple-100 text-purple-700',
    delivered: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-700',
}

function applyFilter() {

    router.get('/admin/orders', { status: statusFilter.value }, { preserveState: true })
}

const updatingId = ref<number | null>(null)

async function updateStatus(order: Order, newStatus: string) {
    updatingId.value = order.id

    try {
        await axios.patch(`/api/orders/${order.id}/status`, { status: newStatus })
        order.status = newStatus
    } catch (error) {
        console.error(error)
        alert('Failed to update status.')
    } finally {
        updatingId.value = null
    }
}
</script>


<template>

    <div class="flex flex-col gap-6 p-2">

        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">All Orders ({{ orders.total }})</h2>

            <select v-model="statusFilter" @change="applyFilter"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option v-for="s in statusOptions" :key="s" :value="s">{{ s }}</option>
            </select>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Order #</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">City</th>
                        <th class="px-4 py-3">Items</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            <a :href="`/admin/orders/${order.id}`" class="text-blue-600 hover:underline">
                                #{{ order.id }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-800 font-medium">{{ order.customer_name }}</p>
                            <p class="text-gray-400 text-xs">{{ order.customer_phone }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ order.city }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ order.items.length }} item(s)</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            Rs. {{ Number(order.total_amount).toLocaleString() }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded-full font-medium capitalize"
                                :class="statusColors[order.status]">
                                {{ order.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ new Date(order.created_at).toLocaleDateString() }}
                        </td>
                        <td class="px-4 py-3">
                            <select :value="order.status"
                                @change="updateStatus(order, ($event.target as HTMLSelectElement).value)"
                                :disabled="updatingId === order.id"
                                class="text-gray-800 border border-gray-300 rounded-lg px-2 py-1 text-xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                                <option v-for="s in statusOptions" :key="s" :value="s">{{ s }}</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="orders.data.length === 0" class="text-center py-16 text-gray-400">
                <p class="font-semibold">No orders found</p>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center gap-1">
            <template v-for="link in orders.links" :key="link.label">
                <button v-if="link.url" @click="router.get(link.url, {}, { preserveState: true })" v-html="link.label"
                    class="px-3 py-1.5 text-sm rounded-lg border cursor-pointer"
                    :class="link.active ? 'bg-[#d71208] text-white border-[#d71208]' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'" />
                <span v-else v-html="link.label"
                    class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300" />
            </template>
        </div>

    </div>
</template>