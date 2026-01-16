<script setup>
import { ref, computed } from 'vue';
import Dialog from 'primevue/dialog';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    visible: Boolean,
    post: Object,
    currentUserCompany: Object,
});

const emit = defineEmits(['update:visible', 'submitComment']);

const activeTab = ref('comments'); // 'comments' or 'likes'

const commentForm = useForm({
    content: ''
});

const submitComment = () => {
    emit('submitComment', props.post.id, commentForm.content, () => {
        commentForm.reset();
    });
};

const close = () => {
    emit('update:visible', false);
};
</script>

<template>
    <Dialog 
        :visible="visible" 
        @update:visible="$emit('update:visible', $event)" 
        modal 
        :header="`${post?.company?.name}'s Post`" 
        :style="{ width: '50vw' }" 
        :breakpoints="{ '960px': '75vw', '641px': '100vw' }"
        :dismissableMask="true"
    >
        <div class="flex flex-col h-[60vh]">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 dark:border-gray-700 mb-4">
                <button 
                    @click="activeTab = 'comments'"
                    class="flex-1 py-3 text-sm font-medium transition-colors relative"
                    :class="activeTab === 'comments' ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                >
                    Comments ({{ post?.comments_count || 0 }})
                </button>
                <button 
                    @click="activeTab = 'likes'"
                    class="flex-1 py-3 text-sm font-medium transition-colors relative"
                    :class="activeTab === 'likes' ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                >
                    Likes ({{ post?.likes_count || 0 }})
                </button>
            </div>

            <!-- Content Area -->
            <div class="flex-grow overflow-y-auto pr-2 custom-scrollbar">
                
                <!-- Comments Tab -->
                <div v-if="activeTab === 'comments'" class="flex flex-col gap-4">
                    <div v-if="post?.comments?.length === 0" class="text-center text-gray-500 py-10">
                        No comments yet. Be the first to share your thoughts!
                    </div>
                    <div v-for="comment in post?.comments" :key="comment.id" class="flex gap-3">
                         <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                             <img v-if="comment.company?.logo_path" :src="`/storage/${comment.company.logo_path}`" alt="Logo" class="h-full w-full object-cover" />
                             <div v-else class="h-full w-full flex items-center justify-center text-xs font-bold text-gray-500">
                                 {{ comment.company?.name?.charAt(0) || 'C' }}
                             </div>
                         </div>
                         <div class="bg-gray-50 dark:bg-boxdark p-3 rounded-lg flex-grow border border-gray-100 dark:border-gray-700">
                             <div class="flex items-center justify-between mb-1">
                                 <span class="font-medium text-sm text-black dark:text-white">{{ comment.company?.name }}</span>
                                 <span class="text-xs text-gray-400">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                             </div>
                             <p class="text-sm text-gray-600 dark:text-gray-300">{{ comment.content }}</p>
                         </div>
                    </div>
                </div>

                <!-- Likes Tab -->
                <div v-if="activeTab === 'likes'" class="flex flex-col gap-3">
                    <div v-if="post?.likes?.length === 0" class="text-center text-gray-500 py-10">
                        No likes yet.
                    </div>
                    <div v-for="like in post?.likes" :key="like.id">
                        <Link 
                            :href="route('explorer.show', like.company.id)"
                            class="flex items-center gap-3 p-2 hover:bg-gray-50 dark:hover:bg-meta-4 rounded-lg transition-colors group"
                        >
                             <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                                 <img v-if="like.company?.logo_path" :src="`/storage/${like.company.logo_path}`" alt="Logo" class="h-full w-full object-cover" />
                                 <div v-else class="h-full w-full flex items-center justify-center text-xs font-bold text-gray-500">
                                     {{ like.company?.name?.charAt(0) || 'C' }}
                                 </div>
                             </div>
                             <div>
                                 <h4 class="font-medium text-sm text-black dark:text-white group-hover:text-primary transition-colors">{{ like.company?.name }}</h4>
                                 <p class="text-xs text-gray-500">Liked on {{ new Date(like.created_at).toLocaleDateString() }}</p>
                             </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Footer Comment Input (Only for Comments Tab) -->
            <div v-if="activeTab === 'comments'" class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                <form @submit.prevent="submitComment" class="flex gap-3 items-center">
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
                            :disabled="commentForm.processing || !commentForm.content.trim()"
                         >
                             <i class="pi pi-send"></i>
                         </button>
                     </div>
                </form>
            </div>
        </div>
    </Dialog>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 20px;
}
</style>
