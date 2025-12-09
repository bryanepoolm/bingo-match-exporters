<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import FileUpload from 'primevue/fileupload';
import { Head } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import Modal from '@/Components/Common/Modal.vue';
import MultiSelect from 'primevue/multiselect';

const props = defineProps({
    company: Object,
    capabilities: Array,
});

const toast = useToast();
const isEditingInfo = ref(false);

const form = useForm({
    name: props.company.name,
    tax_id: props.company.tax_id,
    website: props.company.website,
    description: props.company.description,
    type: props.company.type,
    logo: null,
    logo_preview: null,
    verification_documents: [],
    capabilities: props.company.exporter?.capabilities?.map(c => c.id) || [],
});

const types = [
    { name: 'Producer', value: 'producer' },
    { name: 'Exporter', value: 'exporter' },
];

const onLogoSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        form.logo_preview = URL.createObjectURL(file);
        // Auto submit logo update if desired, or wait for save. 
        // For this design, let's allow logo update independently or as part of the form.
        // Since the logo is in a separate card, maybe we should have a save button there or just auto-save?
        // The reference doesn't explicitly show save behavior for avatar. 
        // I'll assume the "Save Changes" in the modal is for info. 
        // For the logo, I'll add a small "Save Logo" button if a file is selected, or just include it in the main submit if I wrap everything in one form?
        // The user said "En el segundo pondras para la informacion... apareciendo un modal".
        // This implies the modal is ONLY for info.
        // So I should probably handle logo upload separately or have a save button in the logo card.
        // Let's add a "Update Logo" button in the logo card that appears when a file is selected.
    }
};

const updateLogo = () => {
    if (!form.logo) return;
    
    router.post(route('company.update'), {
        logo: form.logo,
    }, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Logo updated successfully', life: 3000 });
            form.logo = null;
            form.logo_preview = null;
        },
    });
};

const onSelectDocuments = (event) => {
    form.verification_documents = event.files;
    // Auto upload or wait? The previous code had a single submit.
    // I'll add a "Upload Documents" button in the documents card.
};

const uploadDocuments = () => {
    if (form.verification_documents.length === 0) return;

    router.post(route('company.update'), {
        verification_documents: form.verification_documents,
    }, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Documents uploaded successfully', life: 3000 });
            form.verification_documents = [];
        },
    });
};

const submitInfo = () => {
    form.post(route('company.update'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Profile updated successfully', life: 3000 });
            isEditingInfo.value = false;
        },
    });
};

const deleteDocument = (path) => {
    if (confirm('Are you sure you want to delete this document?')) {
        router.delete(route('company.document.destroy'), {
            data: { path },
            preserveScroll: true,
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Deleted', detail: 'Document deleted successfully', life: 3000 });
            },
        });
    }
};

const getTypeName = (value) => {
    const type = types.find(t => t.value === value);
    return type ? type.name : value;
};
</script>

