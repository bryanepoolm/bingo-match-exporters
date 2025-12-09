<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Button from 'primevue/button';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Welcome" />
    <div class="min-h-screen bg-gradient-to-br from-surface-50 to-primary-50 dark:from-surface-900 dark:to-surface-800 flex flex-col items-center justify-center p-6">
        
        <!-- Hero Section -->
        <div class="text-center mb-16 max-w-3xl">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white dark:bg-surface-800 shadow-lg text-primary-600 mb-8 animate-fade-in-down">
                <i class="pi pi-diamond text-4xl"></i>
            </div>
            <h1 class="text-5xl md:text-6xl font-bold text-surface-900 dark:text-surface-0 mb-6 tracking-tight">
                B2B <span class="text-primary-600">Marketplace</span>
            </h1>
            <p class="text-xl md:text-2xl text-surface-600 dark:text-surface-300 mb-10 leading-relaxed">
                Connect with trusted Producers and Exporters worldwide. <br class="hidden md:block" />
                Streamline your business matching process.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <Link :href="route('login')">
                    <Button label="Sign In" icon="pi pi-sign-in" size="large" class="w-full sm:w-auto px-8" />
                </Link>
                <Link :href="route('register')">
                    <Button label="Get Started" severity="secondary" icon="pi pi-arrow-right" iconPos="right" size="large" class="w-full sm:w-auto px-8" />
                </Link>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl w-full px-4">
            <div class="p-8 bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 mb-6">
                    <i class="pi pi-globe text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-surface-900 dark:text-surface-0">Global Reach</h3>
                <p class="text-surface-600 dark:text-surface-400 leading-relaxed">
                    Expand your business network internationally with verified partners from over 50 countries.
                </p>
            </div>
            
            <div class="p-8 bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 mb-6">
                    <i class="pi pi-verified text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-surface-900 dark:text-surface-0">Verified Companies</h3>
                <p class="text-surface-600 dark:text-surface-400 leading-relaxed">
                    Connect with legitimate producers and exporters. Every profile is manually verified for authenticity.
                </p>
            </div>
            
            <div class="p-8 bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 mb-6">
                    <i class="pi pi-chart-line text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-surface-900 dark:text-surface-0">Smart Matching</h3>
                <p class="text-surface-600 dark:text-surface-400 leading-relaxed">
                    Our advanced algorithm finds the best business partners based on your specific capabilities and needs.
                </p>
            </div>
        </div>
        
        <div class="mt-16 text-surface-500 dark:text-surface-500 text-sm">
            © 2025 Bingoplace. All rights reserved.
        </div>
    </div>
</template>
