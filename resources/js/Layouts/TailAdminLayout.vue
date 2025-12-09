<script setup>
import SidebarProvider from '@/Components/Layout/SidebarProvider.vue';
import LayoutStructure from '@/Layouts/LayoutStructure.vue';
</script>

<template>
    <SidebarProvider>
        <LayoutStructure>
            <slot></slot>
        </LayoutStructure>
    </SidebarProvider>
</template>
