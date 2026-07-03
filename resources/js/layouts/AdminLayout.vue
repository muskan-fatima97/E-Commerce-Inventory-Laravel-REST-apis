<script setup lang="ts">
import {
    LayoutDashboard,
    Package,
    ShoppingBag,
    Menu,
    X,
    Store,
} from 'lucide-vue-next'
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'


const page = usePage()
const sidebarOpen = ref(true)

const navItems = [
    { name: 'Dashboard', href: '/admin', icon: LayoutDashboard },
    { name: 'Products', href: '/inventory', icon: Package },
    { name: 'Orders', href: '/admin/orders', icon: ShoppingBag },
]

function isActive(href: string) {
    return page.url === href || page.url.startsWith(href + '/')
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex">

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="bg-gray-900 text-white flex flex-col transition-all duration-300 shrink-0"
        >
            <div class="flex items-center justify-between px-4 py-5 border-b border-gray-800">
                <span v-if="sidebarOpen" class="text-lg font-bold text-[#d71208] tracking-wide">
                    MyStore Admin
                </span>
                <button @click="sidebarOpen = !sidebarOpen" class="cursor-pointer text-gray-400 hover:text-white">
                    <Menu v-if="!sidebarOpen" class="w-5 h-5" />
                    <X v-else class="w-5 h-5" />
                </button>
            </div>

            <nav class="flex-1 px-2 py-4 flex flex-col gap-1">
               <a 
                    v-for="item in navItems"
                    :key="item.name"
                    :href="item.href"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition"
                    :class="isActive(item.href)
                        ? 'bg-[#d71208] text-white'
                        : 'text-gray-300 hover:bg-gray-800 hover:text-white'"
                >
                    <component :is="item.icon" class="w-5 h-5 shrink-0" />
                    <span v-if="sidebarOpen">{{ item.name }}</span>
                </a>
            </nav>

            <div class="px-2 py-4 border-t border-gray-800">
                <a
                    href="/store"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition"
                >
                    <Store class="w-5 h-5 shrink-0" />
                    <span v-if="sidebarOpen">View Store</span>
                </a>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 min-w-0">
           

            <main class="">
                <slot />
            </main>
        </div>

    </div>
</template>