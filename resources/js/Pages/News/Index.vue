<script setup>
import { useForm, usePage, Head } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import PostItem from '@/Components/News/PostItem.vue';
import { ref } from 'vue';

const props = defineProps({ posts: Object });
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
        <div class="mx-auto max-w-2xl">
            <!-- Create Post -->
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
                <!-- ... existing create post form ... -->
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
    </TailAdminLayout>
</template>

