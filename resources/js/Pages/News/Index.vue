<script setup>
import { useForm, usePage, Head } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import PostItem from '@/Components/News/PostItem.vue';
import { ref } from 'vue';

import Carousel from 'primevue/carousel';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';

const props = defineProps({ 
    posts: Object,
    events: Array
});

const selectedEvent = ref(null);
const displayEventDialog = ref(false);

const openEventDetails = (event) => {
    selectedEvent.value = event;
    displayEventDialog.value = true;
};

const formatAddress = (event) => {
    const parts = [event.address, event.city, event.state, event.country].filter(Boolean);
    return parts.join(', ') || 'Online / Unknown';
};

const openGoogleMaps = (event) => {
    let url = '';
    if (event.latitude && event.longitude) {
        url = `https://www.google.com/maps/search/?api=1&query=${event.latitude},${event.longitude}`;
    } else {
        const query = encodeURIComponent(formatAddress(event));
        url = `https://www.google.com/maps/search/?api=1&query=${query}`;
    }
    window.open(url, '_blank');
};
const user = usePage().props.auth.user;
const company = user?.company;

const form = useForm({
    content: '',
    image: null,
});

const submit = () => {
    form.post(route('news.store'), {
        onSuccess: () => {
            form.reset();
            const fileInput = document.getElementById('image-upload');
            if (fileInput) fileInput.value = '';
        },
    });
};

const handleImageUpload = (e) => {
    form.image = e.target.files[0];
};

</script>

