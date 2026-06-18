<script setup lang="ts">
defineOptions({ layout: null })

defineProps<{
    products: {
        data: Array<any>,
        total: number,
        links: Array<any>
    }
}>()
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
            <div v-for="product in products.data" :key="product.id"
                class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 flex flex-col overflow-hidden">
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
                            ${{ product.price }}
                        </span>
                       
                    </div>
                </div>
            </div>
        </div>

        <div v-if="products.data.length === 0" class="flex flex-col items-center justify-center mt-24 text-gray-400">
            <p class="text-xl font-semibold">No products found</p>
            <p class="text-sm mt-1">Add some products to see them here</p>
        </div>

        <!-- <div class="flex justify-center mt-10 gap-2">
            <a v-for="link in products.links" :key="link.label" :href="link.url" v-html="link.label"
                class="px-4 py-2 rounded-lg text-sm border" :class="link.active
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'"></a>
        </div> -->

    </div>
</template>