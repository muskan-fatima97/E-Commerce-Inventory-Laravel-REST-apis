<script setup lang="ts">
</script>
<template>
    <nav class="bg-gray-50 px-8 py-4 flex items-center justify-between">

        <div class="text-[#d71208] font-bold text-2xl tracking-wide">
            MyStore
        </div>

        <div class="flex-1 mx-24 border rounded-sm border-gray-300">
            <input type="text" placeholder="Search for products..."
                class="w-full px-4 py-2 rounded-lg text-sm text-gray-800 focus:outline-none" />
        </div>

        <div class="flex items-center gap-3">
            <a href="/inventory"
                class="bg-black text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition">
                Admin
            </a>

        </div>

    </nav>
</template>