<template>
    <Head title="News" />
    <TailAdminLayout>
        <div class="mx-auto max-w-7xl">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Feed -->
                <div class="lg:col-span-2">
                    <!-- Create Post -->
                    <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
                        <!-- ... (existing create post form content) ... -->
                         <div class="flex gap-4">
                             <div class="h-12 w-12 rounded-full overflow-hidden flex-shrink-0 bg-gray-200">
                                 <img v-if="company?.logo_path" :src="`/storage/${company.logo_path}`" alt="Company Logo" class="h-full w-full object-cover" />
                                 <div v-else class="h-full w-full flex items-center justify-center text-gray-500 font-bold text-xl">
                                     {{ company?.name?.charAt(0) || 'C' }}
                                 </div>
                             </div>
                             <div class="flex-grow">
                                 <form @submit.prevent="submit">
                                    <textarea 
                                        v-model="form.content" 
                                        class="w-full rounded-lg border border-stroke bg-transparent py-4 pl-6 pr-10 outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary resize-none"
                                        rows="3"
                                        placeholder="What's happening in your company?"
                                    ></textarea>
                                    <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
                                    
                                    <div class="mt-4 flex items-center justify-between border-t border-stroke pt-4 dark:border-strokedark">
                                        <div class="flex items-center gap-2">
                                            <label for="image-upload" class="cursor-pointer flex items-center gap-2 text-primary hover:text-primary/80 transition-colors">
                                                <i class="pi pi-image text-xl"></i>
                                                <span class="text-sm font-medium">Photo</span>
                                            </label>
                                            <input type="file" id="image-upload" @change="handleImageUpload" accept="image/*" class="hidden" />
                                            <span v-if="form.image" class="text-sm text-gray-500">{{ form.image.name }}</span>
                                            <div v-if="form.errors.image" class="text-red-500 text-sm">{{ form.errors.image }}</div>
                                        </div>
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-6 text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-10"
                                            :disabled="form.processing"
                                        >
                                            Post
                                        </button>
                                    </div>
                                 </form>
                             </div>
                        </div>
                    </div>

                    <!-- Feed -->
                    <div class="flex flex-col gap-6">
                        <PostItem 
                            v-for="post in posts.data" 
                            :key="post.id" 
                            :post="post" 
                            :currentUserCompany="company"
                        />
                    </div>
                </div>

                <!-- Right Sidebar: Events -->
                <div class="hidden lg:block text-black dark:text-white">
                    <div v-if="props.events && props.events.length > 0" class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark sticky top-24">
                        <div class="p-4 border-b border-stroke dark:border-strokedark">
                            <h3 class="font-bold text-lg">Upcoming Events</h3>
                        </div>
                        <div class="p-4">
                            <Carousel :value="props.events" :numVisible="1" :numScroll="1" circular :autoplayInterval="4000">
                                <template #item="slotProps">
                                    <div class="relative rounded-lg overflow-hidden h-80 m-2 shadow-lg group">
                                        <!-- Image Background -->
                                        <img v-if="slotProps.data.image_path" :src="`/storage/${slotProps.data.image_path}`" :alt="slotProps.data.name" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                        <div v-else class="absolute inset-0 bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center">
                                            <i class="pi pi-image text-white text-6xl opacity-50"></i>
                                        </div>

                                        <!-- Gradient Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                                        <!-- Status Tag -->
                                        <div class="absolute top-4 left-4">
                                            <!-- <Tag :value="slotProps.data.status" severity="success" /> -->
                                        </div>

                                        <!-- Content Overlay -->
                                        <div class="absolute bottom-0 left-0 w-full p-6 text-white">
                                            <h4 class="text-xl font-bold mb-2 leading-tight">{{ slotProps.data.name }}</h4>
                                            
                                            <div class="flex flex-col gap-2 text-sm text-gray-200 mb-4">
                                                 <div class="flex items-center gap-2">
                                                    <i class="pi pi-calendar text-primary"></i>
                                                    <span class="font-medium">{{ new Date(slotProps.data.start_date).toLocaleDateString(undefined, { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' }) }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <i class="pi pi-map-marker text-primary"></i>
                                                    <span class="truncate">{{ slotProps.data.location_name || slotProps.data.city }}</span>
                                                </div>
                                            </div>
                                            
                                             <!-- Action Button -->
                                             <Button label="View Details" size="small" class="w-full bg-white/20 hover:bg-white/30 text-white border-white/40 glass-button" @click="openEventDetails(slotProps.data)" />
                                        </div>
                                    </div>
                                </template>
                            </Carousel>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:visible="displayEventDialog" modal :style="{ width: '80vw', maxWidth: '1200px' }" :breakpoints="{ '960px': '90vw' }" contentClass="p-0" :showHeader="false">
            <div v-if="selectedEvent" class="flex flex-col md:flex-row h-[80vh] overflow-hidden bg-white dark:bg-gray-900 rounded-lg">
                <!-- Left: Image (Hero) -->
                <div class="w-full md:w-2/3 bg-black flex items-center justify-center relative group">
                     <img v-if="selectedEvent.image_path" :src="`/storage/${selectedEvent.image_path}`" :alt="selectedEvent.name" class="w-full h-full object-contain" />
                     <div v-else class="w-full h-full flex items-center justify-center text-gray-500">
                         <i class="pi pi-image text-6xl opacity-50"></i>
                     </div>
                     
                     <!-- Close Button for Mobile (overlay) -->
                     <button @click="displayEventDialog = false" class="md:hidden absolute top-4 right-4 bg-black/50 text-white p-2 rounded-full hover:bg-black/70">
                        <i class="pi pi-times"></i>
                     </button>
                </div>
                
                <!-- Right: Information -->
                <div class="w-full md:w-1/3 flex flex-col h-full border-l border-gray-200 dark:border-gray-700">
                    <!-- Header -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-start">
                        <div>
                             <h3 class="text-2xl font-bold text-black dark:text-white leading-tight mb-1">{{ selectedEvent.name }}</h3>
                             <p class="text-sm text-gray-500 flex items-center gap-1">
                                <i class="pi pi-map-marker text-primary text-xs"></i>
                                {{ selectedEvent.location_name }}
                             </p>
                        </div>
                        <button @click="displayEventDialog = false" class="hidden md:block text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <i class="pi pi-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Scrollable Content -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        <!-- Status & Date -->
                        <div class="flex items-center justify-between">
                            <!-- <Tag :value="selectedEvent.status" severity="success" /> -->
                            <div class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="pi pi-calendar text-primary"></i>
                                <span>
                                    {{ new Date(selectedEvent.start_date).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) }}
                                    <span v-if="selectedEvent.end_date"> - {{ new Date(selectedEvent.end_date).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) }}</span>
                                    {{ new Date(selectedEvent.start_date).getFullYear() }}
                                </span>
                            </div>
                        </div>

                        <!-- Location Detail -->
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg text-sm">
                             <div class="font-bold mb-1 text-gray-900 dark:text-white">Location</div>
                             <div class="text-gray-600 dark:text-gray-400">{{ formatAddress(selectedEvent) }}</div>
                        </div>

                        <!-- Description -->
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white mb-2 text-sm uppercase tracking-wide">About</h4>
                            <p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap leading-relaxed text-sm">{{ selectedEvent.description }}</p>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex flex-col gap-3">
                        <Button label="Open in Maps" icon="pi pi-map" severity="info" class="w-full" @click="openGoogleMaps(selectedEvent)" />
                        <a v-if="selectedEvent.website_url" :href="selectedEvent.website_url" target="_blank" rel="noopener noreferrer" class="block w-full">
                             <Button label="Visit Website" icon="pi pi-external-link" severity="secondary" outlined class="w-full" />
                        </a>
                    </div>
                </div>
            </div>
        </Dialog>
    </TailAdminLayout>
</template>

