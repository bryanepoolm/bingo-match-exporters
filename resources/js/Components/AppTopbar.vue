<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Menu from 'primevue/menu';

const menu = ref();
const items = [
    {
        label: 'Profile',
        icon: 'pi pi-user',
        command: () => {
            // router.visit(route('profile.edit'));
        }
    },
    {
        label: 'Settings',
        icon: 'pi pi-cog',
        command: () => {
            // router.visit(route('settings'));
        }
    },
    {
        separator: true
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out',
        command: () => {
            router.post(route('logout'));
        }
    }
];

const toggle = (event) => {
    menu.value.toggle(event);
};
</script>

<template>
    <div class="fixed top-0 right-0 z-10 flex items-center justify-between px-8 bg-surface-0/80 dark:bg-surface-900/80 backdrop-blur-md border-b border-surface-200 dark:border-surface-700 transition-all duration-300"
         style="height: var(--topbar-height); left: var(--sidebar-width)">
        
        <!-- Left Side (Search/Breadcrumbs placeholder) -->
        <div class="flex items-center gap-4">
            <span class="p-input-icon-left">
                <i class="pi pi-search text-surface-500" />
                <InputText placeholder="Search..." class="w-64" />
            </span>
        </div>

        <!-- Right Side (Actions) -->
        <div class="flex items-center gap-4">
            <Button icon="pi pi-bell" text rounded severity="secondary" v-badge.danger="2" />
            <Button icon="pi pi-cog" text rounded severity="secondary" />
            
            <div class="relative">
                <button @click="toggle" class="flex items-center gap-2 focus:outline-none">
                    <img :src="`https://ui-avatars.com/api/?name=${$page.props.auth.user.name}&background=random`" 
                         class="w-8 h-8 rounded-full border-2 border-primary-500" 
                         alt="User Avatar" />
                    <span class="hidden md:block font-medium text-surface-700 dark:text-surface-200">
                        {{ $page.props.auth.user.name }}
                    </span>
                    <i class="pi pi-chevron-down text-xs text-surface-500"></i>
                </button>
                <Menu ref="menu" :model="items" :popup="true" />
            </div>
        </div>
    </div>
</template>
