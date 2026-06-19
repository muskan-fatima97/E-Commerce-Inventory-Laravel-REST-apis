<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: null })

const form = useForm({
    name: '',
    description: '',
    price: '',
    category: '',
    brand: '',
    size: '',
    color: '',
    stock_quantity: '',
    image: null as File | null,
})

function handleImage(e: Event) {
    const target = e.target as HTMLInputElement
    if (target.files && target.files[0]) {
        form.image = target.files[0]
    }
}

function submit() {
    form.post('/products', {
        forceFormData: true,
    })
}
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center ">
        <div class="bg-white rounded-2xl shadow-md w-full max-w-3xl p-8">

            <h1 class="text-2xl font-bold text-gray-800 mb-6">Add New Product</h1>

            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                        <input v-model="form.name" type="text" placeholder="Enter item name"
                            class="text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>

                    <!-- Brand -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                        <input v-model="form.brand" type="text" placeholder="Enter item's brand"
                            class="text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        <p v-if="form.errors.brand" class="text-red-500 text-xs mt-1">{{ form.errors.brand }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select v-model="form.category"
                            class="cursor-pointer text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" >Select category</option>
                            <option value="Men" class="cursor-pointer">Men</option>
                            <option value="Women" class="cursor-pointer">Women</option>
                            <option value="Kids" class="cursor-pointer">Kids</option>
                        </select>
                        <p v-if="form.errors.category" class="text-red-500 text-xs mt-1">{{ form.errors.category }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input v-model="form.color" type="text" placeholder="Enter Item's color"
                            class="text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        <p v-if="form.errors.color" class="text-red-500 text-xs mt-1">{{ form.errors.color }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                        <input v-model="form.price" type="number" step="0" placeholder="0"
                            class="text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        <p v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                        <input v-model="form.stock_quantity" type="number" placeholder="0"
                            class="text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        <p v-if="form.errors.stock_quantity" class="text-red-500 text-xs mt-1">{{ form.errors.stock_quantity }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                        <div class="flex gap-4">
                            <label v-for="s in ['S', 'M', 'L', 'XL', 'XXL']" :key="s"
                                class="flex items-center gap-1 cursor-pointer">
                                <input type="radio" :value="s" v-model="form.size" class="accent-blue-600 cursor-pointer"/>
                                <span class="text-sm text-black">{{ s }}</span>
                            </label>
                        </div>
                        <p v-if="form.errors.size" class="text-red-500 text-xs mt-1">{{ form.errors.size }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea v-model="form.description" rows="3" placeholder="Product description..."
                            class="text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        <p v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
                        <input type="file" accept="image/*" @change="handleImage"
                            class="cursor-pointer text-black w-full border border-gray-300 rounded-lg px-4 py-2 text-sm"/>
                        <p v-if="form.errors.image" class="text-red-500 text-xs mt-1">{{ form.errors.image }}</p>
                    </div>

                </div>

                <!-- Submit -->
                <button type="submit" :disabled="form.processing"
                    class="mt-6 cursor-pointer w-full bg-[#d71208] hover:bg-[#c31007] text-white font-semibold py-2 px-4 rounded-lg transition duration-200 disabled:opacity-50">
                    {{ form.processing ? 'Adding...' : 'Add Product' }}
                </button>

            </form>

        </div>
    </div>
</template>