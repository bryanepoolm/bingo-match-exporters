<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import { useToast } from 'primevue/usetoast';
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
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';

const toast = useToast();

const props = defineProps({
    product: Object,
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

const selectedCategory = ref(props.product.category_id ? {[String(props.product.category_id)]: true} : null);

watch(selectedCategory, (newVal) => {
    if (newVal && Object.keys(newVal).length > 0) {
        form.category_id = Object.keys(newVal)[0];
    } else {
        form.category_id = null;
    }
});

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
    // Basic
    name: props.product.name,
    description: props.product.description,
    category_id: props.product.category_id,
    brand: props.product.brand,
    
    // Classification
    hs_code: props.product.hs_code, 
    target_markets: props.product.target_markets || [],
    origin_country: props.product.origin_country,
    origin_state: props.product.origin_state,
    origin_city: props.product.origin_city,
    
    // Specs
    unit_of_measure: props.product.unit_of_measure,
    price_per_unit: parseFloat(props.product.price_per_unit),
    currency: props.product.currency,
    price_term: props.product.price_term,
    minimum_order_quantity: parseFloat(props.product.minimum_order_quantity),
    available_quantity: parseFloat(props.product.available_quantity),
    
    // Logistics
    packaging_type: props.product.packaging_type,
    units_per_package: props.product.units_per_package,
    weight_per_unit: props.product.weight_per_unit ? parseFloat(props.product.weight_per_unit) : null,
    length: props.product.length ? parseFloat(props.product.length) : null,
    width: props.product.width ? parseFloat(props.product.width) : null,
    height: props.product.height ? parseFloat(props.product.height) : null,
    lead_time_days: props.product.lead_time_days,
    
    requires_cold_chain: Boolean(props.product.requires_cold_chain),
    temperature_min: props.product.temperature_min ? parseFloat(props.product.temperature_min) : null,
    temperature_max: props.product.temperature_max ? parseFloat(props.product.temperature_max) : null,
    
    requires_hazmat: Boolean(props.product.requires_hazmat),
    hazmat_class: props.product.hazmat_class,
    is_perishable: Boolean(props.product.is_perishable),
    special_handling_notes: props.product.special_handling_notes,
    
    // Quality
    quality_grade: props.product.quality_grade,
    shelf_life_days: props.product.shelf_life_days,
    certifications: props.product.certifications?.map(c => c.id) || [], // Assuming relationship or array of IDs
    // Note: If certifications is just a JSON array of IDs from DB, use as is. 
    // If it's a relationship loaded, map to IDs.
    // The cast in model says 'array', but Service syncs relation.
    // Ideally we should use the relation data if available. 
    // For now assuming existing data is just the IDs or we need to handle it.
    
    // Docs
    requires_phytosanitary: Boolean(props.product.requires_phytosanitary),
    requires_health_certificate: Boolean(props.product.requires_health_certificate),
    requires_cites: Boolean(props.product.requires_cites),
    
    documents: [], // New uploads
    preserved_documents: props.product.documents || [], // Keep these by default

    // Images
    images: [], // New Uploads
    preserved_images: props.product.images || [], // Keep these by default
    primary_image_index: null,
    primary_image_path: props.product.primary_image || null,
    
    // SEO
    keywords: props.product.keywords || [],
    tags: props.product.tags || [],
    
    // Settings
    status: props.product.status,
    visibility: props.product.visibility,
    is_featured: Boolean(props.product.is_featured),
});

// HS Code Logic
const filteredHsCodes = ref([]);
const selectedHsCode = ref(props.product.hs_code 
    ? { code: props.product.hs_code, description: props.product.hs_description || props.product.hs_code } 
    : null);

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
}

const submit = () => {
     // If the user typed manually or selected, ensure form has value
     if (selectedHsCode.value) {
         if (typeof selectedHsCode.value === 'object') {
             form.hs_code = selectedHsCode.value.code;
         } else {
             form.hs_code = selectedHsCode.value; // Manually typed
         }
    }

    form.put(route('products.update', props.product.id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Product updated successfully', life: 3000 });
        },
        onError: () => {
             toast.add({ severity: 'error', summary: 'Error', detail: 'Please check the form for errors', life: 3000 });
        }
    });
};

const onSelectDocs = (event) => {
    form.documents = event.files;
    toast.add({ severity: 'info', summary: 'Files Selected', detail: `${event.files.length} documents ready to upload`, life: 3000 });
};

const onSelectImages = (event) => {
    form.images = event.files;
    toast.add({ severity: 'info', summary: 'Images Selected', detail: `${event.files.length} images ready to upload`, life: 3000 });
};

const removeDocument = (index) => {
    form.preserved_documents.splice(index, 1);
};

