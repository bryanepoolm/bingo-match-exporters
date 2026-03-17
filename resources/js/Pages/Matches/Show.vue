<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';

import Button from 'primevue/button';
import Tag from 'primevue/tag';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import Toast from 'primevue/toast';

import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import InputText from 'primevue/inputtext';
import Calendar from 'primevue/calendar';
import Checkbox from 'primevue/checkbox';
import Message from 'primevue/message';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    match: Object,
    products: Array,
    services: Array,
    myProducts: Array,
    isReceiver: Boolean,
});

const confirm = useConfirm();
const toast = useToast();

const rejectionReason = ref('');
const showRejectDialog = ref(false);
const form = useForm({
    rejection_reason: '',
});

const showAcceptDialog = ref(false);
const acceptForm = useForm({
    products: [],
    origin: '',
    destination: '',
    tentative_date: null,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const formatCurrency = (amount, currency) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency }).format(amount);
};

const getSeverity = (status) => {
    switch (status) {
        case 'pending': return 'warning';
        case 'accepted': return 'success';
        case 'rejected': return 'danger';
        default: return 'info';
    }
};

const openAcceptDialog = () => {
    acceptForm.products = [];
    acceptForm.origin = props.match.origin || '';
    acceptForm.destination = props.match.destination || '';
    acceptForm.tentative_date = props.match.tentative_date ? new Date(props.match.tentative_date) : null;
    showAcceptDialog.value = true;
};

