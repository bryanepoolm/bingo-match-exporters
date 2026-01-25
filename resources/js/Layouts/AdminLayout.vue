<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const navigation = [
    { name: 'Companies', href: route('admin.dashboard'), active: route().current('admin.dashboard') },
    { name: 'Products', href: route('admin.products.index'), active: route().current('admin.products.index') },
    { name: 'Events', href: route('admin.events.index'), active: route().current('admin.events.index') },
];
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('admin.dashboard')" class="text-xl font-bold text-gray-800 dark:text-gray-200">
                                Admin<span class="text-primary-600">Panel</span>
                            </Link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <Link v-for="item in navigation" :key="item.name" :href="item.href"
                                :class="[
                                    item.active
                                        ? 'border-primary-500 text-gray-900 dark:text-gray-100'
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300',
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                                ]"
                            >
                                {{ item.name }}
                            </Link>
                        </div>
                    </div>
                    <div class="flex items-center">
                         <div class="ml-3 relative">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ user.name }} (Admin)
                            </div>
                        </div>
                        <Link :href="route('logout')" method="post" as="button" class="ml-4 text-sm text-red-600 hover:text-red-800">
                            Log Out
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                   <slot />
                </div>
            </div>
        </main>
    </div>
</template>
