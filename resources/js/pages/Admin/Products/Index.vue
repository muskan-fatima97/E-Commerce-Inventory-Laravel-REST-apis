<script setup lang="ts">

import { router } from '@inertiajs/vue3';

import axios from 'axios';

// defineOptions({ layout: null })

defineProps<{
    products: {
        data: Array<any>,
        total: number,
        links: Array<any>
    }
}>()


async function deleteProduct(id: number) {

    const confirmed = window.confirm("Are you sure you want to delete this product?");

    if (!confirmed) {
    return;
}

    try {

        await axios.delete(`/api/product/${id}`);

        alert("Product deleted successfully.");

        // Reload inventory page
        router.reload();

    } catch (error) {

        console.error(error);
        alert("Failed to delete product.");

    }
}
</script>

<template>
    <div class="min-h-screen bg-blue-50 p-8">

        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Product Inventory</h1>
                <!-- <p class="text-gray-500 mt-1">Browsing {{ products.total }} products</p> -->
            </div>

            <a href="/products/create"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg transition duration-200">
                Add Product
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
           <div
    v-for="product in products.data"
    :key="product.id"
    class="group relative bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden">
    <!-- Edit & Delete Buttons -->
<div
    class="absolute top-3 right-3 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-20">

    <!-- Edit -->
    
    <button
    @click="router.visit(`/product/${product.id}/edit`)"
    class="bg-white p-2 rounded-full shadow-md hover:bg-blue-100 text-blue-600 transition cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg"
         class="w-5 h-5"
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
    class="bg-white p-2 rounded-full shadow-md hover:bg-red-100 text-red-600 transition cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg"
         class="w-5 h-5"
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
                <div class="w-full h-48 bg-gray-200 overflow-hidden  border-b-black">
                    <img :src="'/storage/' + product.image" :alt="product.name"
                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
                </div>
                <div class="px-4 pb-2 flex flex-col flex-1">
                                        <div class="mt-4 flex items-center justify-between">

                    <h2 class="text-lg font-semibold text-gray-800 mb-1">
                        {{ product.name }}
                    </h2>
                     <span class="text-xs px-2 py-1 rounded-full" :class="product.stock_quantity > 0
                            ? 'bg-green-100 text-green-600'
                            : 'bg-red-100 text-red-500'">
                            {{ product.stock_quantity > 0 ? product.stock_quantity + ' in stock' : 'Out of stock' }}
                        </span>
                        </div>
                    <div class="flex gap-2 mb-2">
                        <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">{{ product.category
                        }}</span>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ product.brand
                        }}</span>
                    </div>
                    <p class="text-gray-500 text-sm flex-1 line-clamp-2">
                        {{ product.description }}
                    </p>
                    <div class="flex gap-2 mt-2">
                        <span class="text-xs border border-gray-300 text-gray-600 px-2 py-1 rounded">Size: {{
                            product.size }}</span>
                        <span class="text-xs border border-gray-300 text-gray-600 px-2 py-1 rounded">Color: {{
                            product.color }}</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-red-600 font-bold text-lg">
                            Rs. {{ Number(product.price).toLocaleString() }}
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