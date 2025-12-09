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
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    match: Object,
    products: Array,
});

const confirm = useConfirm();
const toast = useToast();

const rejectionReason = ref('');
const showRejectDialog = ref(false);
const form = useForm({
    rejection_reason: '',
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

const acceptRequest = () => {
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

</script>

<template>
    <Head :title="`Request from ${match.producer.company.name}`" />
    <ConfirmDialog />
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
                    <!-- Actions for Exporter (who received the request) -->
                     <!-- We assume if we are viewing this and status is pending, we can act. 
                          Ideally check if auth user is the target, but for visual demo we assume 'pending' is enough trigger for now if we don't have user prop. -->
                    <template v-if="match.status === 'pending'">
                        <Button label="Reject" icon="pi pi-times" severity="danger" outlined @click="openRejectDialog" />
                        <Button label="Accept" icon="pi pi-check" severity="success" @click="acceptRequest" />
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
                <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        Proposed Products
                    </h3>
                </div>
                <div class="p-6.5">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="product in products" :key="product.id" class="border border-stroke dark:border-strokedark rounded-lg p-4 flex flex-col gap-3">
                            <div class="h-40 bg-gray-100 dark:bg-gray-800 rounded-md overflow-hidden">
                                <img :src="product.primary_image ? `/storage/${product.primary_image}` : 'https://placehold.co/300x200'" 
                                     class="h-full w-full object-cover" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-black dark:text-white">{{ product.name }}</h4>
                                <p class="text-sm text-primary font-medium">{{ formatCurrency(product.price_per_unit, product.currency) }} / {{ product.unit_of_measure }}</p>
                            </div>
                            <Link :href="route('products.show', product.id)" class="mt-auto text-center w-full rounded border border-primary py-2 px-4 text-primary hover:bg-primary hover:text-white transition-colors">
                                View Product Details
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TailAdminLayout>
</template>