const confirmAcceptWithForm = () => {
    acceptForm.post(route('matches.accept', props.match.id), {
        onSuccess: () => {
            showAcceptDialog.value = false;
            toast.add({ severity: 'success', summary: 'Accepted', detail: 'You have accepted the request and attached your products.', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill all required fields correctly.', life: 3000 });
        }
    });
};

const acceptRequest = () => {
    if (props.isReceiver && props.match.initiator_type === 'exporter') {
        openAcceptDialog();
        return;
    }

    confirm.require({
        message: 'Are you sure you want to accept this connection request?',
        header: 'Confirm Acceptance',
        icon: 'pi pi-check',
        acceptClass: 'p-button-success',
        accept: () => {
             router.post(route('matches.accept', props.match.id), {}, {
                 onSuccess: () => {
                     toast.add({ severity: 'success', summary: 'Accepted', detail: 'You have accepted the request.', life: 3000 });
                 },
                 onError: () => {
                     toast.add({ severity: 'error', summary: 'Error', detail: 'Could not accept request.', life: 3000 });
                 }
             });
        }
    });
};

const openRejectDialog = () => {
    rejectionReason.value = '';
    showRejectDialog.value = true;
};

const confirmReject = () => {
    form.rejection_reason = rejectionReason.value;
    form.post(route('matches.reject', props.match.id), {
        onSuccess: () => {
            showRejectDialog.value = false;
            toast.add({ severity: 'info', summary: 'Rejected', detail: 'Request rejected.', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Could not reject request.', life: 3000 });
        }
    });
};

const cancelRequest = () => {
    confirm.require({
        message: 'Are you sure you want to cancel this connection request?',
        header: 'Confirm Cancel',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
             router.delete(route('matches.destroy', props.match.id), {
                 onSuccess: () => {
                     toast.add({ severity: 'success', summary: 'Canceled', detail: 'You have canceled the connection request.', life: 3000 });
                 },
                 onError: () => {
                     toast.add({ severity: 'error', summary: 'Error', detail: 'Could not cancel request.', life: 3000 });
                 }
             });
        }
    });
};

</script>

<template>
    <Head :title="`Request from ${match.producer.company.name}`" />
    <Toast />
    <Dialog v-model:visible="showRejectDialog" modal header="Reject Request" :style="{ width: '50vw' }">
        <div class="flex flex-col gap-4">
            <p class="text-gray-600 dark:text-gray-300">Please provide a reason for rejecting this request (Optional):</p>
            <Textarea v-model="rejectionReason" rows="5" placeholder="Reason for rejection..." class="w-full border-gray-300 dark:border-gray-600 dark:bg-boxdark dark:text-white" />
        </div>
        <template #footer>
            <Button label="Cancel" icon="pi pi-times" @click="showRejectDialog = false" text />
            <Button label="Reject Request" icon="pi pi-check" severity="danger" @click="confirmReject" :loading="form.processing" />
        </template>
    </Dialog>

    <!-- Accept Dialog for Producers responding to Exporters -->
    <Dialog v-model:visible="showAcceptDialog" modal header="Accept Connection Request" :style="{ width: '80vw', maxWidth: '800px' }" :maximizable="true">
        <div class="flex flex-col gap-6 py-4">
            <p class="text-gray-600 dark:text-gray-300">
                To accept this logistics service request, you must select the products you intend to send and confirm the logistics details.
            </p>
            
            <Message v-if="acceptForm.errors.products" severity="error" :text="acceptForm.errors.products" />
            
            <div class="mb-4">
                <h4 class="mb-3 text-lg font-semibold text-black dark:text-white">1. Select Products</h4>
                <div v-if="myProducts && myProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="product in myProducts" :key="product.id" 
                        :class="[
                            'cursor-pointer rounded-lg border p-4 transition-all hover:shadow-md flex items-start gap-4',
                            acceptForm.products.includes(product.id) ? 'border-primary bg-primary/5' : 'border-stroke dark:border-strokedark bg-white dark:bg-meta-4'
                        ]"
                        @click="() => {
                            const index = acceptForm.products.indexOf(product.id);
                            if (index > -1) acceptForm.products.splice(index, 1);
                            else acceptForm.products.push(product.id);
                        }">
                        
                        <div class="pt-1">
                            <Checkbox v-model="acceptForm.products" :inputId="`prod-${product.id}`" :value="product.id" @click.stop />
                        </div>
                        <div class="flex-1">
                            <div class="h-24 w-full rounded bg-gray-100 dark:bg-gray-800 mb-2 overflow-hidden">
                                <img v-if="product.primary_image" :src="`/storage/${product.primary_image}`" class="h-full w-full object-cover"/>
                                <div v-else class="h-full w-full flex items-center justify-center text-gray-400">
                                    <i class="pi pi-image text-2xl"></i>
                                </div>
                            </div>
                            <label :for="`prod-${product.id}`" class="cursor-pointer font-medium text-black dark:text-white line-clamp-1 block">{{ product.name }}</label>
                        </div>
                    </div>
                </div>
                <div v-else class="p-6 bg-gray-50 border border-dashed border-gray-300 rounded-lg text-center">
                     <p class="text-gray-500 mb-4">You don't have any active products available.</p>
                     <Link :href="route('products.create')" class="text-primary hover:underline">Add a product first</Link>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                     <label class="mb-2 block text-sm font-medium text-black dark:text-white">Origin Location <span class="text-red-500">*</span></label>
                     <InputText v-model="acceptForm.origin" class="w-full" placeholder="Ex: Michoacán, Mexico" />
                     <small v-if="acceptForm.errors.origin" class="text-red-500">{{ acceptForm.errors.origin }}</small>
                </div>
                <div>
                     <label class="mb-2 block text-sm font-medium text-black dark:text-white">Destination <span class="text-red-500">*</span></label>
                     <InputText v-model="acceptForm.destination" class="w-full" placeholder="Ex: Texas, USA" />
                     <small v-if="acceptForm.errors.destination" class="text-red-500">{{ acceptForm.errors.destination }}</small>
                </div>
                <div class="sm:col-span-2">
                     <label class="mb-2 block text-sm font-medium text-black dark:text-white">Tentative Shipping Date <span class="text-red-500">*</span></label>
                     <Calendar v-model="acceptForm.tentative_date" dateFormat="yy-mm-dd" :minDate="new Date()" class="w-full" placeholder="Select a date" />
                     <small v-if="acceptForm.errors.tentative_date" class="text-red-500">{{ acceptForm.errors.tentative_date }}</small>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Cancel" icon="pi pi-times" @click="showAcceptDialog = false" text />
            <Button label="Accept Request" icon="pi pi-check" severity="success" @click="confirmAcceptWithForm" :loading="acceptForm.processing" :disabled="acceptForm.products.length === 0" />
        </template>
    </Dialog>

    <TailAdminLayout>
        <div class="mx-auto max-w-270">
            <!-- Breadcrumb and Actions -->
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <h2 class="text-title-md2 font-bold text-black dark:text-white">
                        Request Details
                    </h2>
                    <Tag :value="match.status.toUpperCase()" :severity="getSeverity(match.status)" />
                </div>

                <div class="flex items-center gap-2">
                    <!-- Actions -->
                    <template v-if="match.status === 'pending'">
                        <template v-if="isReceiver">
                            <Button label="Reject" icon="pi pi-times" severity="danger" outlined @click="openRejectDialog" />
                            <Button label="Accept" icon="pi pi-check" severity="success" @click="acceptRequest" />
                        </template>
                        <template v-else>
                            <Button label="Cancel Request" icon="pi pi-ban" severity="secondary" outlined @click="cancelRequest" />
                        </template>
                    </template>
                    
                    <Link :href="route('matches.index')" class="p-button p-button-secondary p-button-outlined p-button-sm no-underline flex items-center justify-center px-4 py-2 font-bold" style="height: 2.5rem;">
                        Back
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-9 sm:grid-cols-2">
                <!-- Producer Info -->
                <div class="flex flex-col gap-9">
                    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                            <h3 class="font-medium text-black dark:text-white">
                                Producer Information
                            </h3>
                        </div>
                        <div class="p-6.5">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="h-16 w-16 rounded-full overflow-hidden border border-stroke dark:border-strokedark">
                                    <img :src="match.producer.company.logo_path ? `/storage/${match.producer.company.logo_path}` : `https://ui-avatars.com/api/?name=${match.producer.company.name}&background=random`" 
                                         class="h-full w-full object-cover" />
                                </div>
                                <div>
                                    <h4 class="text-xl font-semibold text-black dark:text-white">{{ match.producer.company.name }}</h4>
                                    <p class="text-sm">{{ match.producer.company.type }}</p>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="text-sm font-semibold mb-1">About</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ match.producer.company.description || 'No description.' }}</p>
                            </div>

                            <Link :href="route('explorer.show', match.producer.company.id)" class="text-primary hover:underline text-sm">
                                View Full Profile
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="flex flex-col gap-9">
                     <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                            <h3 class="font-medium text-black dark:text-white">
                                Logistics & Message
                            </h3>
                        </div>
                        <div class="p-6.5 flex flex-col gap-4">
                            <div v-if="match.status === 'rejected'" class="mb-4 p-4 rounded-md border border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400">
                                <h4 class="text-sm font-semibold mb-2">Rejection Reason</h4>
                                <p class="text-sm">{{ match.rejection_reason || 'No specific reason provided.' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-black dark:text-white">Origin</label>
                                <p class="text-gray-600 dark:text-gray-400">{{ match.origin }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-black dark:text-white">Destination</label>
                                <p class="text-gray-600 dark:text-gray-400">{{ match.destination }}</p>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-black dark:text-white">Tentative Date</label>
                                <p class="text-gray-600 dark:text-gray-400">{{ formatDate(match.tentative_date) }}</p>
                            </div>
                            <div v-if="match.message">
                                <label class="block text-sm font-medium text-black dark:text-white">Message</label>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded border border-gray-100 dark:border-gray-700 text-sm italic">
                                    "{{ match.message }}"
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products List -->
             <div class="mt-9 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="p-6.5">
                    <!-- Products -->
                    <div v-if="products && products.length > 0">
                        <h3 class="mb-4 text-xl font-bold text-black dark:text-white border-b border-stroke pb-2">Products Included</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <div v-for="product in products" :key="product.id" class="flex gap-4 p-4 border border-stroke rounded-lg">
                                  <div class="h-16 w-16 flex-shrink-0 rounded bg-gray-100 overflow-hidden">
                                       <img :src="product.primary_image ? `/storage/${product.primary_image}` : 'https://placehold.co/100'" 
                                          class="h-full w-full object-cover" />
                                  </div>
                                  <div>
                                       <h4 class="font-bold text-black dark:text-white">{{ product.name }}</h4>
                                       <p class="text-sm font-medium text-primary">{{ formatCurrency(product.price_per_unit, product.currency) }} / {{ product.unit_of_measure }}</p>
                                       <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ product.description }}</p>
                                  </div>
                             </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div v-if="services && services.length > 0" class="mt-6">
                        <h3 class="mb-4 text-xl font-bold text-black dark:text-white border-b border-stroke pb-2">Services Included</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <div v-for="service in services" :key="'show-serv-'+service.id" class="flex gap-4 p-4 border border-stroke rounded-lg">
                                  <div class="h-16 w-16 flex-shrink-0 rounded bg-gray-100 flex items-center justify-center text-gray-400">
                                       <i class="pi pi-truck text-2xl"></i>
                                  </div>
                                  <div>
                                       <h4 class="font-bold text-black dark:text-white">{{ service.name }}</h4>
                                       <p class="text-sm font-medium text-primary">{{ service.price ? `${service.currency} ${service.price}` : 'Upon Request' }}</p>
                                       <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ service.description }}</p>
                                  </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TailAdminLayout>
</template>
