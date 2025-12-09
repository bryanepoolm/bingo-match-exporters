<script setup>
import { Link } from '@inertiajs/vue3';

const menu = [
    { label: 'Dashboard', icon: 'pi pi-home', route: 'dashboard' },
    { label: 'Company Profile', icon: 'pi pi-building', route: 'company.edit' },
    { label: 'Messages', icon: 'pi pi-envelope', route: 'dashboard' }, // Placeholder
    { label: 'Marketplace', icon: 'pi pi-shopping-bag', route: 'dashboard' }, // Placeholder
];
</script>

<template>
    <div class="fixed left-0 top-0 h-full bg-surface-0 dark:bg-surface-900 border-r border-surface-200 dark:border-surface-700 flex flex-col transition-all duration-300 z-20"
         style="width: var(--sidebar-width)">
        
        <!-- Logo Area -->
        <div class="h-[var(--topbar-height)] flex items-center justify-center border-b border-surface-200 dark:border-surface-700">
            <Link :href="route('dashboard')" class="flex items-center gap-2 text-2xl font-bold text-primary-600">
                <i class="pi pi-diamond text-2xl"></i>
                <span>DIAMOND</span>
            </Link>
        </div>

        <!-- Menu -->
        <div class="flex-1 overflow-y-auto py-4 px-3">
            <ul class="space-y-1">
                <li v-for="item in menu" :key="item.label">
                    <Link :href="route(item.route)" 
                          class="flex items-center gap-3 px-4 py-3 rounded-lg text-surface-600 dark:text-surface-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 transition-colors"
                          :class="{ 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 font-medium': route().current(item.route) }">
                        <i :class="item.icon" class="text-lg"></i>
                        <span>{{ item.label }}</span>
                    </Link>
                </li>
            </ul>
        </div>

        <!-- User Profile Summary (Bottom) -->
        <div class="p-4 border-t border-surface-200 dark:border-surface-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">
                    {{ $page.props.auth.user.name.charAt(0) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-surface-900 dark:text-surface-0 truncate">
                        {{ $page.props.auth.user.name }}
                    </p>
                    <p class="text-xs text-surface-500 truncate">
                        {{ $page.props.auth.user.email }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
