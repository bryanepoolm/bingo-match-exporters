<script setup>
import AppSidebar from '@/Components/AppSidebar.vue';
import AppTopbar from '@/Components/AppTopbar.vue';
import AppFooter from '@/Components/AppFooter.vue';
</script>

<template>
    <div class="min-h-screen bg-surface-50 dark:bg-surface-900 transition-colors duration-300">
        <AppSidebar />
        <AppTopbar />
        
        <div class="flex flex-col min-h-screen transition-all duration-300" 
             style="margin-left: var(--sidebar-width); padding-top: var(--topbar-height)">
            <div class="flex-1 p-[var(--content-padding)]">
                <div class="p-6 bg-surface-0 dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 min-h-[calc(100vh-var(--topbar-height)-4rem)]">
                    <slot />
                </div>
                <AppFooter />
            </div>
        </div>
    </div>
</template>
