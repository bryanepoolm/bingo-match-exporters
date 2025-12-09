<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    company: Object,
    products: Array,
    existingMatch: Object,
});

const companyLogo = computed(() => {
    return props.company.logo_path 
        ? `/storage/${props.company.logo_path}` 
        : `https://ui-avatars.com/api/?name=${props.company.name}&background=random&size=128`;
});

const formatCurrency = (amount, currency) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency }).format(amount);
};
</script>

<template>
    <Head :title="company.name" />

    <TailAdminLayout>
        <div class="mx-auto max-w-242.5">
            <!-- Breadcrumb -->
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-title-md2 font-bold text-black dark:text-white">
                    Profile
                </h2>
                <nav>
                    <ol class="flex items-center gap-2">
                        <li><Link class="font-medium" :href="route('dashboard')">Dashboard /</Link></li>
                        <li><Link class="font-medium" :href="route('explorer.index')">Explorer /</Link></li>
                        <li class="font-medium text-primary">Profile</li>
                    </ol>
                </nav>
            </div>

            <!-- Profile Header -->
            <div class="overflow-hidden rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="px-4 pb-6 text-center lg:pb-8 xl:pb-11.5 mt-10">
                    <div class="relative mx-auto h-30 w-30 rounded-full bg-white/20 p-1 backdrop-blur sm:h-44 sm:w-44 sm:p-3 border border-stroke dark:border-strokedark shadow-1">
                        <div class="relative drop-shadow-2">
                            <img :src="companyLogo" alt="profile" class="h-full w-full rounded-full object-cover" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="mb-1.5 text-2xl font-semibold text-black dark:text-white">
                            {{ company.name }}
                        </h3>
                        <p class="font-medium">{{ company.type }}</p>
                        
                        <!-- Connect Button -->
                         <div class="mx-auto mt-4.5 mb-5.5 grid max-w-94 grid-cols-1 rounded-md border border-stroke py-2.5 shadow-1 dark:border-strokedark dark:bg-[#37404F]">
                            <div class="flex flex-col items-center justify-center gap-1 border-r border-stroke px-4 dark:border-strokedark xsm:flex-row">
                                <span class="font-semibold text-black dark:text-white">
                                    <button 
                                        v-if="existingMatch && existingMatch.status === 'pending'"
                                        disabled
                                        class="flex justify-center rounded bg-gray-500 py-2 px-6 font-medium text-white cursor-not-allowed opacity-70"
                                    >
                                        Request Pending
                                    </button>
                                    <Link 
                                        v-else
                                        :href="route('matches.create', company.id)" 
                                        class="flex justify-center rounded bg-primary py-2 px-6 font-medium text-gray hover:bg-opacity-90"
                                    >
                                        Connect
                                    </Link>
                                </span>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="mx-auto mt-4.5 mb-5.5 grid max-w-94 grid-cols-3 rounded-md border border-stroke py-2.5 shadow-1 dark:border-strokedark dark:bg-[#37404F]">
                            <div class="flex flex-col items-center justify-center gap-1 border-r border-stroke px-4 dark:border-strokedark">
                                <span class="font-semibold text-black dark:text-white">{{ products.length }}</span>
                                <span class="text-sm">Products</span>
                            </div>
                            <div class="flex flex-col items-center justify-center gap-1 border-r border-stroke px-4 dark:border-strokedark">
                                <span class="font-semibold text-black dark:text-white">{{ company.verification_documents?.length || 0 }}</span>
                                <span class="text-sm">Documents</span>
                            </div>
                            <div class="flex flex-col items-center justify-center gap-1 px-4">
                                <span class="font-semibold text-black dark:text-white">{{ company.is_verified ? 'Yes' : 'No' }}</span>
                                <span class="text-sm">Verified</span>
                            </div>
                        </div>

                         <div class="mx-auto max-w-180">
                            <h4 class="font-semibold text-black dark:text-white mb-2">About</h4>
                            <p class="mt-4.5 text-sm font-medium">
                                {{ company.description || 'No description available.' }}
                            </p>
                            <div v-if="company.website" class="mt-4">
                                <a :href="company.website" target="_blank" class="text-primary hover:underline">
                                    <i class="pi pi-globe mr-1"></i> {{ company.website }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs / Sections -->
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                
                <!-- Documents Section -->
                <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h4 class="mb-6 text-xl font-semibold text-black dark:text-white">
                        Documents & Certifications
                    </h4>
                    
                    <div v-if="company.verification_documents && company.verification_documents.length > 0" class="flex flex-col gap-4">
                        <div v-for="(doc, index) in company.verification_documents" :key="index" class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                             <div class="flex items-center gap-3">
                                <i class="pi pi-file-pdf text-red-500 text-xl"></i>
                                <span class="font-medium text-black dark:text-white">{{ doc.original_name || `Document ${index + 1}` }}</span>
                            </div>
                            <a :href="`/storage/${doc.path}`" target="_blank" class="text-primary hover:text-primary/80">
                                <i class="pi pi-download"></i>
                            </a>
                        </div>
                    </div>
                    <div v-else class="text-gray-500 italic text-center py-4">
                        No public documents available.
                    </div>
                </div>

                <!-- Products Section -->
                 <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h4 class="mb-6 text-xl font-semibold text-black dark:text-white">
                        Products
                    </h4>

                     <div v-if="products.length > 0" class="flex flex-col gap-4">
                        <div v-for="product in products" :key="product.id" class="flex items-start gap-4 p-3 border rounded-lg hover:shadow-md transition-shadow">
                            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-stroke dark:border-strokedark">
                                <img :src="product.primary_image ? `/storage/${product.primary_image}` : 'https://placehold.co/100'" 
                                     :alt="product.name" 
                                     class="h-full w-full object-cover" />
                            </div>
                            <div class="flex-grow">
                                <h5 class="font-medium text-black dark:text-white">{{ product.name }}</h5>
                                <p class="text-sm text-gray-500 line-clamp-1">{{ product.description }}</p>
                                <div class="mt-1 font-semibold text-primary">
                                    {{ formatCurrency(product.price_per_unit, product.currency) }} / {{ product.unit_of_measure }}
                                </div>
                            </div>
                        </div>
                     </div>
                     <div v-else class="text-gray-500 italic text-center py-4">
                        No products listed.
                    </div>
                 </div>
            </div>
        </div>
    </TailAdminLayout>
</template>
