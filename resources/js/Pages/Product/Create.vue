<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import { useToast } from 'primevue/usetoast';
import Steps from 'primevue/steps';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import AutoComplete from 'primevue/autocomplete';
import Checkbox from 'primevue/checkbox';
import FileUpload from 'primevue/fileupload';
import Toast from 'primevue/toast';
import Chips from 'primevue/chips';
import TreeSelect from 'primevue/treeselect';
import MultiSelect from 'primevue/multiselect';
import { computed } from 'vue';

const toast = useToast();

const props = defineProps({
    categories: Array,
    certifications: Array,
});

// Transform categories to TreeSelect nodes
const categoryNodes = computed(() => {
    const transform = (cats) => {
        if (!Array.isArray(cats)) return [];
        
        return cats.map(c => {
            const hasChildren = Array.isArray(c.children) && c.children.length > 0;
            
            return {
                key: String(c.id),
                label: c.name,
                data: c,
                icon: c.icon,
                leaf: !hasChildren,
                children: hasChildren ? transform(c.children) : []
            };
        });
    };
    
    return transform(props.categories || []);
});

// TreeSelect Model (Object format: {'key': true})
const selectedCategory = ref(null);

// Sync selectedCategory to form.category_id
import { watch } from 'vue';
watch(selectedCategory, (newVal) => {
    if (newVal && Object.keys(newVal).length > 0) {
        form.category_id = Object.keys(newVal)[0]; // Extract the ID
    } else {
        form.category_id = null;
    }
});

// Enums (Should ideally be passed from backend or shared)
const units = [
    { label: 'Kilograms', value: 'kg' },
    { label: 'Tons', value: 'tons' },
    { label: 'Liters', value: 'liters' },
    { label: 'Pieces', value: 'pieces' },
    { label: 'Boxes', value: 'boxes' },
    { label: 'Pallets', value: 'pallets' },
];

const incoterms = [
    { label: 'EXW (Ex Works)', value: 'exw' },
    { label: 'FOB (Free On Board)', value: 'fob' },
    { label: 'CIF (Cost Insurance Freight)', value: 'cif' },
    { label: 'DDP (Delivered Duty Paid)', value: 'ddp' },
];

const currencies = [
    { label: 'USD', value: 'USD' },
    { label: 'MXN', value: 'MXN' },
    { label: 'EUR', value: 'EUR' },
];

const statuses = [
    { label: 'Draft', value: 'draft' },
    { label: 'Active', value: 'active' },
    { label: 'Inactive', value: 'inactive' },
];

const visibilityOptions = [
    { label: 'Public (Everyone)', value: 'public' },
    { label: 'Private (Me Only)', value: 'private' },
    { label: 'Partners Only', value: 'partners_only' },
];

const form = useForm({
    // Step 1: Basic
    name: '',
    description: '',
    category_id: null, // Need to implement category selection properly or assume fixed for now
    brand: '',
    
    // Step 2: Classification
    hs_code: '', 
    target_markets: [],
    
    // Origin (New)
    origin_country: 'MX', // Default
    origin_state: '',
    origin_city: '',
    
    // Step 3: Specs
    unit_of_measure: null,
    price_per_unit: null,
    currency: 'USD',
    price_term: null,
    minimum_order_quantity: null,
    available_quantity: null,
    
    // Step 4: Logistics
    packaging_type: '',
    units_per_package: null,
    weight_per_unit: null,
    length: null,
    width: null,
    height: null,
    
    // Step 5: Quality
    quality_grade: '',
    shelf_life_days: null,
    certifications: [],

    // Step 6: Documentation
    requires_phytosanitary: false,
    requires_health_certificate: false,
    requires_cites: false,
    additional_documents_required: [], // For the checklist if needed? Or just use documents array
    documents: [], // For actual files

    requires_cold_chain: false,
    temperature_min: null,
    temperature_max: null,
    lead_time_days: null,
    
    requires_hazmat: false,
    hazmat_class: '',
    is_perishable: false,
    special_handling_notes: '',
    
    // Step 5: Media
    images: [], // URLs or Files
    primary_image_index: 0, 
    
    // SEO
    keywords: [],
    tags: [],
    
    // Step 7: Settings
    status: 'draft',
    visibility: 'public',
    is_featured: false,
});

// HS Code Autocomplete
const filteredHsCodes = ref([]);
const selectedHsCode = ref(null);

