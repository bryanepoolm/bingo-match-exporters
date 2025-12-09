<script setup>
import { useSidebar } from '@/Composables/useSidebar';

const { isMobileOpen, toggleMobileSidebar } = useSidebar();
</script>

<template>
    <div
        v-if="isMobileOpen"
        class="fixed inset-0 z-999 bg-gray-900/50 lg:hidden"
        @click="toggleMobileSidebar"
    ></div>
</template>
