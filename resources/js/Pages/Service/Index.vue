<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';

const confirm = useConfirm();

defineProps({
    services: Object,
});

const getSeverity = (status) => {
    switch (status) {
        case 'active': return 'success';
        case 'inactive': return 'secondary';
        case 'draft': return 'warning';
        default: return 'info';
    }
};

const deleteService = (id) => {
    confirm.require({
        message: 'Are you sure you want to delete this service? This action cannot be undone.',
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
             router.delete(route('services.destroy', id));
        }
    });
};
</script>

<template>
    <Head title="My Services" />
    <ConfirmDialog />

    <TailAdminLayout>
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                My Logistics Services
            </h2>
            <Link :href="route('services.create')">
                <Button label="New Service" icon="pi pi-plus" />
            </Link>
        </div>

        <div class="flex flex-col gap-10">
            <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
                <DataTable :value="services.data" :paginator="true" :rows="10" 
                    paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                    :rowsPerPageOptions="[10,20,50]"
                    responsiveLayout="scroll">
                    
                    <Column field="name" header="Name" sortable>
                        <template #body="slotProps">
                            <div class="font-medium text-black dark:text-white">{{ slotProps.data.name }}</div>
                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ slotProps.data.description }}</div>
                        </template>
                    </Column>
                    
                    <Column field="price" header="Price" sortable>
                        <template #body="slotProps">
                            {{ slotProps.data.price ? `${slotProps.data.currency} ${slotProps.data.price}` : 'Upon Request' }}
                        </template>
                    </Column>
                    
                    <Column field="weight_limit" header="Capacity" sortable>
                         <template #body="slotProps">
                            {{ slotProps.data.weight_limit || 'N/A' }}
                        </template>
                    </Column>
                    
                    <Column field="status" header="Status" sortable>
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.status" :severity="getSeverity(slotProps.data.status)" rounded />
                        </template>
                    </Column>
                    
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex gap-2">
                                <Link :href="route('services.edit', slotProps.data.id)">
                                    <Button icon="pi pi-pencil" text rounded aria-label="Edit" />
                                </Link>
                                <Button icon="pi pi-trash" text rounded severity="danger" aria-label="Delete" @click="deleteService(slotProps.data.id)" />
                            </div>
                        </template>
                    </Column>
                    
                    <template #empty>
                        No services found. Start by offering your first logistics package!
                    </template>
                </DataTable>
            </div>
        </div>
    </TailAdminLayout>
</template>
