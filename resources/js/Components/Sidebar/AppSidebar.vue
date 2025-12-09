<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { useSidebar } from '@/Composables/useSidebar';

const { isExpanded, isMobileOpen, isHovered, openSubmenu, toggleSubmenu } = useSidebar();
const page = usePage();

const user = computed(() => page.props.auth.user);
const company = computed(() => user.value?.company);
const isProducer = computed(() => ['producer', 'both'].includes(company.value?.type));

const isActive = (path) => {
    return route().current(path);
};

const isSubmenuOpen = (groupIndex, itemIndex) => {
    const key = `${groupIndex}-${itemIndex}`;
    return openSubmenu.value === key;
};

const menuGroups = computed(() => [
    {
        title: "MENU",
        items: [
            {
                icon: "pi pi-th-large",
                name: "Dashboard",
                path: "dashboard",
            },
            ...(isProducer.value ? [{
                icon: "pi pi-box",
                name: "My Products",
                path: "products.index",
            },
            {
                icon: "pi pi-send",
                name: "My Requests",
                path: "matches.index",
                params: { sent: true },
            }] : []),
            ...(company.value?.type === 'exporter' || company.value?.type === 'both' ? [{
                icon: "pi pi-inbox",
                name: "Requests",
                path: "matches.index",
                badge: computed(() => page.props.auth.unread_matches_count || 0),
            }] : []),
            {
                icon: "pi pi-users",
                name: "Partners",
                path: "partners.index",
            },
            {
                icon: "pi pi-compass",
                name: "Explorer",
                path: "explorer.index",
            },
            {
                icon: "pi pi-building",
                name: "My Company",
                path: "company.edit",
            },
        ],
    },
]);
</script>

<template>
    <aside
        :class="[
            'fixed mt-16 flex flex-col lg:mt-0 top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-50 border-r border-gray-200',
            {
                'lg:w-[290px]': isExpanded || isMobileOpen || isHovered,
                'lg:w-[90px]': !isExpanded && !isHovered,
                'translate-x-0 w-[290px]': isMobileOpen,
                '-translate-x-full': !isMobileOpen,
                'lg:translate-x-0': true,
            },
        ]"
        @mouseenter="!isExpanded && (isHovered = true)"
        @mouseleave="isHovered = false"
    >
        <div
            :class="[
                'py-8 flex',
                !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
            ]"
        >
            <Link :href="route('dashboard')">
                <span v-if="isExpanded || isHovered || isMobileOpen" class="text-2xl font-bold text-brand-500 dark:text-white">
                    Bingo
                </span>
                <span v-else class="text-2xl font-bold text-brand-500 dark:text-white">
                    B
                </span>
            </Link>
        </div>
        <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
            <nav class="mb-6">
                <div class="flex flex-col gap-4">
                    <div v-for="(menuGroup, groupIndex) in menuGroups" :key="groupIndex">
                        <h2
                            :class="[
                                'mb-4 text-xs uppercase flex leading-[20px] text-gray-400',
                                !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
                            ]"
                        >
                            <template v-if="isExpanded || isHovered || isMobileOpen">
                                {{ menuGroup.title }}
                            </template>
                            <span v-else>...</span>
                        </h2>
                        <ul class="flex flex-col gap-4">
                            <li v-for="(item, index) in menuGroup.items" :key="item.name">
                                <Link
                                    v-if="item.path"
                                    :href="route(item.path, item.params || {})"
                                    :class="[
                                        'menu-item group',
                                        {
                                            'menu-item-active': isActive(item.path),
                                            'menu-item-inactive': !isActive(item.path),
                                        },
                                    ]"
                                >
                                    <span
                                        :class="[
                                            isActive(item.path) ? 'menu-item-icon-active' : 'menu-item-icon-inactive',
                                        ]"
                                    >
                                        <i :class="item.icon" class="text-xl"></i>
                                    </span>
                                    <span
                                        v-if="isExpanded || isHovered || isMobileOpen"
                                        class="menu-item-text"
                                    >
                                        {{ item.name }}
                                        <span v-if="item.badge && item.badge > 0" class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-medium text-white">
                                            {{ item.badge }}
                                        </span>
                                    </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </aside>
</template>
