<script setup>
import { usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const { props } = usePage();

onMounted(() => {
    if (!props.auth.user) {
        window.location.href = route('register');
    } else {
        window.location.href = route('dashboard');
    }
});
</script>

<template>
    <!-- Empty template as the page will redirect immediately -->
</template>