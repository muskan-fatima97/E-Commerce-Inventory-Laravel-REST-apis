<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { ShoppingCart, User, LogOut, Package } from 'lucide-vue-next'
import axios from 'axios'

const page = usePage()
const cartCount = ref(0)

const user = computed(() => (page.props.auth as any)?.user ?? null)
const isAdmin = computed(() => user.value?.role === 'admin')

const menuOpen = ref(false)

async function fetchCartCount() {
    try {
        const response = await axios.get('/api/cart/count')
        cartCount.value = response.data.count
    } catch (error) {
        console.error(error)
    }
}

onMounted(fetchCartCount)

function logout() {
    router.post('/logout')
}
</script>

<template>
    <nav class="bg-gray-50 px-8 py-4 flex items-center justify-between relative">

        <a href="/store" class="text-[#d71208] font-bold text-2xl tracking-wide">
            MyStore
        </a>

        <div class="flex-1 mx-24 border rounded-sm border-gray-300">
            <input type="text" placeholder="Search for products..."
                class="w-full px-4 py-2 rounded-lg text-sm text-gray-800 focus:outline-none" />
        </div>

        <div class="flex items-center gap-3">

            <a href="/cart" class="relative p-2 rounded-lg hover:bg-gray-200 transition">
                <ShoppingCart class="w-6 h-6 text-gray-700" />
                <span class="absolute -top-1 -right-1 bg-[#d71208] text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">
                    {{ cartCount }}
                </span>
            </a>

            <!-- Logged in -->
            <div v-if="user" class="relative">
                <button @click="menuOpen = !menuOpen"
                    class="flex items-center gap-2 bg-black text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition cursor-pointer">
                    <User class="w-4 h-4" />
                    {{ user.name.split(' ')[0] }}
                </button>

                <div v-if="menuOpen" @click.outside="menuOpen = false"
                    class="absolute right-0 top-12 bg-white shadow-lg rounded-lg py-2 w-48 border border-gray-100 z-10">

                    <a v-if="isAdmin" href="/admin"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <Package class="w-4 h-4" /> Admin Panel
                    </a>

                    <a href="/orders"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <Package class="w-4 h-4" /> My Orders
                    </a>

                    <button @click="logout"
                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-gray-50 cursor-pointer">
                        <LogOut class="w-4 h-4" /> Logout
                    </button>
                </div>
            </div>

            <!-- Guest -->
            <div v-else class="flex items-center gap-2">
                <a href="/login"
                    class="text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                    Login
                </a>
                <a href="/register"
                    class="bg-black text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition">
                    Register
                </a>
            </div>

        </div>

    </nav>
</template>