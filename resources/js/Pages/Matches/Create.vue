<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import Dropdown from 'primevue/dropdown';
import { ref, computed, onMounted } from 'vue';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Calendar from 'primevue/calendar';
import Checkbox from 'primevue/checkbox';
import Message from 'primevue/message';

const props = defineProps({
    targetCompany: Object,
    myProducts: Array,
});

const currentStep = ref(1);
const countries = ref([]);
const form = useForm({
    products: [],
    origin: null,
    destination: null,
    tentative_date: null,
    message: '',
});

onMounted(async () => {
    try {
        const response = await fetch('https://restcountries.com/v3.1/all?fields=name');
        const data = await response.json();
        countries.value = data
            .map(country => country.name.common)
            .sort((a, b) => a.localeCompare(b));
    } catch (error) {
        console.error('Failed to fetch countries:', error);
    }
});

const nextStep = () => {
    if (currentStep.value < 3) currentStep.value++;
};

const prevStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};

const submit = () => {
    form.post(route('matches.store', props.targetCompany.id));
};

const isStep1Valid = computed(() => form.products.length > 0);
const isStep2Valid = computed(() => form.origin && form.destination && form.tentative_date);
</script>

<template>
    <Head title="Connect Request" />

    <TailAdminLayout>
        <div class="mx-auto max-w-270">
            <!-- Breadcrumb -->
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-title-md2 font-bold text-black dark:text-white">
                    Connect with {{ targetCompany.name }}
                </h2>
                <nav>
                    <ol class="flex items-center gap-2">
                        <li><Link class="font-medium" :href="route('dashboard')">Dashboard /</Link></li>
                        <li><Link class="font-medium" :href="route('explorer.index')">Explorer /</Link></li>
                        <li class="font-medium text-primary">Connect</li>
                    </ol>
                </nav>
            </div>

            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-full border border-stroke dark:border-strokedark overflow-hidden">
                             <img :src="targetCompany.logo_path ? `/storage/${targetCompany.logo_path}` : `https://ui-avatars.com/api/?name=${targetCompany.name}&background=random&size=128`" 
                                     alt="Company Logo" 
                                     class="h-full w-full object-cover" />
                        </div>
                        <div>
                            <h3 class="font-medium text-black dark:text-white">
                                {{ targetCompany.name }}
                            </h3>
                            <p class="text-sm">Initiating connection request</p>
                        </div>
                    </div>
                </div>

                <div class="p-6.5">
                    <!-- Stepper Header -->
                    <div class="mb-8 flex justify-between items-center relative">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-200 dark:bg-gray-700 -z-1"></div>
                        
                        <div v-for="step in 3" :key="step" class="relative z-10 flex flex-col items-center gap-2">
                             <div :class="[
                                'w-10 h-10 rounded-full flex items-center justify-center font-bold text-white transition-colors duration-300',
                                currentStep >= step ? 'bg-primary' : 'bg-gray-300 dark:bg-gray-600'
                            ]">
                                {{ step }}
                            </div>
                            <span class="text-xs font-medium" :class="currentStep >= step ? 'text-primary' : 'text-gray-500'">
                                {{ step === 1 ? 'Products' : step === 2 ? 'Logistics' : 'Message' }}
                            </span>
                        </div>
                    </div>

                    <form @submit.prevent="submit">
                        
                        <!-- Step 1: Products -->
                        <div v-if="currentStep === 1">
                            <h4 class="mb-4 text-xl font-semibold text-black dark:text-white">Select Products to Offer</h4>
                            <p class="mb-6 text-sm text-gray-500">Choose which of your products you want to propose to this exporter.</p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                <div v-for="product in myProducts" :key="product.id" 
                                    :class="[
                                        'cursor-pointer rounded-lg border p-4 transition-all hover:shadow-md flex items-start gap-4',
                                        form.products.includes(product.id) ? 'border-primary bg-primary/5' : 'border-stroke dark:border-strokedark'
                                    ]"
                                    @click="() => {
                                        if(form.products.includes(product.id)) {
                                            form.products = form.products.filter(id => id !== product.id);
                                        } else {
                                            form.products.push(product.id);
                                        }
                                    }">
                                    
                                    <div class="flex-shrink-0">
                                         <Checkbox v-model="form.products" :value="product.id" :inputId="`prod-${product.id}`" @click.stop />
                                    </div>
                                    <div class="flex-grow">
                                        <div class="h-12 w-12 rounded border border-stroke dark:border-strokedark overflow-hidden mb-2">
                                            <img :src="product.primary_image ? `/storage/${product.primary_image}` : 'https://placehold.co/100'" 
                                                 class="h-full w-full object-cover" />
                                        </div>
                                        <label :for="`prod-${product.id}`" class="font-medium text-black dark:text-white cursor-pointer select-none">
                                            {{ product.name }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                             <div v-if="myProducts.length === 0" class="text-center py-4 bg-gray-50 dark:bg-gray-800 rounded">
                                <p>You don't have any visible products listed.</p>
                                <Link :href="route('products.create')" class="text-primary hover:underline">Create a product first</Link>
                            </div>
                            <Message v-if="form.errors.products" severity="error" :closable="false">{{ form.errors.products }}</Message>
                        </div>

                        <!-- Step 2: Logistics -->
                        <div v-if="currentStep === 2">
                            <h4 class="mb-4 text-xl font-semibold text-black dark:text-white">Logistics Details</h4>
                            <div class="flex flex-col gap-6">
                                <div>
                                    <label class="mb-2.5 block text-black dark:text-white">Origin Location</label>
                                    <Dropdown 
                                        v-model="form.origin" 
                                        :options="countries" 
                                        filter 
                                        placeholder="Select Origin Country" 
                                        class="w-full" 
                                        :class="{'p-invalid': form.errors.origin}" 
                                    />
                                    <small class="text-red-500" v-if="form.errors.origin">{{ form.errors.origin }}</small>
                                </div>
                                <div>
                                    <label class="mb-2.5 block text-black dark:text-white">Preferred Destination</label>
                                    <Dropdown 
                                        v-model="form.destination" 
                                        :options="countries" 
                                        filter 
                                        placeholder="Select Destination Country" 
                                        class="w-full" 
                                        :class="{'p-invalid': form.errors.destination}" 
                                    />
                                    <small class="text-red-500" v-if="form.errors.destination">{{ form.errors.destination }}</small>
                                </div>
                                <div>
                                    <label class="mb-2.5 block text-black dark:text-white">Tentative Shipment Date</label>
                                    <Calendar v-model="form.tentative_date" showIcon placeholder="Select Date" class="w-full" :class="{'p-invalid': form.errors.tentative_date}" />
                                    <small class="text-red-500" v-if="form.errors.tentative_date">{{ form.errors.tentative_date }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Message -->
                        <div v-if="currentStep === 3">
                            <h4 class="mb-4 text-xl font-semibold text-black dark:text-white">Additional Message</h4>
                            <div>
                                <label class="mb-2.5 block text-black dark:text-white">Message (Optional)</label>
                                <Textarea v-model="form.message" rows="6" placeholder="Any specific requirements, questions, or introduction..." class="w-full" />
                                <small class="text-red-500" v-if="form.errors.message">{{ form.errors.message }}</small>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="mt-8 flex justify-between">
                            <button 
                                type="button" 
                                @click="prevStep" 
                                v-if="currentStep > 1"
                                class="flex justify-center rounded border border-stroke bg-gray p-3 font-medium text-black hover:bg-opacity-90 dark:border-strokedark dark:bg-meta-4 dark:text-white"
                            >
                                Back
                            </button>
                            <div v-else></div> <!-- Spacer -->

                            <button 
                                type="button" 
                                @click="nextStep" 
                                v-if="currentStep < 3"
                                :disabled="currentStep === 1 ? !isStep1Valid : !isStep2Valid"
                                class="flex justify-center rounded bg-primary p-3 font-medium text-gray hover:bg-opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Next
                            </button>

                            <button 
                                v-if="currentStep === 3"
                                type="submit"
                                :disabled="form.processing"
                                class="flex justify-center rounded bg-primary p-3 font-medium text-gray hover:bg-opacity-90 disabled:opacity-50"
                            >
                                <i class="pi pi-check mr-2" v-if="!form.processing"></i>
                                {{ form.processing ? 'Sending...' : 'Send Request' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </TailAdminLayout>
</template>