const removeImage = (index) => {
    const removedPath = form.preserved_images[index];
    form.preserved_images.splice(index, 1);
    
    // If we removed the primary image, clear it
    if (form.primary_image_path === removedPath) {
        form.primary_image_path = null;
        // Fallback to first existing if available
        if (form.preserved_images.length > 0) {
            form.primary_image_path = form.preserved_images[0];
        } else if (form.images.length > 0) {
            // Or first new image
            form.primary_image_index = 0;
        }
    }
};

const removeNewImage = (index, removeFileCallback) => {
    if(removeFileCallback) removeFileCallback(); // Remove from PrimeVue internal state
    // form.images is updated via onSelectImages usually, but if we need manual splice:
    // With custom template, we might need to verify if onSelectImages fires on remove? 
    // Actually using #content slot with custom files means we rely on `files` prop mostly.
    // But `form.images` is what we send. 
    // PrimeVue's removeFile updates the `files` list in the slot scope.
    // DOES IT trigger an update to what we captured in `onSelect`? 
    // Usually `onSelect` fires when files are ADDED. 
    // `onRemove` fires when removed. We should listen to `@remove`.
    // BUT we are manually managing the list via form.images? 
    // Let's assume we stick to the pattern: `form.images` should be kept in sync.
    // The safest way with the #content slot is to rely on the slot's `files` for display, 
    // and ensure `form.images` is updated before submit or synced.
    // Actually, `onSelect` sets `form.images = event.files`.
    // Using `removeFile()` from scope updates the internal state. 
    // We should probably sync `form.images` to that internal state or just manually splice our array 
    // IF `form.images` is what drives the upload.
    // Simpler: Just splice our array. 
    // But `form.images = event.files` makes it a FileList or Array.
    // Let's manually splice form.images.
    form.images.splice(index, 1);
    
    if (form.primary_image_index === index) {
         form.primary_image_index = null;
          // Fallback
        if (form.preserved_images.length > 0) {
            form.primary_image_path = form.preserved_images[0];
        } else if (form.images.length > 0) {
            form.primary_image_index = 0;
        }
    } else if (form.primary_image_index > index && form.primary_image_index !== null) {
        form.primary_image_index--;
    }
}

const setPrimaryExisting = (path) => {
    form.primary_image_path = path;
    form.primary_image_index = null;
};

const setPrimaryNew = (index) => {
    form.primary_image_index = index;
    form.primary_image_path = null;
};

</script>

