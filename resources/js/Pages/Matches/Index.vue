<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import { useConfirm } from "primevue/useconfirm";
import ConfirmDialog from 'primevue/confirmdialog';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';

import InputSwitch from 'primevue/inputswitch';
import { ref, watch } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';

const props = defineProps({
    requests: Object,
    type: String, // 'received' (default) or 'sent'
    archived: Boolean,
});

const showArchived = ref(props.archived);

const toggleArchived = () => {
    router.get(route('matches.index'), { 
        sent: props.type === 'sent', 
        archived: showArchived.value 
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const confirm = useConfirm();
const toast = useToast();

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const deleteRequest = (id) => {
    confirm.require({
        message: 'Are you sure you want to archive/cancel this request?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
            router.delete(route('matches.destroy', id), {
                onSuccess: () => {
                   toast.add({ severity: 'success', summary: 'Confirmed', detail: 'Request archived', life: 3000 });
                },
                onError: () => {
                    toast.add({ severity: 'error', summary: 'Error', detail: 'Could not archive request', life: 3000 });
                }
            });
        }
    });
};

const getSeverity = (status) => {
    switch (status) {
        case 'pending': return 'warning';
        case 'accepted': return 'success';
        case 'rejected': return 'danger';
        default: return 'info';
    }
};
</script>

<template>
    <Head title="Connection Requests" />
    <ConfirmDialog />

    <TailAdminLayout>
        <div class="mx-auto max-w-270">
            <!-- Breadcrumb -->
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <h2 class="text-title-md2 font-bold text-black dark:text-white">
                        {{ type === 'sent' ? 'My Sent Requests' : 'Incoming Requests' }}
                    </h2>
                     <div class="flex items-center gap-2">
                        <InputSwitch v-model="showArchived" @change="toggleArchived" />
                        <span class="text-sm">Show Archived</span>
                    </div>
                </div>
                <nav>
                    <ol class="flex items-center gap-2">
                        <li><Link class="font-medium" :href="route('dashboard')">Dashboard /</Link></li>
                        <li class="font-medium text-primary">Requests</li>
                    </ol>
                </nav>
            </div>

            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <DataTable :value="requests.data" stripedRows tableStyle="min-width: 50rem" v-if="requests.data.length > 0">
                    <Column :header="type === 'sent' ? 'Exporter' : 'Producer'">
                        <template #body="slotProps">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                <div class="h-12.5 w-15 rounded-md">
                                    <img v-if="type === 'sent'" :src="slotProps.data.exporter?.company?.logo_path ? `/storage/${slotProps.data.exporter.company.logo_path}` : `https://ui-avatars.com/api/?name=${slotProps.data.exporter?.company?.name}&background=random`" alt="Logo" class="h-full w-full object-cover rounded" />
                                    <img v-else :src="slotProps.data.producer?.company?.logo_path ? `/storage/${slotProps.data.producer.company.logo_path}` : `https://ui-avatars.com/api/?name=${slotProps.data.producer?.company?.name}&background=random`" alt="Logo" class="h-full w-full object-cover rounded" />
                                </div>
                                <p class="text-sm font-medium text-black dark:text-white">
                                    {{ type === 'sent' ? slotProps.data.exporter?.company?.name : slotProps.data.producer?.company?.name }}
                                    <span v-if="type !== 'sent' && !slotProps.data.is_read" class="ml-2 inline-flex items-center justify-center rounded-full bg-primary py-0.5 px-2 text-xs font-medium text-white">New</span>
                                </p>
                            </div>
                        </template>
                    </Column>
                    <Column header="Date">
                        <template #body="slotProps">
                            <p class="text-black dark:text-white">{{ formatDate(slotProps.data.created_at) }}</p>
                        </template>
                    </Column>
                    <Column header="Status">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.status.toUpperCase()" :severity="getSeverity(slotProps.data.status)" />
                        </template>
                    </Column>
                    <Column header="Actions">
                         <template #body="slotProps">
                            <div class="flex items-center space-x-3.5">
                                <Link :href="route('matches.show', slotProps.data.id)" class="hover:text-primary">
                                    <i class="pi pi-eye"></i>
                                </Link>
                                
                                <template v-if="type === 'sent' && (slotProps.data.status === 'pending' || slotProps.data.status === 'rejected')">
                                    <Link v-if="slotProps.data.status === 'pending'" :href="route('matches.edit', slotProps.data.id)" class="hover:text-primary">
                                        <i class="pi pi-pencil"></i>
                                    </Link>
                                    <button @click="deleteRequest(slotProps.data.id)" class="hover:text-danger">
                                        <i class="pi pi-trash"></i>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </Column>
                </DataTable>

            <!-- Pagination -->
            <div v-if="requests.links && requests.data.length > 0" class="p-4 flex justify-center gap-2">
                 <Link v-for="(link, i) in requests.links" :key="i"
                    :href="link.url || '#'"
                    class="px-3 py-1 rounded"
                    :class="{
                        'bg-primary text-white': link.active,
                        'text-gray-500 hover:bg-gray-100': !link.active,
                        'opacity-50 pointer-events-none': !link.url
                    }"
                    v-html="link.label"
                />
            </div>

            <div v-if="requests.data.length === 0" class="p-6 text-center text-gray-500">
                No requests found.
            </div>
            </div>
        </div>
    </TailAdminLayout>
</template>
