<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppMenuItem from './AppMenuItem.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const company = computed(() => user.value?.company);
const isProducer = computed(() => {
    const type = company.value?.type;
    return ['producer', 'both'].includes(type);
});

const model = computed(() => [
    {
        label: 'Home',
        items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: 'dashboard' }]
    },
    {
        label: 'Management',
        items: [
             ...(isProducer.value ? [{ label: 'My Products', icon: 'pi pi-fw pi-box', to: 'products.index' }] : []),
        ]
    },
    {
        label: 'Business',
        items: [
            { label: 'Company Profile', icon: 'pi pi-fw pi-building', to: 'company.edit' },
            { label: 'Messages', icon: 'pi pi-fw pi-envelope', to: 'dashboard' }, // Placeholder
            { label: 'Explorer', icon: 'pi pi-fw pi-compass', to: 'explorer.index' }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
