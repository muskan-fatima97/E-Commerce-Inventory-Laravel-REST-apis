<script setup lang="ts">

import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';
import AuthSplitLayout from '@/layouts/auth/AuthSplitLayout.vue';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        component: AuthSplitLayout,
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});
</script>

<template>
    
    <Head title="Register" />
 

    
    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-5">
            <div class="grid gap-1.5">
                <Label for="name" class="text-black">Name</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Full name"
                    class="h-12 rounded-full border-gray-300 bg-white px-5 text-black placeholder:text-gray-400"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-1.5">
                <Label for="email" class="text-black">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="email@example.com"
                    class="h-12 rounded-full border-gray-300 bg-white px-5 text-black placeholder:text-gray-400"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-1.5">
                <Label for="password" class="text-black">Password</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Password"
                    :passwordrules="passwordRules"
                    class="h-12 rounded-full border-gray-300 bg-white px-5 text-black placeholder:text-gray-400"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-1.5">
                <Label for="password_confirmation" class="text-black">Confirm password</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                    :passwordrules="passwordRules"
                    class="h-12 rounded-full border-gray-300 bg-white px-5 text-black placeholder:text-gray-400"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 h-12 w-full rounded-full bg-[#d71208] text-white hover:bg-[#c31007]"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                Create account
            </Button>
        </div>

        <div class="text-center text-sm text-gray-600">
            Already have an account?
            <TextLink
                :href="login()"
                class="font-medium text-black underline"
                :tabindex="6"
                >Log in</TextLink
            >
        </div>
    </Form>
</template>