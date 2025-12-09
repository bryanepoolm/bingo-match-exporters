<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useLayout } from '@/Composables/layout';
import { Link, router } from '@inertiajs/vue3';

const { onMenuToggle, toggleDarkMode, isDarkTheme } = useLayout();

const outsideClickListener = ref(null);
const topbarMenuActive = ref(false);

const onTopBarMenuButton = () => {
    topbarMenuActive.value = !topbarMenuActive.value;
};

const onSettingsClick = () => {
    topbarMenuActive.value = false;
    router.visit(route('company.edit'));
};

const logout = () => {
    router.post(route('logout'));
};

const topbarMenuClasses = computed(() => {
    return {
        'layout-topbar-menu-mobile-active': topbarMenuActive.value
    };
});
</script>

<template>
    <div class="layout-topbar">
        <Link :href="route('dashboard')" class="layout-topbar-logo">
            <span class="text-xl font-bold text-primary-600 dark:text-primary-400">B2B Market</span>
        </Link>

        <button class="p-link layout-menu-button layout-topbar-button" @click="onMenuToggle()">
            <i class="pi pi-bars"></i>
        </button>

        <button class="p-link layout-topbar-menu-button layout-topbar-button" @click="onTopBarMenuButton()">
            <i class="pi pi-ellipsis-v"></i>
        </button>

        <div class="layout-topbar-menu" :class="topbarMenuClasses">
            <button @click="onTopBarMenuButton()" class="p-link layout-topbar-button">
                <i class="pi pi-calendar"></i>
                <span class="hidden">Calendar</span>
            </button>
            <button @click="onSettingsClick()" class="p-link layout-topbar-button">
                <i class="pi pi-user"></i>
                <span class="hidden">Profile</span>
            </button>
            <button @click="logout()" class="p-link layout-topbar-button">
                <i class="pi pi-sign-out"></i>
                <span class="hidden">Logout</span>
            </button>
             <button type="button" class="p-link layout-topbar-button" @click="toggleDarkMode">
                <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
                <span class="hidden">Theme</span>
            </button>
        </div>
    </div>
</template>

<style lang="scss" scoped></style>