const searchHsCodes = async (event) => {
    try {
        const response = await fetch(route('api.hs-codes.search') + `?query=${event.query}`);
        const data = await response.json();
        filteredHsCodes.value = data;
    } catch (e) {
        console.error(e);
    }
};

const onHsCodeSelect = (event) => {
    form.hs_code = event.value.code;
    // Potentially auto-fill category or description
};

const items = ref([
    { label: 'Basic Info' },
    { label: 'Classification' },
    { label: 'Specs' },
    { label: 'Logistics' },
    { label: 'Quality' },
    { label: 'Documentation' },
    { label: 'Media' },
    { label: 'Settings' }
]);

const activeStep = ref(0);

const nextStep = () => {
    // Add validation check here if needed
    activeStep.value++;
};

const prevStep = () => {
    activeStep.value--;
};

const submit = () => {
    // Transform data if needed
    if (selectedHsCode.value) {
        form.hs_code = selectedHsCode.value.code;
    }
    
    // Ensure certifications is array of IDs
    
    form.post(route('products.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Product created successfully', life: 3000 });
        },
        onError: () => {
             toast.add({ severity: 'error', summary: 'Error', detail: 'Please check the form for errors', life: 3000 });
        }
    });
};

const onUpload = (event) => {
    // Handle file upload response
    console.log('Uploaded:', event);
    toast.add({ severity: 'info', summary: 'Success', detail: 'File Uploaded', life: 3000 });
};

const onSelectDocs = (event) => {
    // PrimeVue Advanced Uploader 'uploader' event gives { files }
    // We add them to form.documents
    form.documents = event.files;
    toast.add({ severity: 'info', summary: 'Files Selected', detail: `${event.files.length} documents ready to upload`, life: 3000 });
};

const onSelectImages = (event) => {
    // Keep existing files if any and append new ones? 
    // Usually FileUpload replaces unless we manually manage.
    // Let's assume FileUpload gives us the *current* list of files in 'files' property of event.
    form.images = event.files;
    
    // Default primary to 0 if not set or out of bounds
    if (form.primary_image_index >= form.images.length) {
        form.primary_image_index = 0;
    }
};

const removeImage = (index) => {
    // We need to remove from the FileUpload component internal list too if possible, 
    // but controlling FileUpload state from outside is tricky. 
    // Better to let FileUpload handle removal via template if possible, or just manage form.images.
    // If we manage form.images separate from FileUpload visual list, we might get out of sync.
    // Strategy: We will render form.images OURSELVES and hide default FileUpload content.
    form.images.splice(index, 1);
    if (form.primary_image_index >= index && form.primary_image_index > 0) {
        form.primary_image_index--;
    }
};

const setPrimary = (index) => {
    form.primary_image_index = index;
};

</script>

