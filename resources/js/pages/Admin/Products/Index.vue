<script setup lang="ts">

import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, watch } from 'vue';

const props = defineProps<{
    products: {
        data: Array<any>,
        total: number,
        links: Array<any>
    },
    filters?: {
        search?: string
    }
}>()

const search = ref(props.filters?.search ?? '')

let debounceTimer: ReturnType<typeof setTimeout>

watch(search, (value) => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => {
        router.get('/inventory', { search: value }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        })
    }, 350)
})

async function deleteProduct(id: number) {

    const confirmed = window.confirm("Are you sure you want to delete this product?");

    if (!confirmed) {
        return;
    }

    try {
        await axios.delete(`/api/product/${id}`);
        alert("Product deleted successfully.");
        router.reload();
    } catch (error) {
        console.error(error);
        alert("Failed to delete product.");
    }
}
</script>

<template>
    <div class="min-h-screen p-2">

         <div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Product Inventory</h2>
                <p class="text-gray-500 text-sm mt-1">{{ products.total }} products</p>
            </div>

            <div class="w-full lg:flex-1 lg:flex lg:justify-center order-3 lg:order-none">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search by name, brand, category..."
                    class="text-black w-full max-w-md border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                />
            </div>

            <a href="/products/create"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg transition duration-200 whitespace-nowrap">
                Add Product
            </a>
        </div>

        <!-- Desktop table (unchanged, lg and above) -->
        <div class="hidden lg:block bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Brand</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sizes</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Color</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="product in products.data" :key="product.id"
                            class="hover:bg-gray-50 transition">

                            <td class="px-4 py-3">
                                <div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-100">
                                    <img :src="'/storage/' + product.image" :alt="product.name"
                                        class="w-full h-full object-cover" />
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-gray-800">{{ product.name }}</p>
                                <p class="text-xs text-gray-400 line-clamp-1 max-w-[200px]">{{ product.description }}</p>
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">
                                    {{ product.category }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                    {{ product.brand }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-600">
                                    {{ product.sizes?.join(', ') }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-600">{{ product.color }}</span>
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-sm font-bold text-red-600">
                                    Rs. {{ Number(product.price).toLocaleString() }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full" :class="product.stock_quantity > 0
                                    ? 'bg-green-100 text-green-600'
                                    : 'bg-red-100 text-red-500'">
                                    {{ product.stock_quantity > 0 ? product.stock_quantity + ' in stock' : 'Out of stock' }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Edit -->
                                    <button
                                        @click="router.visit(`/product/${product.id}/edit`)"
                                        class="p-2 rounded-full hover:bg-blue-100 text-blue-600 transition cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="w-4 h-4"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor"
                                             stroke-width="2">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16.862 4.487a2.1 2.1 0 113 2.97L8.5 18.82 4 20l1.18-4.5L16.862 4.487z" />
                                        </svg>
                                    </button>

                                    <!-- Delete -->
                                    <button
                                        @click="deleteProduct(product.id)"
                                        class="p-2 rounded-full hover:bg-red-100 text-red-600 transition cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="w-4 h-4"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor"
                                             stroke-width="2">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M19 7L18.132 19.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.994-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile/tablet card list (below lg) -->
        <div class="lg:hidden flex flex-col gap-4">
            <div v-for="product in products.data" :key="product.id"
                class="bg-white rounded-xl shadow-md p-4 flex gap-3">

                <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                    <img :src="'/storage/' + product.image" :alt="product.name"
                        class="w-full h-full object-cover" />
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ product.name }}</p>
                        <div class="flex items-center gap-1 shrink-0">
                            <button
                                @click="router.visit(`/product/${product.id}/edit`)"
                                class="p-1.5 rounded-full hover:bg-blue-100 text-blue-600 transition cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-4 h-4"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="2">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16.862 4.487a2.1 2.1 0 113 2.97L8.5 18.82 4 20l1.18-4.5L16.862 4.487z" />
                                </svg>
                            </button>
                            <button
                                @click="deleteProduct(product.id)"
                                class="p-1.5 rounded-full hover:bg-red-100 text-red-600 transition cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-4 h-4"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="2">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M19 7L18.132 19.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.994-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 line-clamp-1 mt-0.5">{{ product.description }}</p>

                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">{{ product.category }}</span>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ product.brand }}</span>
                        <span v-if="product.sizes?.length" class="text-xs border border-gray-300 text-gray-600 px-2 py-0.5 rounded-full">
                            {{ product.sizes.join(', ') }}
                        </span>
                        <span v-if="product.color" class="text-xs border border-gray-300 text-gray-600 px-2 py-0.5 rounded-full">
                            {{ product.color }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm font-bold text-red-600">
                            Rs. {{ Number(product.price).toLocaleString() }}
                        </span>
                        <span class="text-xs px-2 py-1 rounded-full" :class="product.stock_quantity > 0
                            ? 'bg-green-100 text-green-600'
                            : 'bg-red-100 text-red-500'">
                            {{ product.stock_quantity > 0 ? product.stock_quantity + ' in stock' : 'Out of stock' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="products.data.length === 0" class="flex flex-col items-center justify-center mt-24 text-gray-400">
            <p class="text-xl font-semibold">No products found</p>
            <p class="text-sm mt-1">Add some products to see them here</p>
        </div>

    </div>
</template>