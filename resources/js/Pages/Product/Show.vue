<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';

const props = defineProps({
    product: Object,
});

const formatCurrency = (amount, currency) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency }).format(amount);
};
</script>

<template>
    <Head :title="product.name" />

    <TailAdminLayout>
        <div class="mx-auto max-w-270">
            <!-- Breadcrumb -->
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-title-md2 font-bold text-black dark:text-white">
                    Product Details
                </h2>
                <nav>
                    <ol class="flex items-center gap-2">
                        <li><Link class="font-medium" :href="route('dashboard')">Dashboard /</Link></li>
                         <!-- Back link logic could be improved to know if came from products or requests, for now default to generic or history back -->
                        <li class="font-medium text-primary">Product</li>
                    </ol>
                </nav>
            </div>

            <div class="grid grid-cols-1 gap-9 sm:grid-cols-2">
                <!-- Product Images -->
                <div class="flex flex-col gap-9">
                    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                         <div class="h-96 overflow-hidden rounded-t-sm bg-gray-100 flex items-center justify-center">
                            <img :src="product.primary_image ? `/storage/${product.primary_image}` : 'https://placehold.co/600x400'" 
                                 class="h-full w-full object-contain" />
                        </div>
                        <div class="p-6">
                            <!-- Helper images if any (future implementation) -->
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="flex flex-col gap-9">
                    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-6.5">
                        <div class="mb-6 border-b border-stroke dark:border-strokedark pb-4">
                            <h3 class="mb-2 text-2xl font-semibold text-black dark:text-white">
                                {{ product.name }}
                            </h3>
                            <p class="mb-4 text-sm text-primary font-medium">
                                {{ product.category?.name }}
                            </p>
                             <div class="flex flex-wrap gap-2">
                                <span v-if="product.brand" class="inline-flex rounded-full bg-primary bg-opacity-10 py-1 px-3 text-sm font-medium text-primary">
                                    Brand: {{ product.brand }}
                                </span>
                                <span v-if="product.status" :class="{
                                    'bg-success text-success': product.status === 'active',
                                    'bg-warning text-warning': product.status === 'draft',
                                    'bg-danger text-danger': product.status === 'inactive'
                                }" class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium capitalize">
                                    {{ product.status }}
                                </span>
                            </div>
                        </div>

                        <!-- Tabs or Sections -->
                        <div class="flex flex-col gap-6">
                            
                            <!-- Description -->
                            <div>
                                <h4 class="font-semibold text-black dark:text-white mb-2">Description</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                    {{ product.description || 'No description available.' }}
                                </p>
                            </div>

                            <!-- Classification & Origin -->
                            <div>
                                <h4 class="font-semibold text-black dark:text-white mb-3">Classification & Origin</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="block text-gray-500">HS Code</span>
                                        <span class="font-medium text-black dark:text-white">{{ product.hs_code }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-gray-500">Origin</span>
                                        <span class="font-medium text-black dark:text-white">
                                            {{ [product.origin_city, product.origin_state, product.origin_country].filter(Boolean).join(', ') }}
                                        </span>
                                    </div>
                                    <div class="col-span-2" v-if="product.target_markets && product.target_markets.length">
                                        <span class="block text-gray-500">Target Markets</span>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            <span v-for="market in product.target_markets" :key="market" class="inline-flex rounded bg-gray-100 dark:bg-gray-700 py-0.5 px-2 text-xs font-medium text-gray-600 dark:text-gray-300">
                                                {{ market }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Specifications & Price -->
                            <div>
                                <h4 class="font-semibold text-black dark:text-white mb-3">Specifications</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="block text-gray-500">Price</span>
                                        <span class="font-medium text-black dark:text-white text-lg">
                                            {{ formatCurrency(product.price_per_unit, product.currency) }} / {{ product.unit_of_measure }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="block text-gray-500">Incoterm</span>
                                        <span class="font-medium text-black dark:text-white uppercase">{{ product.price_term }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-gray-500">Available Qty</span>
                                        <span class="font-medium text-black dark:text-white">{{ product.available_quantity }} {{ product.unit_of_measure }}</span>
                                    </div>
                                     <div>
                                        <span class="block text-gray-500">Min. Order</span>
                                        <span class="font-medium text-black dark:text-white">{{ product.minimum_order_quantity }} {{ product.unit_of_measure }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Logistics -->
                            <div>
                                <h4 class="font-semibold text-black dark:text-white mb-3">Logistics</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="block text-gray-500">Packaging</span>
                                        <span class="font-medium text-black dark:text-white">{{ product.packaging_type || 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-gray-500">Lead Time</span>
                                        <span class="font-medium text-black dark:text-white">{{ product.lead_time_days }} Days</span>
                                    </div>
                                    <div v-if="product.weight_per_unit">
                                        <span class="block text-gray-500">Unit Weight</span>
                                        <span class="font-medium text-black dark:text-white">{{ product.weight_per_unit }} kg</span>
                                    </div>
                                    <div v-if="product.length || product.width || product.height">
                                        <span class="block text-gray-500">Dimensions (L x W x H)</span>
                                        <span class="font-medium text-black dark:text-white">
                                            {{ product.length || '-' }} x {{ product.width || '-' }} x {{ product.height || '-' }} cm
                                        </span>
                                    </div>
                                     <div class="col-span-2">
                                        <div class="flex flex-wrap gap-4 mt-2">
                                            <div v-if="product.requires_cold_chain" class="flex items-center gap-1 text-blue-600 text-xs font-medium bg-blue-50 px-2 py-1 rounded">
                                                <i class="pi pi-snowflake"></i> Cold Chain ({{ product.temperature_min }}°C to {{ product.temperature_max }}°C)
                                            </div>
                                            <div v-if="product.requires_hazmat" class="flex items-center gap-1 text-red-600 text-xs font-medium bg-red-50 px-2 py-1 rounded">
                                                <i class="pi pi-exclamation-triangle"></i> Hazmat ({{ product.hazmat_class }})
                                            </div>
                                            <div v-if="product.is_perishable" class="flex items-center gap-1 text-orange-600 text-xs font-medium bg-orange-50 px-2 py-1 rounded">
                                                <i class="pi pi-clock"></i> Perishable
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-2" v-if="product.special_handling_notes">
                                         <span class="block text-gray-500 mb-1">Handling Notes</span>
                                         <p class="text-xs bg-gray-50 p-2 rounded border border-gray-100 italic">{{ product.special_handling_notes }}</p>
                                    </div>
                                </div>
                            </div>

                             <!-- Quality & Certifications -->
                            <div>
                                <h4 class="font-semibold text-black dark:text-white mb-3">Quality & Compliance</h4>
                                <div class="flex flex-col gap-3 text-sm">
                                    <div class="flex gap-4">
                                        <div>
                                            <span class="block text-gray-500">Grade</span>
                                            <span class="font-medium text-black dark:text-white">{{ product.quality_grade || 'Standard' }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-gray-500">Shelf Life</span>
                                            <span class="font-medium text-black dark:text-white">{{ product.shelf_life_days }} Days</span>
                                        </div>
                                    </div>
                                    
                                    <div v-if="product.certifications && product.certifications.length">
                                        <span class="block text-gray-500 mb-1">Certifications</span>
                                        <div class="flex flex-wrap gap-2">
                                            <span v-for="(cert, index) in product.certifications" :key="index" class="inline-flex rounded bg-success bg-opacity-10 py-1 px-3 text-sm font-medium text-success">
                                                {{ typeof cert === 'object' ? cert.name : cert }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 mt-1">
                                        <span v-if="product.requires_phytosanitary" class="inline-flex items-center gap-1 rounded bg-gray-100 py-1 px-2 text-xs font-medium">
                                            <i class="pi pi-check text-green-500"></i> Phytosanitary
                                        </span>
                                        <span v-if="product.requires_health_certificate" class="inline-flex items-center gap-1 rounded bg-gray-100 py-1 px-2 text-xs font-medium">
                                            <i class="pi pi-check text-green-500"></i> Health Cert
                                        </span>
                                        <span v-if="product.requires_cites" class="inline-flex items-center gap-1 rounded bg-gray-100 py-1 px-2 text-xs font-medium">
                                            <i class="pi pi-check text-green-500"></i> CITES
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        
                        <div v-if="product.company" class="mt-8 pt-6 border-t border-stroke dark:border-strokedark">
                            <h4 class="font-semibold text-black dark:text-white mb-3">Producer</h4>
                            <div class="flex items-center gap-3">
                                 <div class="h-12 w-12 rounded-full overflow-hidden border border-stroke dark:border-strokedark">
                                     <img :src="product.company.logo_path ? `/storage/${product.company.logo_path}` : `https://ui-avatars.com/api/?name=${product.company.name}&background=random`" 
                                         class="h-full w-full object-cover" />
                                </div>
                                <div>
                                    <span class="font-medium block text-black dark:text-white">{{ product.company.name }}</span>
                                    <Link :href="route('explorer.show', product.company.id)" class="text-xs text-primary hover:underline">
                                        View Profile
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TailAdminLayout>
</template>
