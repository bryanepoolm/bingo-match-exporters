<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';

defineProps({
    companies: Array,
});

const confirm = useConfirm();
const toast = useToast();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    type: { value: null, matchMode: FilterMatchMode.EQUALS },
    tax_id: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    'user.email': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const confirmDelete = (company) => {
    confirm.require({
        message: 'Are you sure you want to delete this company? This action will remove all their data and access.',
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger'
        },
        accept: () => {
             router.delete(route('admin.companies.destroy', company.id), {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Confirmed', detail: 'Company deleted successfully', life: 3000 });
                },
                onError: () => {
                    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete company', life: 3000 });
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard - Companies
            </h2>
        </template>
        
        <ConfirmDialog />

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
             <div class="p-6">
                <DataTable 
                    :value="companies" 
                    v-model:filters="filters" 
                    filterDisplay="row" 
                    :globalFilterFields="['name', 'type', 'tax_id', 'user.email']"
                    stripedRows 
                    paginator 
                    :rows="10"
                >
                    <Column field="id" header="ID" sortable></Column>
                    
                    <Column field="name" header="Name" sortable :showFilterMenu="false">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter" placeholder="Search by name" />
                        </template>
                    </Column>

                    <Column field="tax_id" header="Tax ID" sortable :showFilterMenu="false">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter" placeholder="Search by Tax ID" />
                        </template>
                    </Column>

                    <Column field="type" header="Type" sortable :showFilterMenu="false">
                        <template #body="slotProps">
                            <span class="capitalize">{{ slotProps.data.type }}</span>
                        </template>
                         <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter" placeholder="Search by Type" />
                        </template>
                    </Column>
                    <Column field="user.email" header="Owner Email" sortable :showFilterMenu="false">
                         <template #body="slotProps">
                            {{ slotProps.data.user?.email || 'N/A' }}
                        </template>
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter" placeholder="Search Email" />
                        </template>
                    </Column>
                    <Column header="Actions" :exportable="false" style="min-width:8rem">
                        <template #body="slotProps">
                            <Button icon="pi pi-trash" severity="danger" rounded outlined aria-label="Delete" @click="confirmDelete(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AdminLayout>
</template>
