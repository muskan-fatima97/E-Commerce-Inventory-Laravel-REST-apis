<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Welcome Back',
        description: 'You need to be signed in to access the Closyyy',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <div
        v-if="status"
        class=" text-center text-sm font-medium text-black"
    >
        {{ status }}
    </div>

    <PasskeyVerify />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="flex flex-col gap-5">
            <div class="grid gap-1.5">
                <Label for="email" class="text-black">Email</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="Email "
                    class="h-12 rounded-full border-gray-300 bg-white px-5 text-black placeholder:text-gray-400"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-1.5">
                                <Label for="password" class="text-black">Password</Label>

                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Password"
                    class="h-12 rounded-full border-gray-300 bg-white px-5 text-black placeholder:text-gray-400"
                />
                <InputError :message="errors.password" />
            </div>

            <div v-if="canResetPassword" class="text-center">
                <TextLink
                    :href="request()"
                    class="text-sm text-black underline"
                    :tabindex="5"
                >
                    Forgot Password?
                </TextLink>
            </div>

            <Button
                type="submit"
                class="mt-2 h-12 w-full rounded-full bg-[#d71208] text-white hover:bg-[#c31007]"
                :tabindex="4"
                :disabled="processing"
            >
                <Spinner v-if="processing" />
                Log in
            </Button>
        </div>

        <!-- <div class="mt-6 flex items-center justify-center gap-2">
            <Label for="remember" class="flex items-center gap-2 text-sm text-gray-500">
                <Checkbox id="remember" name="remember" :tabindex="3" />
                <span>Remember me</span>
            </Label>
        </div> -->

        <div class="mt-2 text-center text-sm text-gray-600">
            Haven't joined yet?
            <TextLink :href="register()" :tabindex="5" class="font-medium text-black underline">
                Sign up
            </TextLink>
        </div>
    </Form>
</template>