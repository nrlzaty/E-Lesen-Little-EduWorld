<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log Masuk" />

    <div class="flex h-screen bg-gray-100">
        <!-- Left Section: Login Form -->
        <div class="w-full max-w-md p-8 bg-white shadow-lg flex flex-col justify-center items-center">
            <div class="mb-6">
              
                <AuthenticationCardLogo />
                    <div class="text-xl font-semibold text-gray-700 text-center mb-2">SELAMAT KEMBALI</div>
                <h2 class="text-lg text-gray-600 text-center mb-2">LOG MASUK</h2>
            </div>

            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="w-full">
                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="block mt-4">
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.remember" name="remember" />
                        <span class="ms-2 text-sm text-gray-600">Ingat Saya</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Lupa kata laluan?
                    </Link>

                    <div class="flex items-center space-x-4">
                        <Link :href="route('register')" class="text-sm text-gray-600 hover:text-gray-900 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Daftar Akaun
                        </Link>
                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Log Masuk
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Section: Welcome Message -->
        <div class="flex-1 flex items-center justify-center relative">
            <img src="/images/login-bg.jpg" alt="Welcome Background" class="absolute inset-0 w-full h-full object-cover z-0" />
        </div>
    </div>
</template>

<!-- <style scoped>
.login-title {
    @apply text-3xl font-extrabold text-indigo-700 text-center tracking-wide mb-4 drop-shadow-md;
    letter-spacing: 0.05em;
}
</style> -->
