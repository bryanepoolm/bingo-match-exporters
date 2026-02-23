<script setup>
import { ref, onMounted } from 'vue';
import { useSidebar } from '@/Composables/useSidebar';
import { Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';

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

const notificationsOpen = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);

const toggleNotifications = () => {
    notificationsOpen.value = !notificationsOpen.value;
    if (notificationsOpen.value) {
        dropdownOpen.value = false;
    }
};

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
    if (dropdownOpen.value) {
        notificationsOpen.value = false;
    }
};

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('notifications.index'));
        notifications.value = response.data.notifications;
        unreadCount.value = response.data.unreadCount;
    } catch (e) {
        console.error('Failed to fetch notifications', e);
    }
};

const markAsRead = async (notification) => {
    if (!notification.read_at) {
        try {
            await axios.patch(route('notifications.read', notification.id));
            notification.read_at = new Date();
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        } catch (e) {
            console.error('Failed to mark as read', e);
        }
    }
    
    if (notification.data && notification.data.url) {
        router.visit(notification.data.url);
        notificationsOpen.value = false;
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post(route('notifications.read-all'));
        notifications.value.forEach(n => n.read_at = new Date());
        unreadCount.value = 0;
    } catch (e) {
        console.error('Failed to mark all as read', e);
    }
};

onMounted(() => {
    if (user) {
        fetchNotifications();
        setInterval(fetchNotifications, 60000);
    }
});

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

                <div class="relative mr-2">
                    <button @click="toggleNotifications" class="relative flex items-center justify-center w-10 h-10 text-gray-500 rounded-full hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                        <i class="pi pi-bell text-xl"></i>
                        <span v-if="unreadCount > 0" class="absolute top-1 right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full">
                            {{ unreadCount > 99 ? '99+' : unreadCount }}
                        </span>
                    </button>

                    <div v-show="notificationsOpen" class="absolute right-0 mt-4 flex w-80 flex-col rounded-lg border border-gray-200 bg-white shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 z-50">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Notifications</h3>
                            <button v-if="unreadCount > 0" @click="markAllAsRead" class="text-xs text-brand-500 hover:underline">Mark all read</button>
                        </div>
                        <ul class="flex flex-col max-h-96 overflow-y-auto w-full">
                            <li v-if="notifications.length === 0" class="px-4 py-6 text-center text-gray-500 text-sm">
                                No notifications to show.
                            </li>
                            <li v-for="notification in notifications" :key="notification.id" 
                                class="border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-200"
                                :class="{ 'bg-blue-50/50 dark:bg-blue-900/10': !notification.read_at }"
                                @click="markAsRead(notification)">
                                <div class="px-4 py-3 flex gap-3">
                                    <div class="mt-1 flex-shrink-0">
                                        <i v-if="notification.data.type === 'connection_request_received'" class="pi pi-user-plus text-blue-500"></i>
                                        <i v-else-if="notification.data.type === 'connection_request_accepted'" class="pi pi-check-circle text-green-500"></i>
                                        <i v-else-if="notification.data.type === 'connection_request_rejected'" class="pi pi-times-circle text-red-500"></i>
                                        <i v-else-if="notification.data.type === 'new_chat_message'" class="pi pi-comments text-brand-500"></i>
                                        <i v-else-if="notification.data.type === 'new_partner_post'" class="pi pi-file text-purple-500"></i>
                                        <i v-else-if="notification.data.type?.includes('liked')" class="pi pi-heart-fill text-pink-500"></i>
                                        <i v-else class="pi pi-bell text-gray-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-800 dark:text-gray-200 line-clamp-2" :class="{ 'font-semibold': !notification.read_at }">
                                            {{ notification.data.message }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">{{ new Date(notification.created_at).toLocaleDateString() }}</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

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
