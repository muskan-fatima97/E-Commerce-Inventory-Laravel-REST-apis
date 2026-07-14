<script setup lang="ts">
import { ref, onMounted } from 'vue'
import StoreNavbar from '@/components/StoreNavbar.vue'

defineOptions({ layout: null })

const menProducts = ref<any[]>([])
const womenProducts = ref<any[]>([])
const kidsProducts = ref<any[]>([])
const loading = ref(true)

async function fetchCategory(category: string) {
    const response = await fetch(`/api/product?category=${category}&limit=8`);
    const json = await response.json();
    return json.data.data
}

onMounted(async () => {
    const [men, women, kids] = await Promise.all([
        fetchCategory('Men'),
        fetchCategory('Women'),
        fetchCategory('Kids'),
    ])
    menProducts.value = men;
    womenProducts.value = women;
    kidsProducts.value = kids;
    loading.value = false;
})
</script>

<template>
    <div class="min-h-screen bg-white">

        <!-- Navbar -->
        <StoreNavbar />

        <!-- Hero Banner -->
        <div class="bg-[#d71208] px-6 sm:px-10 lg:px-16 py-10 lg:py-16 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="text-white max-w-2xl">
                <h1 class="text-3xl sm:text-4xl lg:text-[63px] font-bold leading-tight mb-4">
                    Sell What You Don't Wear.<br />
                    Shop What You Love.
                </h1>
                <p class="text-red-100 text-base sm:text-lg lg:text-[26px] leading-relaxed">
                    Turn your wardrobe into earnings — sell your pre-loved or brand-new
                    clothes easily and find stylish deals that don't break the bank.
                </p>
            </div>
            <div class="hidden md:block shrink-0">
                <img src="/hero_image.png" alt="Clothes" class="h-64 w-56 lg:h-96 lg:w-80 object-cover rounded-xl opacity-90" />
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="px-6 sm:px-10 lg:px-16 pt-6 lg:pt-9 flex gap-3 lg:gap-4 overflow-x-auto">
            <a v-for="cat in ['Men', 'Women', 'Kids']" :key="cat" :href="`/store?category=${cat}`"
                class="px-6 sm:px-8 lg:px-12 py-2 border border-[#d71208] text-[#d71208] rounded-sm font-semibold text-sm lg:text-[16px] uppercase tracking-wide hover:bg-red-50 transition whitespace-nowrap shrink-0">
                {{ cat }}'s
            </a>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center mt-24">
            <div class="w-10 h-10 border-4 border-[#d71208] border-t-transparent rounded-full animate-spin"></div>
        </div>

        <!-- All Collections -->
        <div v-else class="px-6 sm:px-10 lg:px-16 py-8 space-y-8">

            <!-- Men Collection -->
            <div v-if="menProducts.length > 0" class="mt-5">
                <h2 class="text-lg lg:text-[24px] font-bold text-gray-900 mb-4">Men Collection</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 lg:gap-4">
                    <a v-for="product in menProducts" :key="product.id" :href="`/product/${product.id}`"
                        class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition cursor-pointer">
                        <div class="h-40 sm:h-52 lg:h-64 bg-gray-100 overflow-hidden">
                            <img :src="'/storage/' + product.image" :alt="product.name"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
                        </div>
                         <div class="px-3 py-3 lg:py-5">
                            <p class="text-[#d71208] font-bold text-sm lg:text-md">Rs. {{ Number(product.price ).toLocaleString()}}</p>
                            <p class="text-gray-700 text-sm lg:text-md mt-1 line-clamp-2">{{ product.name }}</p>
                            <p class="text-gray-400 text-xs lg:text-md mt-1">{{ product.brand }}</p>
                        </div>
                    </a>
                </div>
                <div class="text-center mt-6">
                    <a href="/store?category=Men"
                        class="border border-[#d71208] text-[#d71208] px-8 py-2 text-sm font-semibold hover:bg-red-50 transition">
                        VIEW ALL 
                    </a>
                </div>
            </div>

            <!-- Women Collection -->
            <div v-if="womenProducts.length > 0">
                <h2 class="text-lg lg:text-[24px] font-bold text-gray-900 mb-4">Women Collection</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 lg:gap-4">
                    <a v-for="product in womenProducts" :key="product.id" :href="`/product/${product.id}`"
                        class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition cursor-pointer">
                        <div class="h-40 sm:h-52 lg:h-64 bg-gray-100 overflow-hidden">
                            <img :src="'/storage/' + product.image" :alt="product.name"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
                        </div>
                        <div class="px-3 py-3 lg:py-5">
                            <p class="text-[#d71208] font-bold text-sm lg:text-md">Rs. {{ Number(product.price ).toLocaleString()}}</p>
                            <p class="text-gray-700 text-sm lg:text-md mt-1 line-clamp-2">{{ product.name }}</p>
                            <p class="text-gray-400 text-xs lg:text-md mt-1">{{ product.brand }}</p>
                        </div>
                    </a>
                </div>
                <div class="text-center mt-6">
                    <a href="/store?category=Women"
                        class="border border-[#d71208] text-[#d71208] px-8 py-2 text-sm font-semibold hover:bg-red-50 transition">
                        VIEW ALL 
                    </a>
                </div>
            </div>

            <!-- Kids Collection -->
            <div v-if="kidsProducts.length > 0">
                <h2 class="text-lg lg:text-[24px] font-bold text-gray-900 mb-4">Kids Collection</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 lg:gap-4">
                    <a v-for="product in kidsProducts" :key="product.id" :href="`/product/${product.id}`"
                        class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition cursor-pointer">
                        <div class="h-40 sm:h-52 lg:h-64 bg-gray-100 overflow-hidden">
                            <img :src="'/storage/' + product.image" :alt="product.name"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
                        </div>
                        <div class="px-3 py-3 lg:py-5">
                            <p class="text-[#d71208] font-bold text-sm lg:text-md">Rs.{{ product.price }}</p>
                            <p class="text-gray-700 text-sm lg:text-md mt-1 line-clamp-2">{{ product.name }}</p>
                            <p class="text-gray-400 text-xs lg:text-md mt-1">{{ product.brand }}</p>
                        </div>
                    </a>
                </div>
                <div class="text-center mt-6">
                    <a href="/store?category=Kids"
                        class="border border-[#d71208] text-[#d71208] px-8 py-2 text-sm font-semibold hover:bg-red-50 transition">
                        VIEW ALL 
                    </a>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="bg-white border-t px-6 sm:px-8 py-8 mt-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="font-bold text-gray-800 text-lg">MyStore</p>
                <p class="text-xs text-gray-400 mt-1">Privacy Policy</p>
            </div>
            <div class="flex gap-3">
                <a href="#"
                    class="w-8 h-8 bg-[#d71208] rounded-full flex items-center justify-center text-white text-xs">f</a>
                <a href="#"
                    class="w-8 h-8 bg-[#d71208] rounded-full flex items-center justify-center text-white text-xs">in</a>
                <a href="#"
                    class="w-8 h-8 bg-[#d71208] rounded-full flex items-center justify-center text-white text-xs">ig</a>
            </div>
        </footer>

    </div>
</template>