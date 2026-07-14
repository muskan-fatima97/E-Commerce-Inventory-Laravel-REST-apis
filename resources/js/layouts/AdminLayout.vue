<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3'
import {
    LayoutDashboard,
    Package,
    ShoppingBag,
    Menu,
    X,
    Store,
    LogOut,
} from 'lucide-vue-next'
import { ref } from 'vue'


const page = usePage()
const sidebarOpen = ref(true)          // desktop collapse toggle (unchanged)
const mobileSidebarOpen = ref(false)   // mobile/tablet drawer toggle (new)

const navItems = [
    { name: 'Dashboard', href: '/admin', icon: LayoutDashboard },
    { name: 'Products', href: '/inventory', icon: Package },
    { name: 'Orders', href: '/admin/orders', icon: ShoppingBag },
]

function isActive(href: string) {
    if (href === '/admin') {
        return page.url === '/admin'
    }
    return page.url === href || page.url.startsWith(href + '/')
}

function toggleSidebar() {
    sidebarOpen = !sidebarOpen
    mobileSidebarOpen.value = false
}

function logout() {
    router.post('/logout')
}
</script>

<template>
    <div class="h-screen bg-gray-50 flex overflow-hidden">

        <!-- Mobile/tablet backdrop -->
        <div
            v-if="mobileSidebarOpen"
            @click="mobileSidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        ></div>

        <!-- Sidebar -->
        <aside
            :class="[
                sidebarOpen ? 'lg:w-64' : 'lg:w-20',
                mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
            class="fixed lg:static inset-y-0 left-0 z-50 w-64 h-screen bg-gray-900 text-white flex flex-col transition-all duration-300 shrink-0"
        >
            <div class="flex items-center justify-between px-4 py-5 border-b border-gray-800">
                <span v-if="sidebarOpen" class="text-lg font-bold text-[#d71208] tracking-wide">
                    Admin Panel
                </span>
                <button @click="toggleSidebar" class="cursor-pointer text-gray-400 hover:text-white">
                    <Menu v-if="!sidebarOpen" class="w-5 h-5" />
                    <X v-else class="w-5 h-5" />
                </button>
            </div>

            <nav class="flex-1 px-2 py-4 flex flex-col gap-1 overflow-y-auto">
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

            <div class="px-2 py-4 border-t border-gray-800 flex flex-col gap-1">
                <a
                    href="/store"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition"
                >
                    <Store class="w-5 h-5 shrink-0" />
                    <span v-if="sidebarOpen">View Store</span>
                </a>

                <button
                    @click="logout"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-400 hover:bg-gray-800 hover:text-red-300 transition cursor-pointer w-full"
                >
                    <LogOut class="w-5 h-5 shrink-0" />
                    <span v-if="sidebarOpen">Logout</span>
                </button>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 min-w-0 h-screen overflow-y-auto">

            <!-- Mobile/tablet top bar -->
            <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200 sticky top-0 z-30">
                <button @click="mobileSidebarOpen = true" class="text-gray-600 cursor-pointer">
                    <Menu class="w-6 h-6" />
                </button>
                <span class="text-sm font-semibold text-gray-800">Admin Panel</span>
                <div class="w-6"></div>
            </div>

            <main class="p-4 lg:p-8">
                <slot />
            </main>
        </div>

    </div>
</template>