<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';

defineProps({
    products: Object,
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'company.name': { value: null, matchMode: FilterMatchMode.CONTAINS },
    price: { value: null, matchMode: FilterMatchMode.EQUALS },
});
</script>

<template>
    <Head title="Admin Products" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Products
            </h2>
        </template>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <DataTable 
                    :value="products.data" 
                    stripedRows 
                    tableStyle="min-width: 50rem"
                    v-model:filters="filters"
                    filterDisplay="row"
                    :globalFilterFields="['name', 'company.name', 'price']"
                >
                    <Column field="id" header="ID" sortable></Column>
                    
                    <Column field="name" header="Name" sortable :showFilterMenu="false">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter" placeholder="Search Name" />
                        </template>
                    </Column>
                    
                    <Column field="company.name" header="Company" sortable :showFilterMenu="false">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter" placeholder="Search Company" />
                        </template>
                    </Column>
                    
                    <Column field="price" header="Price" sortable :showFilterMenu="false">
                        <template #body="slotProps">
                             ${{ slotProps.data.price }}
                        </template>
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter" placeholder="Search Price" />
                        </template>
                    </Column>
                    
                    <Column field="created_at" header="Created At" sortable>
                         <template #body="slotProps">
                            {{ new Date(slotProps.data.created_at).toLocaleDateString() }}
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AdminLayout>
</template>
