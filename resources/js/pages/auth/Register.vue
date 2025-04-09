<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    // form.post(route('register'), {
    //     onFinish: () => form.reset('password', 'password_confirmation'),
    //     onError: (errors) => {
    //         if (errors.email) {
    //             alert(errors.email); // ✅ Shows error on UI
    //         }
    //     },
    // });
    axios
        .post(
            'https://a2vwrk4t8f.execute-api.us-east-1.amazonaws.com/default/myMusicLambdaFunction/registration',
            form.data(), // Gets the current form data
            {
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
            },
        )
        .then((response) => {
            // Check if we have a 303 redirect status code
            if (response.data.statusCode === 303) {
                // window.location.href = response.headers.location;
                window.location.href = '/login';
            } else {
                if (response.data && response.data.statusCode === 422) {
                    const validationErrors = response.data.errors || {};

                    // Set form errors for each field
                    Object.keys(validationErrors).forEach((field) => {
                        form.setError(field, Array.isArray(validationErrors[field]) ? validationErrors[field][0] : validationErrors[field]);
                    });
                }
                // Handle normal success
                form.reset('password', 'password_confirmation');
                // Redirect to login page
                console.log(response);
            }
        })
        .catch((error) => {
            console.error('Registration error:', error);
        })
        .finally(() => {
            // Always execute this code after request completes (success or error)
            form.processing = false; // Reset the processing state if you're tracking it
        });
};
</script>

<template>
    <!-- ✅ Background applied directly here -->
    <div class="flex min-h-screen items-center justify-center bg-yellow-400">
        <AuthBase class="bg-yellow-400" title="Create an account" description="Enter your details below to create your account">
            <Head title="Register" />

            <form @submit.prevent="submit" class="flex flex-col gap-6">
                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="name"
                            v-model="form.name"
                            placeholder="Full name"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            :tabindex="2"
                            autocomplete="email"
                            v-model="form.email"
                            placeholder="email@example.com"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">Password</Label>
                        <Input
                            id="password"
                            type="password"
                            required
                            :tabindex="3"
                            autocomplete="new-password"
                            v-model="form.password"
                            placeholder="Password"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">Confirm password</Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            required
                            :tabindex="4"
                            autocomplete="new-password"
                            v-model="form.password_confirmation"
                            placeholder="Confirm password"
                        />
                        <InputError :message="form.errors.password_confirmation" />
                    </div>

                    <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Create account
                    </Button>
                </div>

                <div class="text-center text-sm text-muted-foreground">
                    Already have an account?
                    <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
                </div>
            </form>
        </AuthBase>
    </div>
</template>
