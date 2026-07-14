<script setup lang="ts">
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex h-dvh flex-col items-center justify-center p-6 md:p-10"
        style="background: linear-gradient(135deg, #2563eb 0%, #d71208 55%, #b90d05 100%);"
    >
        <div class="grid w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-xl lg:grid-cols-2">

            <!-- Left: form column -->
            <div class="flex flex-col justify-center px-8 py-10 md:px-10">
                <div class="mb-6">
                    <h1 v-if="title" class="text-3xl font-bold leading-tight text-black">
                        {{ title }}
                    </h1>
                    <p v-if="description" class="mt-2 text-sm text-gray-500">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>

            <!-- Right: image column with overlay quote -->
            <div class="relative hidden min-h-[420px] lg:block">
                <img
                    src="/img_1.png"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                <p class="absolute bottom-8 left-8 right-8 text-2xl font-bold leading-snug text-white">
                    "Give your clothes a new life — and earn from it."
                </p>
            </div>

        </div>
    </div>
</template>