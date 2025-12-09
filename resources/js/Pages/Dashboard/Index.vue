<script setup>
import { computed } from 'vue';
import { usePage, Head, Link } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const company = computed(() => user.value?.company);

const isProducer = computed(() => ['producer', 'both'].includes(company.value?.type));
const isExporter = computed(() => ['exporter', 'both'].includes(company.value?.type));
</script>

<template>
    <Head title="Dashboard" />

    <TailAdminLayout>
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                Dashboard
            </h2>
            <nav>
                <ol class="flex items-center gap-2">
                    <li><Link class="font-medium" :href="route('dashboard')">Dashboard /</Link></li>
                    <li class="font-medium text-primary">Overview</li>
                </ol>
            </nav>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5">
            <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
                    <i class="pi pi-box text-primary text-xl"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-black dark:text-white">0</h4>
                        <span class="text-sm font-medium">Active Products</span>
                    </div>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
                    <i class="pi pi-globe text-primary text-xl"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-black dark:text-white">0</h4>
                        <span class="text-sm font-medium">Matches Found</span>
                    </div>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
                    <i class="pi pi-envelope text-primary text-xl"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-black dark:text-white">0</h4>
                        <span class="text-sm font-medium">New Messages</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="!company" class="mt-6 rounded-sm border border-warning bg-warning/10 p-4 text-warning-dark">
            <div class="flex items-center gap-3">
                <i class="pi pi-exclamation-triangle text-xl"></i>
                <div>
                    <p class="font-bold">Profile Incomplete</p>
                    <p>Please <Link :href="route('company.create')" class="underline font-medium hover:text-opacity-80">complete your company profile</Link> to access all features.</p>
                </div>
            </div>
        </div>
    </TailAdminLayout>
</template>

