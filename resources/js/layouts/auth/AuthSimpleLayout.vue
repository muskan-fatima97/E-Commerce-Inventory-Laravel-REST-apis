<script setup lang="ts">
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10"
        style="background: linear-gradient(135deg, #2563eb 0%, #d71208 55%, #b90d05 100%);"
    >
        <div class="w-full max-w-md rounded-3xl bg-white px-8 py-10 shadow-xl">
            <div class="flex flex-col gap-8">
                <div class="space-y-2 text-center">
                    <h1 class="text-3xl font-bold text-black">{{ title }}</h1>
                    <p class="text-center text-sm text-gray-500">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>