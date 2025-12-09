<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

defineProps({
    companies: Object,
});

const getBadgeColor = (type) => {
    switch (type) {
        case 'producer':
            return 'bg-success/10 text-success';
        case 'exporter':
            return 'bg-primary/10 text-primary';
        case 'logistics':
            return 'bg-warning/10 text-warning';
        case 'both':
            return 'bg-secondary/10 text-secondary';
        default:
            return 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
    }
};
</script>

<template>
    <Head title="Explorer" />

    <TailAdminLayout>
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                Explorer
            </h2>
            <nav>
                <ol class="flex items-center gap-2">
                    <li><Link class="font-medium" :href="route('dashboard')">Dashboard /</Link></li>
                    <li class="font-medium text-primary">Explorer</li>
                </ol>
            </nav>
        </div>

        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="p-4 md:p-6 xl:p-9">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div>
                        <p class="text-sm font-medium">Discover partners for your business.</p>
                    </div>
                    <div class="w-full md:w-auto">
                        <div class="relative">
                            <input
                                type="text"
                                placeholder="Search companies..."
                                class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"
                            />
                            <span class="absolute right-4 top-4">
                                <i class="pi pi-search text-xl opacity-50"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div v-for="company in companies.data" :key="company.id" class="group relative rounded-lg border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-lg transition-all duration-300">
                        <div class="flex flex-col items-center text-center">
                            <div class="h-24 w-24 rounded-full border-4 border-white shadow-1 dark:border-boxdark overflow-hidden mb-4">
                                <img :src="company.logo_path ? `/storage/${company.logo_path}` : `https://ui-avatars.com/api/?name=${company.name}&background=random&size=128`" 
                                     :alt="company.name" 
                                     class="h-full w-full object-cover" />
                            </div>
                            
                            <h3 class="mb-1 text-xl font-bold text-black dark:text-white truncate w-full" :title="company.name">
                                {{ company.name }}
                            </h3>
                            
                            <div class="mb-4">
                                <span :class="['inline-flex rounded-full px-3 py-1 text-xs font-medium', getBadgeColor(company.type)]">
                                    {{ company.type.toUpperCase() }}
                                </span>
                            </div>

                            <p class="mb-6 text-sm line-clamp-3 h-16 w-full">
                                {{ company.description || 'No description available.' }}
                            </p>

                            <div class="mt-auto w-full">
                                <div class="flex flex-wrap gap-2 justify-center mb-4 h-16 overflow-hidden content-start" v-if="company.exporter && company.exporter.capabilities && company.exporter.capabilities.length > 0">
                                    <span v-for="cap in company.exporter.capabilities.slice(0, 3)" :key="cap.id" class="inline-flex rounded bg-gray-100 px-2 py-1 text-xs font-medium text-black dark:bg-gray-700 dark:text-white">
                                        {{ cap.name }}
                                    </span>
                                    <span v-if="company.exporter.capabilities.length > 3" class="text-xs text-gray-500 flex items-center">
                                        +{{ company.exporter.capabilities.length - 3 }} more
                                    </span>
                                </div>
                                <div class="h-16 flex items-center justify-center text-gray-500 text-sm italic" v-else>
                                    No specific capabilities listed.
                                </div>

                                <Link :href="route('explorer.show', company.id)" class="flex w-full justify-center rounded bg-primary p-3 font-medium text-gray hover:bg-opacity-90">
                                    Connect <i class="pi pi-arrow-right ml-2"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="companies.data.length === 0" class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 mb-6">
                        <i class="pi pi-search text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-black dark:text-white mb-2">No companies found</h3>
                    <p>Try adjusting your search or filters.</p>
                </div>
            </div>
        </div>
    </TailAdminLayout>
</template>

