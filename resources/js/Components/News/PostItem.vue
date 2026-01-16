<script setup>
import { useForm, usePage, router, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PostInteractionsModal from '@/Pages/News/PostInteractionsModal.vue';
import Image from 'primevue/image';
import Menu from 'primevue/menu';
import { useConfirm } from 'primevue/useconfirm';

const props = defineProps({
    post: Object,
    currentUserCompany: Object,
});

const form = useForm({ content: '' });
const selectedPost = ref(null);
const isModalVisible = ref(false);

const confirm = useConfirm(); // PrimeVue Confirmation

// Edit Logic
const isEditing = ref(false);
const editForm = useForm({ content: '' });
const menu = ref();

const isOwner = computed(() => {
    return props.currentUserCompany?.id === props.post.company?.id;
});

const menuItems = ref([
    {
        label: 'Edit',
        icon: 'pi pi-pencil',
        command: () => startEdit()
    },
    {
        label: 'Delete',
        icon: 'pi pi-trash',
        class: 'text-red-500',
        command: () => confirmDelete()
    }
]);

const toggleMenu = (event) => {
    menu.value.toggle(event);
};

const startEdit = () => {
    editForm.content = props.post.content;
    isEditing.value = true;
};

const cancelEdit = () => {
    isEditing.value = false;
    editForm.reset();
};

const saveEdit = () => {
    editForm.put(route('news.update', props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        }
    });
};

const confirmDelete = () => {
    confirm.require({
        message: 'Are you sure you want to delete this post?',
        header: 'Delete Post',
        icon: 'pi pi-info-circle',
        rejectLabel: 'Cancel',
        acceptLabel: 'Delete',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('news.destroy', props.post.id), {
                preserveScroll: true,
            });
        }
    });
};

const openPostModal = () => {
    selectedPost.value = props.post;
    isModalVisible.value = true;
};

const toggleLike = () => {
    router.post(route('news.like', props.post.id), {}, {
        preserveScroll: true,
    });
};

const commentForm = useForm({ content: '' });

const submitComment = (postId, content = null, onSuccessCallback = null) => {
    if(content) commentForm.content = content;
    
    commentForm.post(route('news.comment', props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            commentForm.reset();
            if(onSuccessCallback) onSuccessCallback();
        },
    });
};
</script>

