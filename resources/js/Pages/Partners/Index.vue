<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import Card from 'primevue/card';
import Button from 'primevue/button';
import Tag from 'primevue/tag';

const props = defineProps({
    partners: Array,
});

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString();
};
</script>

<template>
    <Head title="My Partners" />

    <TailAdminLayout>
        <div class="mx-auto max-w-270">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-title-md2 font-bold text-black dark:text-white">
                    My Partners
                </h2>
                <nav>
                    <ol class="flex items-center gap-2">
                        <li><Link class="font-medium" :href="route('dashboard')">Dashboard /</Link></li>
                        <li class="font-medium text-primary">Partners</li>
                    </ol>
                </nav>
            </div>

            <div v-if="partners.length === 0" class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1 text-center min-h-[200px] flex items-center justify-center text-gray-500">
                You don't have any partners yet. Accept connection requests to build partnerships!
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="partner in partners" :key="partner.id" class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-16 w-16 rounded-full overflow-hidden border border-stroke dark:border-strokedark flex-shrink-0">
                             <img :src="partner.logo_path ? `/storage/${partner.logo_path}` : `https://ui-avatars.com/api/?name=${partner.company_name}&background=random`" 
                                  class="h-full w-full object-cover" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-black dark:text-white">{{ partner.company_name }}</h3>
                            <p class="text-sm text-gray-500">{{ partner.type }}</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-stroke dark:border-strokedark pt-4 mt-4 grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <span class="block text-xl font-bold text-primary">{{ partner.match_count }}</span>
                            <span class="text-xs text-gray-500">Total Requests</span>
                        </div>
                         <div class="text-center">
                            <span class="block text-xl font-bold text-success">{{ partner.accepted_count }}</span>
                            <span class="text-xs text-gray-500">Active Deals</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-center">
                         <Link :href="route('explorer.show', { company: partner.id }) + '#'" class="w-full">
                            <Button label="View Profile" icon="pi pi-user" outlined class="w-full" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </TailAdminLayout>
</template>
