<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import InputText from 'primevue/inputtext';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';

defineProps({
    events: Object,
});

const confirm = useConfirm();
const toast = useToast();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    location_name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS },
});

const confirmDelete = (event) => {
    confirm.require({
        message: 'Are you sure you want to delete this event?',
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptProps: {
            label: 'Delete',
            severity: 'danger'
        },
        accept: () => {
             router.delete(route('admin.events.destroy', event.id), {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Confirmed', detail: 'Event deleted successfully', life: 3000 });
                },
                onError: () => {
                    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete event', life: 3000 });
                }
            });
        }
    });
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'active': return 'success';
        case 'inactive': return 'danger';
        default: return 'info';
    }
};
</script>

<template>
    <Head title="Admin Events" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Events Catalog
                </h2>
                <Link :href="route('admin.events.create')">
                    <Button label="New Event" icon="pi pi-plus" />
                </Link>
            </div>
        </template>
        
        <ConfirmDialog />

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
             <div class="p-6">
                <!-- Data Table -->
                <DataTable 
                    :value="events.data" 
                    stripedRows 
                    tableStyle="min-width: 50rem" 
                    v-model:filters="filters"
                    filterDisplay="row"
                    :globalFilterFields="['name', 'location_name', 'status']"
                >
                    <template #header>
                        <div class="flex justify-end">
                            <span class="p-input-icon-left">
                                <i class="pi pi-search" />
                                <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                            </span>
                        </div>
                    </template>
                    <Column field="name" header="Name" sortable :showFilterMenu="false">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter" placeholder="Search Name" />
                        </template>
                    </Column>
                    <Column field="location_name" header="Location" sortable :showFilterMenu="false">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter" placeholder="Search Location" />
                        </template>
                    </Column>
                    <Column field="start_date" header="Start Date" sortable>
                         <template #body="slotProps">
                            {{ new Date(slotProps.data.start_date).toLocaleDateString() }}
                        </template>
                    </Column>
                     <Column field="status" header="Status" sortable :showFilterMenu="false">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.status.toUpperCase()" :severity="getStatusLabel(slotProps.data.status)" />
                        </template>
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter" placeholder="Search Status" />
                        </template>
                    </Column>
                    <Column header="Actions">
                         <template #body="slotProps">
                            <div class="flex gap-2">
                                <Link :href="route('admin.events.edit', slotProps.data.id)">
                                    <Button icon="pi pi-pencil" rounded outlined severity="info" />
                                </Link>
                                <Button icon="pi pi-trash" rounded outlined severity="danger" @click="confirmDelete(slotProps.data)" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
                
                 <!-- Pagination -->
                <div v-if="events.links && events.data.length > 0" class="mt-4 flex justify-center gap-2">
                     <Link v-for="(link, i) in events.links" :key="i"
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
            </div>
        </div>
    </AdminLayout>
</template>