<template>
    <Head title="Edit Profile" />
    <TailAdminLayout>
        <Toast />
        
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">
                Company Profile
            </h3>

            <!-- Card 1: Avatar / Logo -->
            <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex flex-col items-center w-full gap-6 xl:flex-row">
                        <div class="relative w-24 h-24 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800">
                            <img :src="form.logo_preview || (company.logo_path ? `/storage/${company.logo_path}` : 'https://ui-avatars.com/api/?name=' + company.name + '&background=random&size=128')" 
                                 alt="Company Logo" 
                                 class="h-full w-full object-cover" />
                            <label for="logo-upload" class="absolute inset-0 flex items-center justify-center bg-black/30 text-white opacity-0 hover:opacity-100 cursor-pointer transition-opacity">
                                <i class="pi pi-camera text-xl"></i>
                            </label>
                            <input type="file" id="logo-upload" class="hidden" accept="image/*" @change="onLogoSelect" />
                        </div>
                        <div class="text-center xl:text-left">
                            <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                                {{ company.name }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ getTypeName(company.type) }}
                            </p>
                        </div>
                    </div>
                    <button v-if="form.logo" @click="updateLogo" class="flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                        <i class="pi pi-save"></i> Save Logo
                    </button>
                </div>
            </div>

            <!-- Card 2: Company Information -->
            <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        Company Information
                    </h4>
                    <button @click="isEditingInfo = true" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-white/[0.03]">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill=""/>
                        </svg>
                        Edit
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">Company Name</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ company.name }}</p>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">Tax ID / RFC</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ company.tax_id || '-' }}</p>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">Website</label>
                        <a v-if="company.website" :href="company.website" target="_blank" class="text-sm text-brand-500 hover:underline">{{ company.website }}</a>
                        <p v-else class="text-sm text-gray-900 dark:text-white">-</p>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">Type</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ getTypeName(company.type) }}</p>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ company.description || '-' }}</p>
                    </div>
                    <div class="col-span-1 md:col-span-2" v-if="company.type === 'exporter' || company.type === 'both'">
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">Capabilities</label>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="cap in company.exporter?.capabilities" :key="cap.id" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-100 text-brand-800 dark:bg-brand-900 dark:text-brand-200">
                                {{ cap.name }}
                            </span>
                            <span v-if="!company.exporter?.capabilities?.length" class="text-sm text-gray-500">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Documents -->
            <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                    Verification Documents
                </h4>
                
                <div class="mb-6">
                    <FileUpload name="verification_documents[]" @select="onSelectDocuments" :multiple="true" accept=".pdf,.jpg,.jpeg,.png" :maxFileSize="10000000" customUpload @uploader="() => {}">
                        <template #empty>
                            <p>Drag and drop files to here to upload.</p>
                        </template>
                    </FileUpload>
                    <div class="mt-4 flex justify-end" v-if="form.verification_documents.length > 0">
                         <button @click="uploadDocuments" class="flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                            <i class="pi pi-upload"></i> Upload Selected
                        </button>
                    </div>
                </div>

                <div v-if="company.verification_documents && company.verification_documents.length > 0">
                    <h6 class="font-medium text-black dark:text-white mb-4">Uploaded Documents:</h6>
                    <ul class="space-y-3">
                        <li v-for="(doc, index) in company.verification_documents" :key="index" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="w-10 h-10 rounded-full bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center text-brand-600 flex-shrink-0">
                                    <i class="pi pi-file text-lg"></i>
                                </div>
                                <a :href="`/storage/${doc}`" target="_blank" rel="noopener noreferrer" class="text-sm text-black dark:text-white hover:text-brand-600 font-medium truncate transition-colors">
                                    {{ doc.split('/').pop().split('_').slice(1).join('_') || doc.split('/').pop() }}
                                </a>
                            </div>
                            <button @click="deleteDocument(doc)" class="text-gray-500 hover:text-error-500 transition-colors">
                                <i class="pi pi-trash"></i>
                            </button>
                        </li>
                    </ul>
                </div>
                <div v-else class="text-center text-gray-500 dark:text-gray-400 py-4">
                    No documents uploaded yet.
                </div>
            </div>
        </div>

        <!-- Edit Info Modal -->
        <Modal v-if="isEditingInfo" @close="isEditingInfo = false">
            <template #body>
                <div class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                    <button @click="isEditingInfo = false" class="transition-color absolute right-5 top-5 z-50 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
                        <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z" fill=""/>
                        </svg>
                    </button>
                    
                    <div class="px-2 pr-14">
                        <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                            Edit Company Information
                        </h4>
                        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
                            Update your company details.
                        </p>
                    </div>

                    <form @submit.prevent="submitInfo" class="flex flex-col">
                        <div class="custom-scrollbar h-[458px] overflow-y-auto p-2">
                            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                                <div class="col-span-2 lg:col-span-1">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Company Name</label>
                                    <input v-model="form.name" type="text" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                                    <small class="text-error-500" v-if="form.errors.name">{{ form.errors.name }}</small>
                                </div>

                                <div class="col-span-2 lg:col-span-1">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tax ID / RFC</label>
                                    <input v-model="form.tax_id" type="text" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                                    <small class="text-error-500" v-if="form.errors.tax_id">{{ form.errors.tax_id }}</small>
                                </div>

                                <div class="col-span-2 lg:col-span-1">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Website</label>
                                    <input v-model="form.website" type="text" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                                    <small class="text-error-500" v-if="form.errors.website">{{ form.errors.website }}</small>
                                </div>

                                <div class="col-span-2 lg:col-span-1">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Type</label>
                                    <select v-model="form.type" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                                        <option v-for="type in types" :key="type.value" :value="type.value">{{ type.name }}</option>
                                    </select>
                                    <small class="text-error-500" v-if="form.errors.type">{{ form.errors.type }}</small>
                                </div>

                                <div class="col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
                                    <textarea v-model="form.description" rows="4" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"></textarea>
                                </div>

                                <div class="col-span-2" v-if="form.type === 'exporter' || form.type === 'both'">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Capabilities</label>
                                    <MultiSelect v-model="form.capabilities" :options="capabilities" optionLabel="name" optionValue="id" placeholder="Select Capabilities" :filter="true" display="chip" class="w-full" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
                            <button @click="isEditingInfo = false" type="button" class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                                Close
                            </button>
                            <button type="submit" class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto" :disabled="form.processing">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </Modal>
    </TailAdminLayout>
</template>


