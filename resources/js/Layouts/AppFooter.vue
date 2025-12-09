<script setup>
import { useLayout } from '@/Composables/layout';
import { computed } from 'vue';

const { layoutConfig } = useLayout();

const logoUrl = computed(() => {
    return `layout/images/${layoutConfig.darkTheme ? 'logo-white' : 'logo-dark'}.svg`;
});
</script>

<template>
    <div class="layout-footer">
        <span class="font-medium ml-2">Bingoplace &copy; {{ new Date().getFullYear() }}</span>
    </div>
</template>
<style lang="scss" scoped></style>
