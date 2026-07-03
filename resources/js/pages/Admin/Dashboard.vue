<script setup lang="ts">
import { Package, ShoppingBag, Clock, DollarSign, AlertTriangle } from 'lucide-vue-next'

interface Stats {
    totalProducts: number
    totalOrders: number
    pendingOrders: number
    totalRevenue: number
    outOfStockCount: number
}

interface LowStockProduct {
    id: number
    name: string
    stock_quantity: number
    image: string
}

interface OrderItem {
    id: number
    product_name: string
    quantity: number
}

interface RecentOrder {
    id: number
    customer_name: string
    total_amount: string
    status: string
    created_at: string
    items: OrderItem[]
}

defineProps<{
    stats: Stats
    lowStockProducts: LowStockProduct[]
    recentOrders: RecentOrder[]
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
    <div class="flex flex-col gap-6 p-2">

        <h2 class="text-xl font-bold text-gray-800">Dashboard</h2>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                    <Package class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase">Total Products</p>
                    <p class="text-xl font-bold text-gray-800">{{ stats.totalProducts }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                    <ShoppingBag class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase">Total Orders</p>
                    <p class="text-xl font-bold text-gray-800">{{ stats.totalOrders }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center">
                    <Clock class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase">Pending Orders</p>
                    <p class="text-xl font-bold text-gray-800">{{ stats.pendingOrders }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                    <DollarSign class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase">Revenue</p>
                    <p class="text-xl font-bold text-gray-800">Rs. {{ Number(stats.totalRevenue).toLocaleString() }}</p>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Low Stock -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-2 mb-4">
                    <AlertTriangle class="w-5 h-5 text-orange-500" />
                    <h3 class="font-semibold text-gray-800">Low Stock Alert</h3>
                </div>

                <div v-if="lowStockProducts.length === 0 && stats.outOfStockCount === 0"
                    class="text-sm text-gray-400 py-6 text-center">
                    All products are well stocked
                </div>

                <div v-else class="flex flex-col gap-3">
                    <div v-if="stats.outOfStockCount > 0"
                        class="bg-red-50 border border-red-100 rounded-lg px-4 py-2 text-sm text-red-600 font-medium">
                        {{ stats.outOfStockCount }} product(s) completely out of stock
                    </div>

                    <div v-for="product in lowStockProducts" :key="product.id"
                        class="flex items-center gap-3 border border-gray-100 rounded-lg px-3 py-2">
                        <img :src="'/storage/' + product.image" class="w-10 h-10 object-cover rounded-lg" />
                        <p class="flex-1 text-sm text-gray-700">{{ product.name }}</p>
                        <span class="text-xs bg-orange-100 text-orange-600 px-2 py-1 rounded-full font-medium">
                            {{ product.stock_quantity }} left
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Recent Orders</h3>

                <div v-if="recentOrders.length === 0" class="text-sm text-gray-400 py-6 text-center">
                    No orders yet
                </div>

                <div v-else class="flex flex-col gap-3">
                    <a v-for="order in recentOrders" :key="order.id" :href="`/admin/orders/${order.id}`"
                        class="flex items-center justify-between border border-gray-100 rounded-lg px-3 py-2 hover:bg-gray-50 transition">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ order.id }}.  {{ order.customer_name }}</p>
                            <p class="text-xs text-gray-400">{{ order.items.length }} item(s)</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800">Rs. {{ Number(order.total_amount).toLocaleString() }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize" :class="statusColors[order.status]">
                                {{ order.status }}
                            </span>
                        </div>
                    </a>
                </div>
            </div>

        </div>

    </div>
</template>