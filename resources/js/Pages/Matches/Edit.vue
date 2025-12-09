<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Calendar from 'primevue/calendar';
import Dropdown from 'primevue/dropdown';
import Toast from 'primevue/toast';

const props = defineProps({
    match: Object,
});

const toast = useToast();
const countries = ref([]);

const form = useForm({
    origin: props.match.origin,
    destination: props.match.destination,
    tentative_date: props.match.tentative_date ? new Date(props.match.tentative_date) : null,
    message: props.match.message,
});

const submit = () => {
    form.put(route('matches.update', props.match.id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Request updated successfully', life: 3000 });
        },
        onError: () => {
             toast.add({ severity: 'error', summary: 'Error', detail: 'Please check the form for errors', life: 3000 });
        }
    });
};

onMounted(async () => {
    try {
        const response = await fetch('https://restcountries.com/v3.1/all?fields=name,cca2');
        const data = await response.json();
        countries.value = data.map(c => ({
            name: c.name.common,
            code: c.cca2
        })).sort((a, b) => a.name.localeCompare(b.name));
    } catch (error) {
        console.error('Failed to fetch countries', error);
         // Fallback could be manually adding common countries or handling error cleanly
    }
});
</script>

<template>
    <Head title="Edit Request" />
    <Toast />

    <TailAdminLayout>
        <div class="mb-6">
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                Edit Connection Request
            </h2>
        </div>

        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
             <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                <h3 class="font-medium text-black dark:text-white">
                    Request Details
                </h3>
            </div>
            
            <form @submit.prevent="submit" class="p-6.5">
                <div class="flex flex-col gap-6 xl:flex-row">
                    <!-- Request Info -->
                    <div class="w-full xl:w-1/2 flex flex-col gap-4">
                         <div class="field">
                            <label class="mb-2 block font-medium">Origin *</label>
                            <Dropdown v-model="form.origin" :options="countries" optionLabel="name" optionValue="name" filter placeholder="Select Origin" class="w-full" :class="{'p-invalid': form.errors.origin}" />
                            <small class="p-error" v-if="form.errors.origin">{{ form.errors.origin }}</small>
                        </div>

                         <div class="field">
                            <label class="mb-2 block font-medium">Destination *</label>
                            <Dropdown v-model="form.destination" :options="countries" optionLabel="name" optionValue="name" filter placeholder="Select Destination" class="w-full" :class="{'p-invalid': form.errors.destination}" />
                            <small class="p-error" v-if="form.errors.destination">{{ form.errors.destination }}</small>
                        </div>

                         <div class="field">
                            <label class="mb-2 block font-medium">Tentative Shipping Date *</label>
                            <Calendar v-model="form.tentative_date" dateFormat="yy-mm-dd" showIcon class="w-full" :class="{'p-invalid': form.errors.tentative_date}" />
                             <small class="p-error" v-if="form.errors.tentative_date">{{ form.errors.tentative_date }}</small>
                        </div>
                    </div>
                    
                    <!-- Products (Read-Only) & Message -->
                    <div class="w-full xl:w-1/2 flex flex-col gap-4">
                        <div class="field">
                            <label class="mb-2 block font-medium">Included Products (Read-only)</label>
                            <div v-if="match.products && match.products.length" class="flex flex-wrap gap-2 mb-4">
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                    {{ match.products.length }} Products Selected
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 italic">To change products, cancel this request and create a new one.</p>
                        </div>

                         <div class="field">
                             <label class="mb-2 block font-medium">Message</label>
                             <Textarea v-model="form.message" rows="5" class="w-full" />
                         </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-8 justify-end">
                     <Link :href="route('matches.index', { sent: true })">
                        <Button label="Cancel" severity="secondary" />
                    </Link>
                    <Button type="submit" label="Save Changes" :loading="form.processing" />
                </div>
            </form>
        </div>
    </TailAdminLayout>
</template>
