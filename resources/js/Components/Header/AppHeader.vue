<script setup>
import { ref } from 'vue';
import { useSidebar } from '@/Composables/useSidebar';
import { Link, router, usePage } from '@inertiajs/vue3';

const { toggleSidebar, toggleMobileSidebar, isMobileOpen } = useSidebar();
const page = usePage();
const user = page.props.auth.user;

const handleToggle = () => {
    if (window.innerWidth >= 1024) {
        toggleSidebar();
    } else {
        toggleMobileSidebar();
    }
};

const isDarkMode = ref(document.documentElement.classList.contains('dark'));

const toggleDarkMode = () => {
    const html = document.documentElement;
    if (html.classList.contains('dark')) {
        html.classList.remove('dark');
        isDarkMode.value = false;
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        isDarkMode.value = true;
        localStorage.setItem('theme', 'dark');
    }
};

const dropdownOpen = ref(false);

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <header class="sticky top-0 flex w-full bg-white border-gray-200 z-40 dark:border-gray-800 dark:bg-gray-900 lg:border-b">
        <div class="flex flex-col items-center justify-between grow lg:flex-row lg:px-6">
            <div class="flex items-center justify-between w-full gap-2 px-3 py-3 border-b border-gray-200 dark:border-gray-800 sm:gap-4 lg:justify-normal lg:border-b-0 lg:px-0 lg:py-4">
                <button
                    @click="handleToggle"
                    class="flex items-center justify-center w-10 h-10 text-gray-500 border-gray-200 rounded-lg z-50 dark:border-gray-800 dark:text-gray-400 lg:h-11 lg:w-11 lg:border"
                    :class="[isMobileOpen ? 'lg:bg-transparent dark:lg:bg-transparent bg-gray-100 dark:bg-gray-800' : '']"
                >
                    <i class="pi pi-bars text-xl"></i>
                </button>
                
                <div class="lg:hidden">
                    <span class="text-xl font-bold text-brand-500 dark:text-white">Bingo</span>
                </div>
            </div>

            <div class="flex items-center justify-end w-full gap-4 px-5 py-4 lg:flex lg:px-0">
                <button v-if="false" @click="toggleDarkMode" class="flex items-center justify-center w-10 h-10 text-gray-500 rounded-full hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                    <i :class="['pi', isDarkMode ? 'pi-moon' : 'pi-sun', 'text-xl']"></i>
                </button>

                <div class="relative">
                    <button @click="toggleDropdown" class="flex items-center gap-3">
                        <span class="hidden text-right lg:block">
                            <span class="block text-sm font-medium text-black dark:text-white">{{ user.name }}</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</span>
                        </span>
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                             <img :src="'https://ui-avatars.com/api/?name=' + user.name + '&background=random'" alt="User" class="w-full h-full object-cover" />
                        </div>
                        <i class="pi pi-chevron-down text-gray-500 dark:text-gray-400 text-xs"></i>
                    </button>

                    <div v-show="dropdownOpen" class="absolute right-0 mt-4 flex w-62.5 flex-col rounded-lg border border-gray-200 bg-white shadow-theme-lg dark:border-gray-800 dark:bg-gray-900">
                        <ul class="flex flex-col gap-5 border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                            <li>
                                <Link :href="route('company.edit')" class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-brand-500 lg:text-base">
                                    <i class="pi pi-user text-lg"></i>
                                    My Profile
                                </Link>
                            </li>
                        </ul>
                        <button @click="logout" class="flex items-center gap-3.5 px-6 py-4 text-sm font-medium duration-300 ease-in-out hover:text-brand-500 lg:text-base">
                            <i class="pi pi-sign-out text-lg"></i>
                            Log Out
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
