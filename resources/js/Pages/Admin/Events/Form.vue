<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Calendar from 'primevue/calendar';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import FileUpload from 'primevue/fileupload';
import { ref, onMounted } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    event: Object,
});

const isEditing = !!props.event;

const form = useForm({
    name: props.event?.name || '',
    description: props.event?.description || '',
    start_date: props.event?.start_date ? new Date(props.event.start_date) : null,
    end_date: props.event?.end_date ? new Date(props.event.end_date) : null,
    location_name: props.event?.location_name || '',
    address: props.event?.address || '',
    city: props.event?.city || '',
    state: props.event?.state || '',
    country: props.event?.country || '',
    latitude: props.event?.latitude || null,
    longitude: props.event?.longitude || null,
    website_url: props.event?.website_url || '',
    status: props.event?.status || 'active',
    image: null,
});

const statusOptions = [
    { label: 'Active', value: 'active' },
    { label: 'Inactive', value: 'inactive' },
];

const mapContainer = ref(null);
let map = null;
let marker = null;

onMounted(() => {
    // Default to Mexico City if no location
    const defaultLat = 23.6345;
    const defaultLng = -102.5528;
    const zoom = isEditing && form.latitude ? 13 : 5;
    const initialLat = form.latitude || defaultLat;
    const initialLng = form.longitude || defaultLng;

    map = L.map(mapContainer.value).setView([initialLat, initialLng], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Fix marker icon issue in Leaflet with webpack/vite
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
        iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    });

    if (form.latitude && form.longitude) {
        marker = L.marker([form.latitude, form.longitude]).addTo(map);
    }

    map.on('click', (e) => {
        const { lat, lng } = e.latlng;
        form.latitude = lat;
        form.longitude = lng;

        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }
    });
});

const onFileSelect = (event) => {
    form.image = event.files[0];
};

const submit = () => {
    // Need to handle Date objects to string for API if needed, but Inertia/Laravel handles Dates well usually.
    // However, for FormData (file upload), we need special care if using put vs post with files (Laravel method spoofing).
    
    if (isEditing) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.events.update', props.event.id));
    } else {
        form.post(route('admin.events.store'));
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Edit Event' : 'Create Event'" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ isEditing ? 'Edit Event' : 'Create New Event' }}
            </h2>
        </template>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
             <div class="p-6">
                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column: Details -->
                    <div class="space-y-4">
                        <div class="flex flex-col gap-2">
                            <label for="name" class="font-bold">Event Name</label>
                            <InputText id="name" v-model="form.name" :invalid="!!form.errors.name" />
                            <small class="text-red-500" v-if="form.errors.name">{{ form.errors.name }}</small>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="description" class="font-bold">Description</label>
                            <Textarea id="description" v-model="form.description" rows="4" />
                            <small class="text-red-500" v-if="form.errors.description">{{ form.errors.description }}</small>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-2">
                                <label for="start_date" class="font-bold">Start Date</label>
                                <Calendar id="start_date" v-model="form.start_date" :invalid="!!form.errors.start_date" showIcon />
                                <small class="text-red-500" v-if="form.errors.start_date">{{ form.errors.start_date }}</small>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="end_date" class="font-bold">End Date</label>
                                <Calendar id="end_date" v-model="form.end_date" :invalid="!!form.errors.end_date" showIcon />
                                <small class="text-red-500" v-if="form.errors.end_date">{{ form.errors.end_date }}</small>
                            </div>
                        </div>

                         <div class="flex flex-col gap-2">
                            <label for="status" class="font-bold">Status</label>
                            <Dropdown id="status" v-model="form.status" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Select Status" />
                        </div>
                        
                         <div class="flex flex-col gap-2">
                            <label class="font-bold">Event Image</label>
                             <div v-if="event?.image_path" class="mb-2">
                                <img :src="`/storage/${event.image_path}`" alt="Event Image" class="w-32 h-32 object-cover rounded" />
                            </div>
                            <FileUpload mode="basic" name="image" accept="image/*" :maxFileSize="2097152" @select="onFileSelect" :auto="false" chooseLabel="Browse Image" />
                             <small class="text-red-500" v-if="form.errors.image">{{ form.errors.image }}</small>
                        </div>
                    </div>

                    <!-- Right Column: Location & Map -->
                    <div class="space-y-4">
                        <div class="flex flex-col gap-2">
                            <label for="location_name" class="font-bold">Venue / Location Name</label>
                            <InputText id="location_name" v-model="form.location_name" />
                        </div>
                        
                        <div class="flex flex-col gap-2">
                            <label for="address" class="font-bold">Address</label>
                            <InputText id="address" v-model="form.address" />
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <div class="flex flex-col gap-2">
                                <label for="city" class="font-bold">City</label>
                                <InputText id="city" v-model="form.city" />
                            </div>
                             <div class="flex flex-col gap-2">
                                <label for="state" class="font-bold">State</label>
                                <InputText id="state" v-model="form.state" />
                            </div>
                             <div class="flex flex-col gap-2">
                                <label for="country" class="font-bold">Country</label>
                                <InputText id="country" v-model="form.country" />
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-2">
                            <label class="font-bold">Pin Location on Map</label>
                            <div ref="mapContainer" class="h-64 w-full rounded border border-gray-300 z-0"></div>
                            <small class="text-gray-500">Click on the map to set the exact location.</small>
                             <div class="flex gap-4 text-xs text-gray-400">
                                <span>Lat: {{ form.latitude }}</span>
                                <span>Lng: {{ form.longitude }}</span>
                            </div>
                        </div>
                         
                         <div class="flex flex-col gap-2">
                            <label for="website_url" class="font-bold">Website URL</label>
                            <InputText id="website_url" v-model="form.website_url" placeholder="https://..." />
                            <small class="text-red-500" v-if="form.errors.website_url">{{ form.errors.website_url }}</small>
                        </div>
                    </div>
                    
                    <div class="col-span-1 md:col-span-2 flex justify-end gap-2 mt-4">
                        <Link :href="route('admin.events.index')">
                            <Button label="Cancel" severity="secondary" outlined />
                        </Link>
                        <Button type="submit" label="Save Event" :loading="form.processing" />
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Ensure map z-index doesn't overlay dropdowns/modals if needed */
.leaflet-container {
    z-index: 10; 
}
</style>
