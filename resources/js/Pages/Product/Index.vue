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
    products: Object,
});

const getSeverity = (status) => {
    switch (status) {
        case 'active': return 'success';
        case 'draft': return 'secondary';
        case 'out_of_stock': return 'danger';
        case 'pending_approval': return 'warning';
        default: return 'info';
    }
};

const deleteProduct = (id) => {
    confirm.require({
        message: 'Are you sure you want to delete this product? This action cannot be undone.',
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
             router.delete(route('products.destroy', id));
        }
    });
};
</script>

<template>
    <Head title="My Products" />
    <ConfirmDialog />

    <TailAdminLayout>
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                My Products
            </h2>
            <Link :href="route('products.create')">
                <Button label="New Product" icon="pi pi-plus" />
            </Link>
        </div>

        <div class="flex flex-col gap-10">
            <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
                <DataTable :value="products.data" :paginator="true" :rows="10" 
                    paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                    :rowsPerPageOptions="[10,20,50]"
                    responsiveLayout="scroll">
                    
                    <Column field="name" header="Name" sortable>
                        <template #body="slotProps">
                            <div class="font-medium text-black dark:text-white">{{ slotProps.data.name }}</div>
                            <div class="text-sm text-gray-500">{{ slotProps.data.hs_code }}</div>
                        </template>
                    </Column>
                    
                    <Column field="category.name" header="Category" sortable></Column>
                    
                    <Column field="price_per_unit" header="Price" sortable>
                        <template #body="slotProps">
                            {{ slotProps.data.currency }} {{ slotProps.data.price_per_unit }} / {{ slotProps.data.unit_of_measure }}
                            <div class="text-xs text-gray-500">{{ slotProps.data.price_term.toUpperCase() }}</div>
                        </template>
                    </Column>
                    
                    <Column field="available_quantity" header="Stock" sortable>
                         <template #body="slotProps">
                            {{ slotProps.data.available_quantity }}
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
                                <Link :href="route('products.edit', slotProps.data.id)">
                                    <Button icon="pi pi-pencil" text rounded aria-label="Edit" />
                                </Link>
                                <Button icon="pi pi-trash" text rounded severity="danger" aria-label="Delete" @click="deleteProduct(slotProps.data.id)" />
                            </div>
                        </template>
                    </Column>
                    
                    <template #empty>
                        No products found. Start by adding one!
                    </template>
                </DataTable>
            </div>
        </div>
    </TailAdminLayout>
</template>