<template>
    <Head title="Edit Product" />
    <Toast />

    <TailAdminLayout>
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                Edit Product: {{ form.name }}
            </h2>
             <Button label="Save Changes" icon="pi pi-save" @click="submit" :loading="form.processing" />
        </div>

        <div class="card bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-sm shadow-default">
            
            <TabView>
                <TabPanel header="Basic Info">
                    <div class="p-4 flex flex-col gap-4">
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
                </TabPanel>
                
                <TabPanel header="Classification">
                    <div class="p-4 flex flex-col gap-4">
                         <div class="field">
                            <label class="mb-2 block font-medium">HS Code *</label>
                            <AutoComplete 
                                v-model="selectedHsCode" 
                                :suggestions="filteredHsCodes" 
                                @complete="searchHsCodes" 
                                @item-select="onHsCodeSelect"
                                optionLabel="description" 
                                forceSelection 
                                dropdown
                                placeholder="Type to search via code or description..." 
                                class="w-full">
                                <template #option="slotProps">
                                    <div class="flex flex-col">
                                        <span class="font-bold">{{ slotProps.option.code }}</span>
                                        <span class="text-sm">{{ slotProps.option.description }}</span>
                                    </div>
                                </template>
                            </AutoComplete>
                             <small class="block mt-1 text-gray-500">Current Code: {{ form.hs_code }}</small>
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
                </TabPanel>
                
                <TabPanel header="Specs & Pricing">
                     <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
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
                </TabPanel>
                
                <TabPanel header="Logistics">
                    <div class="p-4 flex flex-col gap-4">
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
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
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
                            </div>
                            
                            <div>
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
                </TabPanel>
                
                <TabPanel header="Quality & Docs">
                    <div class="p-4 flex flex-col gap-4">
                        <h3 class="font-bold text-lg">Quality Standards</h3>
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
                        
                        <h3 class="font-bold text-lg mt-4">Documentation Requirements</h3>
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

                        <!-- Existing Documents Display -->
                        <div v-if="form.preserved_documents.length > 0" class="mt-4 p-4 bg-gray-50 border rounded">
                            <h4 class="font-bold text-sm mb-2">Saved Documents</h4>
                            <ul class="list-none p-0 m-0">
                                <li v-for="(doc, index) in form.preserved_documents" :key="index" class="flex items-center justify-between py-2 border-b last:border-0 border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <i class="pi pi-file text-blue-500"></i>
                                        <span class="text-sm font-medium">{{ doc.original_name }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a :href="'/storage/' + doc.path" target="_blank" class="p-button p-component p-button-text p-button-sm p-0">View</a>
                                        <Button icon="pi pi-trash" class="p-button-danger p-button-text p-button-sm" @click="removeDocument(index)" />
                                    </div>
                                </li>
                            </ul>
                        </div>

                         <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded">
                            <h4 class="font-bold mb-2 text-sm text-blue-800">Upload New Documents</h4>
                            <FileUpload mode="advanced" :multiple="true" accept="application/pdf,image/*" :maxFileSize="10000000" :customUpload="true" @uploader="onSelectDocs" :auto="false">
                                <template #empty>
                                    <p>Drag and drop files to here to upload.</p>
                                </template>
                            </FileUpload>
                            <small class="block mt-1" v-if="form.documents.length > 0">{{ form.documents.length }} new files selected.</small>
                        </div>
                    </div>
                </TabPanel>
                
                <TabPanel header="Images">
                     <div class="p-4">
                         <div class="field">
                            <label class="mb-2 block font-medium">Product Images</label>
                            
                            <!-- Existing Images -->
                             <h4 class="font-bold text-sm mb-2 text-gray-600">Saved Images</h4>
                            <div v-if="form.preserved_images.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                <div v-for="(img, index) in form.preserved_images" :key="index" class="relative group border rounded-lg p-2 bg-white shadow-sm hover:shadow-md transition-shadow">
                                    <div class="aspect-square w-full overflow-hidden rounded-md bg-gray-100 relative">
                                        <img :src="'/storage/' + img" class="w-full h-full object-cover rounded" />
                                        
                                         <!-- Overlay Actions -->
                                        <div class="absolute inset-x-0 bottom-0 bg-black/50 p-2 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                             <Button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-sm text-white" @click="removeImage(index)" />
                                             <span v-if="form.primary_image_path === img" class="text-xs text-green-400 font-bold uppercase">Primary</span>
                                             <Button v-else label="Main" class="p-button-sm p-button-ghost text-white text-xs px-2 py-1" @click="setPrimaryExisting(img)" />
                                        </div>
                                    </div>
                                     <!-- Always visible Primary Badge -->
                                     <div v-if="form.primary_image_path === img" class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full shadow-lg z-10 font-bold">
                                        <i class="pi pi-star-fill mr-1"></i> Main
                                    </div>
                                </div>
                            </div>
                            <div v-else class="mb-6 p-4 bg-gray-50 border border-dashed rounded text-center text-gray-500 text-sm">
                                No saved images.
                            </div>

                            <!-- New Uploads -->
                            <h4 class="font-bold text-sm mb-2 text-gray-600">Add New Images</h4>
                            <FileUpload mode="advanced" :multiple="true" accept="image/*" :maxFileSize="5000000" :customUpload="true" @select="onSelectImages" :auto="false" :showUploadButton="false" :showCancelButton="false">
                                <template #content="{ files, removeFile }">
                                    <div v-if="files.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                        <div v-for="(file, index) in files" :key="index" class="relative group border rounded-lg p-2 bg-gray-50 border-gray-200">
                                            <div class="aspect-square w-full overflow-hidden rounded-md bg-gray-200 relative">
                                                <img :src="file.objectURL" :alt="file.name" class="object-cover w-full h-full" />
                                                
                                                <!-- Overlay Actions -->
                                                <div class="absolute inset-x-0 bottom-0 bg-black/50 p-2 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                     <Button icon="pi pi-times" class="p-button-rounded p-button-danger p-button-sm text-white" @click="removeFile(file); removeNewImage(index, () => removeFile(file))" />
                                                     <span v-if="form.primary_image_index === index" class="text-xs text-green-400 font-bold uppercase">Primary</span>
                                                     <Button v-else label="Main" class="p-button-sm p-button-ghost text-white text-xs px-2 py-1" @click="setPrimaryNew(index)" />
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
                            <small class="block mt-1" v-if="form.images.length > 0">{{ form.images.length }} new images selected.</small>
                        </div>
                    </div>
                </TabPanel>
                
                <TabPanel header="Settings">
                     <div class="p-4 flex flex-col gap-4">
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
                                <small class="text-gray-500">Your product will be highlighted in the marketplace.</small>
                            </div>
                        </div>
                    </div>
                </TabPanel>
            </TabView>
        </div>
    </TailAdminLayout>
</template>