<template>
    <Head title="Create Product" />
    <Toast />

    <TailAdminLayout>
        <div class="mb-6">
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                New Product
            </h2>
        </div>

        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <Steps :model="items" :readonly="true" :activeStep="activeStep" class="mb-8" />
            
            <form @submit.prevent="submit">
                <!-- Step 1: Basic Info -->
                <div v-if="activeStep === 0" class="flex flex-col gap-4">
                    <div class="field">
                        <label class="mb-2 block font-medium">Product Name *</label>
                        <InputText v-model="form.name" class="w-full" :class="{'p-invalid': form.errors.name}" />
                        <small class="p-error" v-if="form.errors.name">{{ form.errors.name }}</small>
                    </div>
                    <div class="field">
                        <label class="mb-2 block font-medium">Description *</label>
                        <Textarea v-model="form.description" rows="5" class="w-full" :class="{'p-invalid': form.errors.description}" />
                        <small class="p-error" v-if="form.errors.description">{{ form.errors.description }}</small>
                    </div>
                    <div class="field">
                        <label class="mb-2 block font-medium">Category *</label>
                        <TreeSelect v-model="selectedCategory" :options="categoryNodes" placeholder="Select a Category" class="w-full" :class="{'p-invalid': form.errors.category_id}" selectionMode="single" />
                        <small class="p-error" v-if="form.errors.category_id">{{ form.errors.category_id }}</small>
                    </div>
                    
                    <div class="field">
                         <label class="mb-2 block font-medium">Brand</label>
                         <InputText v-model="form.brand" class="w-full" placeholder="e.g. Sunny Farms" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="field">
                             <label class="mb-2 block font-medium">Search Keywords</label>
                             <Chips v-model="form.keywords" separator="," placeholder="Type and press enter" class="w-full" />
                        </div>
                        <div class="field">
                             <label class="mb-2 block font-medium">Tags</label>
                             <Chips v-model="form.tags" separator="," placeholder="Internal tags" class="w-full" />
                        </div>
                    </div>
                </div>
                
                <!-- Step 2: Classification -->
                <div v-if="activeStep === 1" class="flex flex-col gap-4">
                     <div class="field">
                        <label class="mb-2 block font-medium">HS Code *</label>
                        <AutoComplete v-model="selectedHsCode" :suggestions="filteredHsCodes" @complete="searchHsCodes" 
                            optionLabel="description" optionValue="code" forceSelection dropdown
                            placeholder="Type to search via code or description..." class="w-full">
                            <template #option="slotProps">
                                <div class="flex flex-col">
                                    <span class="font-bold">{{ slotProps.option.code }}</span>
                                    <span class="text-sm">{{ slotProps.option.description }}</span>
                                </div>
                            </template>
                        </AutoComplete>
                        <small class="block mt-1 text-gray-500" v-if="selectedHsCode">Selected Code: {{ selectedHsCode.code }}</small>
                        <small class="p-error" v-if="form.errors.hs_code">{{ form.errors.hs_code }}</small>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="field">
                            <label class="mb-2 block font-medium">Origin Country *</label>
                             <InputText v-model="form.origin_country" class="w-full" placeholder="MX" />
                        </div>
                        <div class="field">
                            <label class="mb-2 block font-medium">Origin State *</label>
                             <InputText v-model="form.origin_state" class="w-full" />
                        </div>
                        <div class="field">
                            <label class="mb-2 block font-medium">Origin City *</label>
                             <InputText v-model="form.origin_city" class="w-full" />
                        </div>
                    </div>
                    
                    <div class="field">
                        <label class="mb-2 block font-medium">Target Markets</label>
                        <div class="flex gap-4">
                            <!-- Simple example checkboxes for markets -->
                             <div class="flex align-items-center">
                                <Checkbox v-model="form.target_markets" inputId="us" name="market" value="US" />
                                <label for="us" class="ml-2">USA</label>
                            </div>
                             <div class="flex align-items-center">
                                <Checkbox v-model="form.target_markets" inputId="eu" name="market" value="EU" />
                                <label for="eu" class="ml-2">Europe</label>
                            </div>
                            <div class="flex align-items-center">
                                <Checkbox v-model="form.target_markets" inputId="ca" name="market" value="CN" />
                                <label for="ca" class="ml-2">China</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Specs -->
                <div v-if="activeStep === 2" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="field">
                        <label class="mb-2 block font-medium">Unit of Measure *</label>
                        <Dropdown v-model="form.unit_of_measure" :options="units" optionLabel="label" optionValue="value" placeholder="Select Unit" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="mb-2 block font-medium">Price per Unit *</label>
                         <div class="p-inputgroup">
                            <span class="p-inputgroup-addon p-0">
                                <Dropdown v-model="form.currency" :options="currencies" optionLabel="label" optionValue="value" class="w-full h-full border-none shadow-none" style="min-width: 80px;" />
                            </span>
                            <InputNumber v-model="form.price_per_unit" mode="currency" :currency="form.currency" locale="en-US" class="w-full" />
                        </div>
                    </div>
                     <div class="field">
                        <label class="mb-2 block font-medium">Incoterm *</label>
                        <Dropdown v-model="form.price_term" :options="incoterms" optionLabel="label" optionValue="value" placeholder="Select Price Term" class="w-full" />
                    </div>
                     <div class="field">
                        <label class="mb-2 block font-medium">Available Quantity *</label>
                        <InputNumber v-model="form.available_quantity" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="mb-2 block font-medium">Min. Order Quantity *</label>
                        <InputNumber v-model="form.minimum_order_quantity" class="w-full" />
                    </div>
                </div>

                <!-- Step 4: Logistics -->
                <div v-if="activeStep === 3" class="flex flex-col gap-4">
                     <div class="field">
                        <label class="mb-2 block font-medium">Packaging Type</label>
                        <InputText v-model="form.packaging_type" placeholder="e.g. Cardboard Boxes" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="mb-2 block font-medium">Lead Time (Days)</label>
                        <InputNumber v-model="form.lead_time_days" class="w-full" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                         <div class="field">
                            <label class="mb-2 block font-medium">Weight per Unit (kg)</label>
                            <InputNumber v-model="form.weight_per_unit" :minFractionDigits="1" class="w-full" />
                        </div>
                        <div class="field">
                            <label class="mb-2 block font-medium">Length (cm)</label>
                            <InputNumber v-model="form.length" :minFractionDigits="1" class="w-full" />
                        </div>
                        <div class="field">
                            <label class="mb-2 block font-medium">Width (cm)</label>
                            <InputNumber v-model="form.width" :minFractionDigits="1" class="w-full" />
                        </div>
                        <div class="field">
                            <label class="mb-2 block font-medium">Height (cm)</label>
                            <InputNumber v-model="form.height" :minFractionDigits="1" class="w-full" />
                        </div>
                    </div>
                    
                    <div class="field-checkbox flex items-center gap-2 mt-4">
                        <Checkbox v-model="form.requires_cold_chain" :binary="true" inputId="cold_chain" />
                        <label for="cold_chain" class="font-medium">Requires Cold Chain?</label>
                    </div>
                    
                    <div v-if="form.requires_cold_chain" class="grid grid-cols-2 gap-4 mt-2 p-4 bg-gray-50 border rounded">
                        <div class="field">
                            <label class="mb-2 block text-sm">Min Temp (°C)</label>
                            <InputNumber v-model="form.temperature_min" :minFractionDigits="1" class="w-full" />
                        </div>
                         <div class="field">
                            <label class="mb-2 block text-sm">Max Temp (°C)</label>
                            <InputNumber v-model="form.temperature_max" :minFractionDigits="1" class="w-full" />
                        </div>
                    </div>
                    
                    <div class="field-checkbox flex items-center gap-2 mt-4">
                        <Checkbox v-model="form.requires_hazmat" :binary="true" inputId="hazmat" />
                        <label for="hazmat" class="font-medium">Hazardous Material (Hazmat)?</label>
                    </div>
                    
                    <div v-if="form.requires_hazmat" class="p-4 bg-red-50 border border-red-100 rounded mt-2">
                        <div class="field">
                            <label class="mb-2 block text-sm">Hazmat Class / UN Class</label>
                            <InputText v-model="form.hazmat_class" placeholder="e.g. Class 3 Flammable" class="w-full" />
                        </div>
                    </div>
                    
                    <div class="field-checkbox flex items-center gap-2 mt-4">
                        <Checkbox v-model="form.is_perishable" :binary="true" inputId="perishable" />
                        <label for="perishable" class="font-medium">Is Perishable?</label>
                    </div>
                    
                    <div class="field mt-4">
                         <label class="mb-2 block font-medium">Special Handling Notes</label>
                         <Textarea v-model="form.special_handling_notes" rows="3" class="w-full" placeholder="e.g. Do not stack double..." />
                    </div>
                </div>

                <!-- Step 5: Quality -->
                <div v-if="activeStep === 4" class="flex flex-col gap-4">
                    <div class="field">
                        <label class="mb-2 block font-medium">Quality Grade</label>
                        <InputText v-model="form.quality_grade" placeholder="e.g. Premium Size A" class="w-full" />
                    </div>
                    
                    <div class="field">
                        <label class="mb-2 block font-medium">Shelf Life (Days)</label>
                        <InputNumber v-model="form.shelf_life_days" class="w-full" />
                    </div>
                    
                    <div class="field">
                        <label class="mb-2 block font-medium">Certifications</label>
                         <MultiSelect v-model="form.certifications" :options="props.certifications" optionLabel="name" optionValue="id" display="chip" placeholder="Select Certifications" class="w-full" />
                    </div>
                </div>

                <!-- Step 6: Documentation -->
                <div v-if="activeStep === 5" class="flex flex-col gap-4">
                     <div class="field-checkbox flex items-center gap-2">
                        <Checkbox v-model="form.requires_phytosanitary" :binary="true" inputId="phyto" />
                        <label for="phyto" class="font-medium">Requires Phytosanitary Certificate?</label>
                    </div>
                    
                    <div class="field-checkbox flex items-center gap-2">
                        <Checkbox v-model="form.requires_health_certificate" :binary="true" inputId="health" />
                        <label for="health" class="font-medium">Requires Health Certificate?</label>
                    </div>
                    
                    <div class="field-checkbox flex items-center gap-2">
                        <Checkbox v-model="form.requires_cites" :binary="true" inputId="cites" />
                        <label for="cites" class="font-medium">Requires CITES (Endangered Species)?</label>
                    </div>
                    
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded">
                        <h4 class="font-bold mb-2 text-sm text-blue-800">Upload Product Documents</h4>
                        <p class="text-xs text-gray-600 mb-3">Upload any relevant documents (Specs, Certs examples, etc.). Files will be saved on product creation.</p>
                        <!-- Custom File Input logic using PrimeVue FileUpload in 'basic' mode causing auto-upload. 
                             Better to use a custom simple input or manual FileUpload usage. 
                             PrimeVue 'customUpload' mode lets us intercept. -->
                        <FileUpload mode="advanced" :multiple="true" accept="application/pdf,image/*" :maxFileSize="10000000" :customUpload="true" @uploader="onSelectDocs" :auto="false">
                            <template #empty>
                                <p>Drag and drop files to here to upload.</p>
                            </template>
                        </FileUpload>
                        <small class="block mt-1" v-if="form.documents.length > 0">{{ form.documents.length }} files selected.</small>
                    </div>
                </div>

                <!-- Step 7: Media -->
                <div v-if="activeStep === 6" class="flex flex-col gap-4">
                         <div class="field">
                            <label class="mb-2 block font-medium">Images</label>
                            <FileUpload mode="advanced" :multiple="true" accept="image/*" :maxFileSize="5000000" :customUpload="true" @select="onSelectImages" :auto="false" :showUploadButton="false" :showCancelButton="false">
                                <template #content="{ files, removeFile }">
                                    <div v-if="files.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                        <div v-for="(file, index) in files" :key="index" class="relative group border rounded-lg p-2 bg-gray-50 border-gray-200">
                                            <div class="aspect-square w-full overflow-hidden rounded-md bg-gray-200 relative">
                                                <img :src="file.objectURL" :alt="file.name" class="object-cover w-full h-full" />
                                                
                                                <!-- Overlay Actions -->
                                                <div class="absolute inset-x-0 bottom-0 bg-black/50 p-2 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                     <Button icon="pi pi-times" class="p-button-rounded p-button-danger p-button-sm text-white" @click="removeFile(file); removeImage(index)" />
                                                     <span v-if="form.primary_image_index === index" class="text-xs text-green-400 font-bold uppercase">Primary</span>
                                                     <Button v-else label="Main" class="p-button-sm p-button-ghost text-white text-xs px-2 py-1" @click="setPrimary(index)" />
                                                </div>
                                            </div>
                                            <!-- Always visible Primary Badge -->
                                             <div v-if="form.primary_image_index === index" class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full shadow-lg z-10 font-bold">
                                                <i class="pi pi-star-fill mr-1"></i> Main
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                                         <i class="pi pi-images text-4xl text-gray-400 mb-2"></i>
                                         <p class="text-gray-500">Drag and drop images here to upload.</p>
                                    </div>
                                </template>
                            </FileUpload>
                            <small class="block mt-2 text-gray-500">Click "Main" on an image to set it as the primary thumbnail. First image is default.</small>
                        </div>
                </div>

                <!-- Step 8: Settings -->
                <div v-if="activeStep === 7" class="flex flex-col gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="field">
                            <label class="mb-2 block font-medium">Product Status</label>
                            <Dropdown v-model="form.status" :options="statuses" optionLabel="label" optionValue="value" class="w-full" />
                        </div>
                        <div class="field">
                            <label class="mb-2 block font-medium">Visibility</label>
                            <Dropdown v-model="form.visibility" :options="visibilityOptions" optionLabel="label" optionValue="value" class="w-full" />
                        </div>
                    </div>
                    
                    <div class="field-checkbox flex items-center gap-2 mt-4 p-4 border rounded bg-gray-50">
                        <Checkbox v-model="form.is_featured" :binary="true" inputId="featured" />
                        <div>
                            <label for="featured" class="font-medium block">Request to be Featured?</label>
                            <small class="text-gray-500">Your product will be highlighted in the marketplace (Additional fees may apply).</small>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-100 rounded text-sm text-yellow-800">
                        <i class="pi pi-info-circle mr-2"></i>
                        <strong>Verification Status:</strong> New products require administrative verification before earning the "Verified" badge.
                    </div>
                </div>

                <div class="flex justify-between mt-8 mb-4">
                    <Button label="Back" icon="pi pi-arrow-left" @click="prevStep" :disabled="activeStep === 0" severity="secondary" />
                    
                    <Button :label="activeStep === 7 ? 'Published Product' : 'Next'" :icon="activeStep === 7 ? 'pi pi-check' : 'pi pi-arrow-right'" iconPos="right" @click="activeStep === 7 ? submit() : nextStep()" :loading="activeStep === 7 && form.processing" />
                </div>
            </form>
        </div>
    </TailAdminLayout>
</template>
```
