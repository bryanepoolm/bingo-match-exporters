<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import Chips from 'primevue/chips';
import Toast from 'primevue/toast';

const toast = useToast();

const currencies = [
    { label: 'USD', value: 'USD' },
    { label: 'MXN', value: 'MXN' },
    { label: 'EUR', value: 'EUR' },
];

const statuses = [
    { label: 'Active', value: 'active' },
    { label: 'Draft', value: 'draft' },
    { label: 'Inactive', value: 'inactive' },
];

const form = useForm({
    name: '',
    description: '',
    price: null,
    currency: 'USD',
    weight_limit: '',
    destinations: [],
    status: 'active',
});

const submit = () => {
    form.post(route('services.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Service created successfully', life: 3000 });
        },
        onError: () => {
             toast.add({ severity: 'error', summary: 'Error', detail: 'Please check the form for errors', life: 3000 });
        }
    });
};
</script>

<template>
    <Head title="Create Service" />
    <Toast />

    <TailAdminLayout>
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                New Logistics Service
            </h2>
            <Link :href="route('services.index')">
                <Button label="Back to Services" icon="pi pi-arrow-left" severity="secondary" text />
            </Link>
        </div>

        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
            <form @submit.prevent="submit" class="flex flex-col gap-6">
                
                <div class="field">
                    <label class="mb-2 block font-medium">Service Name *</label>
                    <InputText v-model="form.name" class="w-full" placeholder="e.g., Terrestrial Transport MX to USA" :class="{'p-invalid': form.errors.name}" />
                    <small class="p-error" v-if="form.errors.name">{{ form.errors.name }}</small>
                </div>

                <div class="field">
                    <label class="mb-2 block font-medium">Description *</label>
                    <Textarea v-model="form.description" rows="5" class="w-full" placeholder="Describe what is included in this service package..." :class="{'p-invalid': form.errors.description}" />
                    <small class="p-error" v-if="form.errors.description">{{ form.errors.description }}</small>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="field">
                        <label class="mb-2 block font-medium">Price (Optional)</label>
                         <div class="p-inputgroup">
                            <span class="p-inputgroup-addon p-0 max-w-[100px]">
                                <Dropdown v-model="form.currency" :options="currencies" optionLabel="label" optionValue="value" class="w-full h-full border-none shadow-none" />
                            </span>
                            <InputNumber v-model="form.price" mode="currency" :currency="form.currency" locale="en-US" class="w-full" placeholder="Leave empty if 'Upon Request'" />
                        </div>
                        <small class="p-error" v-if="form.errors.price">{{ form.errors.price }}</small>
                    </div>

                    <div class="field">
                        <label class="mb-2 block font-medium">Capacity / Limit</label>
                        <InputText v-model="form.weight_limit" class="w-full" placeholder="e.g., Up to 20 Tons, or 1 Full Container" />
                        <small class="text-gray-500 block mt-1">Specify maximum weight or volume covered.</small>
                    </div>
                </div>

                <div class="field">
                    <label class="mb-2 block font-medium">Destinations Covered</label>
                    <Chips v-model="form.destinations" separator="," placeholder="Type a destination and press enter (e.g., Texas, EU, Global)" class="w-full" />
                </div>

                <div class="field md:w-1/3">
                    <label class="mb-2 block font-medium">Status</label>
                    <Dropdown v-model="form.status" :options="statuses" optionLabel="label" optionValue="value" class="w-full" />
                </div>

                <div class="flex justify-end mt-4">
                    <Button label="Save Service" type="submit" icon="pi pi-check" :loading="form.processing" />
                </div>
            </form>
        </div>
    </TailAdminLayout>
</template>
