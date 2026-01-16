<script setup>
import SidebarProvider from '@/Components/Layout/SidebarProvider.vue';
import LayoutStructure from '@/Layouts/LayoutStructure.vue';
import ConfirmDialog from 'primevue/confirmdialog';
</script>

<template>
    <SidebarProvider>
        <LayoutStructure>
            <slot></slot>
        </LayoutStructure>
        <ConfirmDialog />
    </SidebarProvider>
</template>
