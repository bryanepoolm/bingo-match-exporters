<script setup>
import { useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import SelectButton from 'primevue/selectbutton';
import { Head } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    tax_id: '',
    website: '',
    description: '',
    type: 'producer',
});

const types = [
    { name: 'Producer', value: 'producer' },
    { name: 'Exporter', value: 'exporter' },
];

const submit = () => {
    form.post(route('company.store'));
};
</script>

<template>
    <Head title="Complete Profile" />
    <div class="flex items-center justify-center min-h-screen bg-surface-50 dark:bg-surface-900 p-4">
        <div class="w-full max-w-2xl p-8 bg-white dark:bg-surface-800 rounded-xl shadow-lg border border-surface-200 dark:border-surface-700">
            <h2 class="text-2xl font-bold mb-2 text-surface-900 dark:text-surface-0">Complete Company Profile</h2>
            <p class="text-surface-600 dark:text-surface-400 mb-6">Please provide your company details to continue.</p>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="name" class="text-sm font-medium text-surface-700 dark:text-surface-200">Company Name</label>
                        <InputText id="name" v-model="form.name" :invalid="!!form.errors.name" class="w-full" />
                        <small class="text-red-500" v-if="form.errors.name">{{ form.errors.name }}</small>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="tax_id" class="text-sm font-medium text-surface-700 dark:text-surface-200">Tax ID / RFC</label>
                        <InputText id="tax_id" v-model="form.tax_id" :invalid="!!form.errors.tax_id" class="w-full" />
                        <small class="text-red-500" v-if="form.errors.tax_id">{{ form.errors.tax_id }}</small>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="website" class="text-sm font-medium text-surface-700 dark:text-surface-200">Website (Optional)</label>
                    <InputText id="website" v-model="form.website" :invalid="!!form.errors.website" class="w-full" placeholder="https://example.com" />
                    <small class="text-red-500" v-if="form.errors.website">{{ form.errors.website }}</small>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="type" class="text-sm font-medium text-surface-700 dark:text-surface-200">Company Type</label>
                    <SelectButton v-model="form.type" :options="types" optionLabel="name" optionValue="value" :invalid="!!form.errors.type" />
                    <small class="text-red-500" v-if="form.errors.type">{{ form.errors.type }}</small>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="description" class="text-sm font-medium text-surface-700 dark:text-surface-200">Description</label>
                    <Textarea id="description" v-model="form.description" rows="4" class="w-full" />
                </div>

                <div class="flex justify-end pt-4">
                    <Button type="submit" label="Save & Continue" icon="pi pi-arrow-right" iconPos="right" :loading="form.processing" />
                </div>
            </form>
        </div>
    </div>
</template>