<template>
    <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <Link :href="route('explorer.show', post.company.id)" class="group flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200 group-hover:opacity-80 transition-opacity">
                            <img v-if="post.company?.logo_path" :src="`/storage/${post.company.logo_path}`" alt="Company Logo" class="h-full w-full object-cover" />
                            <div v-else class="h-full w-full flex items-center justify-center text-gray-500 font-bold">
                                {{ post.company?.name?.charAt(0) || 'C' }}
                            </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-black dark:text-white group-hover:text-primary transition-colors">{{ post.company?.name }}</h4>
                        <p class="text-sm text-gray-500">{{ new Date(post.created_at).toLocaleDateString() }} at {{ new Date(post.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</p>
                    </div>
                </Link>
            </div>
            
            <div v-if="isOwner">
                <button @click="toggleMenu" class="text-gray-500 hover:text-black dark:hover:text-white p-1 rounded-full hover:bg-gray-100 dark:hover:bg-boxdark-2 transition-colors">
                    <i class="pi pi-ellipsis-h text-xl"></i>
                </button>
                <Menu ref="menu" :model="menuItems" :popup="true" />
            </div>
        </div>
        
        <div class="mb-4">
            <div v-if="isEditing">
                <textarea 
                    v-model="editForm.content" 
                    class="w-full rounded-lg border border-primary bg-transparent py-3 px-4 outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"
                    rows="4"
                ></textarea>
                <div class="flex justify-end gap-2 mt-2">
                    <button @click="cancelEdit" class="px-4 py-1 text-sm font-medium text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white transition-colors">Cancel</button>
                    <button 
                        @click="saveEdit" 
                        class="px-4 py-1 text-sm font-medium text-white bg-primary rounded hover:bg-opacity-90 transition-colors"
                        :disabled="editForm.processing"
                    >
                        Save
                    </button>
                </div>
            </div>
            <p v-else class="text-black dark:text-white whitespace-pre-wrap">{{ post.content }}</p>
        </div>

        <div v-if="post.image_path" class="mb-4 rounded-lg overflow-hidden border border-stroke dark:border-strokedark">
            <Image :src="`/storage/${post.image_path}`" alt="Post Image" preview imageClass="w-full h-auto object-cover max-h-[500px]" class="w-full" />
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-6 border-t border-stroke pt-3 dark:border-strokedark mb-4">
            <button 
                @click="toggleLike" 
                class="flex items-center gap-2 hover:text-primary transition-colors"
                :class="post.is_liked_by_me ? 'text-red-500' : 'text-gray-500'"
            >
                <i :class="post.is_liked_by_me ? 'pi pi-heart-fill' : 'pi pi-heart'" class="text-lg"></i>
                <span class="text-sm font-medium">{{ post.likes_count }} Likes</span>
            </button>
            <button @click="openPostModal" class="flex items-center gap-2 text-gray-500 hover:text-primary transition-colors">
                <i class="pi pi-comments text-lg"></i>
                <span class="text-sm font-medium">{{ post.comments_count }} Comments</span>
            </button>
        </div>

        <!-- Comments Preview -->
        <div class="bg-gray-50 dark:bg-meta-4/30 p-4 rounded-lg">
            <!-- Comment List (Limited to 3) -->
            <div v-if="post.comments && post.comments.length > 0" class="flex flex-col gap-4 mb-4">
                    <div v-for="comment in post.comments.slice(0, 3)" :key="comment.id" class="flex gap-3">
                        <div class="h-8 w-8 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                            <img v-if="comment.company?.logo_path" :src="`/storage/${comment.company.logo_path}`" alt="Logo" class="h-full w-full object-cover" />
                            <div v-else class="h-full w-full flex items-center justify-center text-xs font-bold text-gray-500">
                                {{ comment.company?.name?.charAt(0) || 'C' }}
                            </div>
                        </div>
                        <div class="bg-white dark:bg-boxdark p-3 rounded-lg rounded-tl-none border border-stroke dark:border-strokedark flex-grow">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-medium text-sm text-black dark:text-white">{{ comment.company?.name }}</span>
                                <span class="text-xs text-gray-400">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ comment.content }}</p>
                        </div>
                    </div>
                    
                    <!-- View All Comments Button -->
                    <button 
                    v-if="post.comments.length > 3" 
                    @click="openPostModal"
                    class="text-sm font-medium text-gray-500 hover:text-primary text-left mt-1"
                    >
                        View all {{ post.comments_count }} comments
                    </button>
            </div>

            <!-- Add Comment -->
            <form @submit.prevent="submitComment(post.id, commentForm.content)" class="flex gap-3 items-start">
                    <div class="h-8 w-8 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                        <img v-if="currentUserCompany?.logo_path" :src="`/storage/${currentUserCompany.logo_path}`" alt="My Logo" class="h-full w-full object-cover" />
                        <div v-else class="h-full w-full flex items-center justify-center text-xs font-bold text-gray-500">
                            {{ currentUserCompany?.name?.charAt(0) || 'C' }}
                        </div>
                    </div>
                    <div class="flex-grow relative">
                        <input 
                        v-model="commentForm.content"
                        type="text" 
                        placeholder="Write a comment..." 
                        class="w-full rounded-lg border border-stroke bg-white py-2 pl-4 pr-12 outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:focus:border-primary text-sm"
                        >
                        <button 
                        type="submit" 
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-primary hover:text-primary/80"
                        :disabled="commentForm.processing"
                        >
                            <i class="pi pi-send"></i>
                        </button>
                    </div>
            </form>
        </div>
        
        <PostInteractionsModal 
            v-if="selectedPost"
            :visible="isModalVisible" 
            @update:visible="isModalVisible = $event"
            :post="selectedPost"
            :currentUserCompany="currentUserCompany"
            @submitComment="submitComment"
        />
    </div>
</template>
