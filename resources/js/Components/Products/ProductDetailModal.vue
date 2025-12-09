<script setup>
import { ref, computed } from 'vue';
import Dialog from 'primevue/dialog';
import Steps from 'primevue/steps';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Image from 'primevue/image';

const props = defineProps({
    visible: Boolean,
    product: Object,
});

const emit = defineEmits(['update:visible']);

const isVisible = computed({
    get: () => props.visible,
    set: (value) => emit('update:visible', value),
});

const items = ref([
    { label: 'Basic Info' },
    { label: 'Classification' },
    { label: 'Specs' },
    { label: 'Logistics' },
    { label: 'Quality' },
    { label: 'Documentation' },
    { label: 'Media' },
]);

const activeStep = ref(0);

const nextStep = () => {
    if (activeStep.value < items.value.length - 1) {
        activeStep.value++;
    }
};

const prevStep = () => {
    if (activeStep.value > 0) {
        activeStep.value--;
    }
};

const formatCurrency = (amount, currency) => {
    if (!amount) return '-';
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency || 'USD' }).format(amount);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};
</script>

<template>
    <Dialog v-model:visible="isVisible" modal header="Product Details" :style="{ width: '800px', maxWidth: '95vw' }" :maximizable="true">
        <template v-if="product">
            <Steps :model="items" :readonly="false" :activeStep="activeStep" class="mb-6 product-steps" />
            
            <div class="p-4 border rounded-lg bg-gray-50 dark:bg-boxdark mb-4 min-h-[300px]">
                
                <!-- Step 1: Basic Info -->
                <div v-if="activeStep === 0" class="flex flex-col gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Product Name</label>
                            <div class="font-bold text-lg text-black dark:text-white">{{ product.name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Category</label>
                            <div class="text-black dark:text-white">{{ product.category?.name || 'N/A' }}</div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Description</label>
                        <div class="text-black dark:text-white bg-white dark:bg-meta-4 p-3 rounded border border-gray-200 dark:border-strokedark mt-1">
                            {{ product.description }}
                        </div>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-500">Brand</label>
                        <div class="text-black dark:text-white">{{ product.brand || 'N/A' }}</div>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-500">Tags / Keywords</label>
                        <div class="flex gap-2 flex-wrap mt-1">
                             <Tag v-for="tag in product.tags" :key="tag" :value="tag" severity="info" class="mr-1"/>
                             <Tag v-for="kw in product.keywords" :key="kw" :value="kw" severity="secondary" rounded class="mr-1"/>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2: Classification -->
                <div v-if="activeStep === 1" class="flex flex-col gap-4">
                     <div>
                        <label class="block text-sm font-medium text-gray-500">HS Code</label>
                        <div class="font-mono bg-gray-200 dark:bg-meta-4 px-2 py-1 rounded inline-block text-black dark:text-white mt-1">{{ product.hs_code }}</div>
                        <div class="text-sm text-gray-500">{{ product.hs_description }}</div>
                    </div>
                     <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Origin Country</label>
                            <div class="text-black dark:text-white font-bold">{{ product.origin_country }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">State</label>
                            <div class="text-black dark:text-white">{{ product.origin_state }}</div>
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-500">City</label>
                            <div class="text-black dark:text-white">{{ product.origin_city }}</div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Target Markets</label>
                         <div class="flex gap-2 flex-wrap mt-1">
                             <Tag v-for="market in product.target_markets" :key="market" :value="market" severity="success" />
                        </div>
                    </div>
                </div>

                <!-- Step 3: Specs -->
                <div v-if="activeStep === 2" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                         <label class="block text-sm font-medium text-gray-500">Price Configuration</label>
                         <div class="mt-2 space-y-2">
                             <div class="flex justify-between border-b pb-1">
                                 <span>Price per Unit</span>
                                 <span class="font-bold">{{ formatCurrency(product.price_per_unit, product.currency) }} / {{ product.unit_of_measure }}</span>
                             </div>
                              <div class="flex justify-between border-b pb-1">
                                 <span>Incoterm</span>
                                 <span class="font-bold">{{ product.price_term }}</span>
                             </div>
                         </div>
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-500">Availability</label>
                         <div class="mt-2 space-y-2">
                             <div class="flex justify-between border-b pb-1">
                                 <span>Available Qty</span>
                                 <span class="font-bold">{{ product.available_quantity }}</span>
                             </div>
                              <div class="flex justify-between border-b pb-1">
                                 <span>Min. Order Qty</span>
                                 <span class="font-bold">{{ product.minimum_order_quantity }}</span>
                             </div>
                         </div>
                    </div>
                </div>
                
                 <!-- Step 4: Logistics -->
                <div v-if="activeStep === 3" class="flex flex-col gap-4">
                     <div>
                        <label class="block text-sm font-medium text-gray-500">Packaging</label>
                        <div class="text-black dark:text-white font-medium">{{ product.packaging_type }}</div>
                        <div class="text-sm text-gray-500 mt-1">Lead Time: {{ product.lead_time_days }} days</div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-3 bg-white dark:bg-meta-4 rounded border">
                         <div class="text-center">
                             <div class="text-xs text-gray-500">Weight</div>
                             <div class="font-bold mt-1">{{ product.weight_per_unit }} kg</div>
                         </div>
                         <div class="text-center">
                             <div class="text-xs text-gray-500">Length</div>
                             <div class="font-bold mt-1">{{ product.length }} cm</div>
                         </div>
                          <div class="text-center">
                             <div class="text-xs text-gray-500">Width</div>
                             <div class="font-bold mt-1">{{ product.width }} cm</div>
                         </div>
                          <div class="text-center">
                             <div class="text-xs text-gray-500">Height</div>
                             <div class="font-bold mt-1">{{ product.height }} cm</div>
                         </div>
                    </div>
                    
                    <div v-if="product.requires_cold_chain" class="p-3 bg-blue-50 border border-blue-100 rounded">
                        <div class="flex items-center gap-2 text-blue-800 font-bold mb-2">
                            <i class="pi pi-snowflake"></i> Requires Cold Chain
                        </div>
                        <div class="text-sm">Range: {{ product.temperature_min }}°C to {{ product.temperature_max }}°C</div>
                    </div>
                     
                    <div v-if="product.is_perishable" class="p-3 bg-orange-50 border border-orange-100 rounded">
                        <div class="flex items-center gap-2 text-orange-800 font-bold">
                            <i class="pi pi-clock"></i> Perishable Product
                        </div>
                    </div>
                    
                    <div v-if="product.special_handling_notes" class="mt-2">
                        <label class="block text-sm font-medium text-gray-500">Special Handling Notes</label>
                        <p class="text-sm italic text-gray-600">{{ product.special_handling_notes }}</p>
                    </div>
                </div>
                
                 <!-- Step 5: Quality -->
                <div v-if="activeStep === 4" class="flex flex-col gap-4">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Quality Grade</label>
                            <div class="text-black dark:text-white font-bold text-lg">{{ product.quality_grade || 'N/A' }}</div>
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-500">Shelf Life</label>
                            <div class="text-black dark:text-white font-bold text-lg">{{ product.shelf_life_days }} days</div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Certifications</label>
                        <div v-if="product.certifications?.length" class="flex gap-2 flex-wrap">
                            <Tag v-for="cert in product.certifications" :key="cert.id || cert" :value="cert.name || cert" icon="pi pi-verified" severity="success" />
                        </div>
                        <div v-else class="text-gray-400 italic">No specific certifications listed.</div>
                    </div>
                </div>
                
                <!-- Step 6: Documentation Request -->
                 <div v-if="activeStep === 5" class="flex flex-col gap-4">
                     <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                         <div class="p-3 border rounded text-center" :class="{ 'bg-green-50 border-green-200': product.requires_phytosanitary }">
                             <div class="font-bold" :class="{ 'text-green-700': product.requires_phytosanitary, 'text-gray-400': !product.requires_phytosanitary }">Phytosanitary</div>
                             <div class="text-xs mt-1">{{ product.requires_phytosanitary ? 'Required / Available' : 'Not specified' }}</div>
                         </div>
                         <div class="p-3 border rounded text-center" :class="{ 'bg-green-50 border-green-200': product.requires_health_certificate }">
                             <div class="font-bold" :class="{ 'text-green-700': product.requires_health_certificate, 'text-gray-400': !product.requires_health_certificate }">Health Cert</div>
                             <div class="text-xs mt-1">{{ product.requires_health_certificate ? 'Required / Available' : 'Not specified' }}</div>
                         </div>
                          <div class="p-3 border rounded text-center" :class="{ 'bg-green-50 border-green-200': product.requires_cites }">
                             <div class="font-bold" :class="{ 'text-green-700': product.requires_cites, 'text-gray-400': !product.requires_cites }">CITES</div>
                             <div class="text-xs mt-1">{{ product.requires_cites ? 'Required / Available' : 'Not specified' }}</div>
                         </div>
                     </div>
                     
                     <div class="mt-4">
                         <label class="block text-sm font-medium text-gray-500 mb-2">Attached Documents</label>
                         <div v-if="product.documents && product.documents.length > 0" class="space-y-2">
                             <div v-for="(doc, idx) in product.documents" :key="idx" class="flex items-center justify-between p-3 bg-white dark:bg-meta-4 border rounded">
                                 <div class="flex items-center gap-3">
                                     <i class="pi pi-file-pdf text-red-500 text-xl"></i>
                                     <span class="font-medium text-sm">{{ doc.original_name }}</span>
                                 </div>
                                 <a :href="`/storage/${doc.path}`" target="_blank" class="text-primary hover:underline text-sm font-bold">Download</a>
                             </div>
                         </div>
                         <div v-else class="text-gray-400 italic">No documents currently attached.</div>
                     </div>
                 </div>
                 
                 <!-- Step 7: Media -->
                <div v-if="activeStep === 6" class="flex flex-col gap-4">
                    <label class="block text-sm font-medium text-gray-500">Product Images</label>
                    <div v-if="product.images && product.images.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div v-for="(img, idx) in product.images" :key="idx" class="relative group">
                             <Image :src="`/storage/${img}`" preview alt="Product Image" imageClass="w-full h-48 object-cover rounded-lg shadow-sm" />
                             <div v-if="product.primary_image === img" class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded shadow">Main</div>
                        </div>
                    </div>
                     <div v-else class="text-center py-10 bg-gray-50">
                        <i class="pi pi-image text-4xl text-gray-300"></i>
                         <p class="text-gray-400 mt-2">No additional images.</p>
                     </div>
                </div>

            </div>

            <div class="flex justify-between mt-4">
                <Button label="Previous" icon="pi pi-arrow-left" @click="prevStep" :disabled="activeStep === 0" severity="secondary" outlined />
                <Button v-if="activeStep < items.length - 1" label="Next" icon="pi pi-arrow-right" iconPos="right" @click="nextStep" />
                <Button v-else label="Close" icon="pi pi-check" iconPos="right" @click="isVisible = false" severity="success" />
            </div>
        </template>
    </Dialog>
</template>

<style scoped>
/* Override Steps to fit better in modal */
:deep(.product-steps .p-steps-item .p-menuitem-link) {
    background: transparent;
}
</style>
