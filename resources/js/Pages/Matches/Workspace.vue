<script setup>
import { ref, onMounted, computed, nextTick, onUnmounted } from 'vue';
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import TailAdminLayout from '@/Layouts/TailAdminLayout.vue';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Card from 'primevue/card';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import FileUpload from 'primevue/fileupload';
import Image from 'primevue/image';
import axios from 'axios';
import ProductDetailModal from '@/Components/Products/ProductDetailModal.vue';
import Timeline from 'primevue/timeline';
import Dropdown from 'primevue/dropdown';

const props = defineProps({
    match: Object,
    products: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const company = computed(() => user.value?.company);
const isProducer = computed(() => company.value?.type === 'producer' || company.value?.type === 'both');

// Chat Logic
const messages = ref([]);
const newMessage = ref('');
const fileUploadRef = ref(null);
const messagesContainer = ref(null);
const pollingInterval = ref(null);

const fetchMessages = async () => {
    try {
        const response = await axios.get(route('matches.messages.index', props.match.id));
        messages.value = response.data.messages.reverse(); 
        scrollToBottom();
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

const sendMessage = async () => {
    if (!newMessage.value.trim()) return;

    try {
        await axios.post(route('matches.messages.store', props.match.id), {
            content: newMessage.value,
            type: 'text',
        });
        newMessage.value = '';
        await fetchMessages();
    } catch (error) {
        console.error('Error sending message:', error);
    }
};

onMounted(() => {
    fetchMessages();
    pollingInterval.value = setInterval(fetchMessages, 5000);
});

onUnmounted(() => {
    if (pollingInterval.value) clearInterval(pollingInterval.value);
});


// Product Edit Logic (Producer Only)
const showEditModal = ref(false);
const editingProduct = ref(null);

const editForm = useForm({
    name: '',
    description: '',
    price_per_unit: 0,
    currency: 'USD',
});

const openEditProduct = (product) => {
    editingProduct.value = product;
    editForm.name = product.name;
    editForm.description = product.description;
    editForm.price_per_unit = product.price_per_unit;
    editForm.currency = product.currency;
    showEditModal.value = true;
};

const updateProduct = () => {
    editForm.put(route('products.update', editingProduct.value.id), {
        onSuccess: () => {
            showEditModal.value = false;
        }
    });
};

// Product Detail Logic
const showDetailModal = ref(false);
const detailedProduct = ref(null);

const openDetailModal = (product) => {
    detailedProduct.value = product;
    showDetailModal.value = true;
};

// Status / Timeline Logic
const statuses = [
    { label: 'Accepted (Pending)', value: 'accepted' },
    { label: 'In Progress', value: 'in_progress' },
    { label: 'Completed', value: 'completed' },
    { label: 'Cancelled', value: 'cancelled' },
];

const currentStatus = ref(props.match.status);
const statusNotes = ref('');
const showStatusDialog = ref(false);
const pendingStatus = ref(null);

const confirmStatusChange = (newVal) => {
    if (newVal === currentStatus.value) return; // Change: prevent confirm if same
    pendingStatus.value = newVal;
    statusNotes.value = ''; // reset
    showStatusDialog.value = true;
};

const saveStatusChange = () => {
    router.post(route('matches.status.update', props.match.id), {
        status: pendingStatus.value,
        notes: statusNotes.value
    }, {
        onSuccess: () => {
            showStatusDialog.value = false;
            currentStatus.value = pendingStatus.value;
        }
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const formatCurrency = (amount, currency) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency }).format(amount);
};

</script>

<template>
    <Head title="Workspace" />

    <TailAdminLayout>
        <div class="h-[calc(100vh-8rem)] flex flex-col">
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 pt-4">
                <div>
                    <h2 class="text-2xl font-bold text-black dark:text-white">Workspace</h2>
                     <p class="text-sm text-gray-500">
                        With: {{ isProducer ? match.exporter.company.name : match.producer.company.name }}
                     </p>
                </div>
                 
                 <!-- Status Dropdown -->
                 <div class="flex items-center gap-2">
                     <span class="text-sm font-medium text-gray-500 hidden sm:block">Deal Status:</span>
                     <Dropdown :modelValue="currentStatus" :options="statuses" optionLabel="label" optionValue="value" 
                         @update:modelValue="confirmStatusChange"
                         :class="{'p-inputtext-sm': true}" 
                         placeholder="Select Status">
                         <template #value="slotProps">
                             <Tag :value="statuses.find(s => s.value === slotProps.value)?.label || slotProps.value" 
                                  :severity="slotProps.value === 'completed' ? 'success' : (slotProps.value === 'cancelled' ? 'danger' : 'info')" />
                         </template>
                     </Dropdown>
                 </div>
            </div>

            <div class="flex-1 overflow-hidden px-6 pb-6">
                <!-- Use a full height card -->
                <div class="bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-lg shadow-default h-full flex flex-col">
                    <TabView class="h-full flex flex-col" :pt="{ panelContainer: 'flex-1 overflow-hidden relative' }">
                        <!-- CHAT TAB -->
                        <TabPanel header="Chat">
                             <!-- ... (Chat Content) ... -->
                             <div class="flex flex-col h-full absolute inset-0 p-4">
                                <!-- Messages Area -->
                                <div ref="messagesContainer" class="flex-1 overflow-y-auto space-y-4 pr-2 mb-4">
                                    <div v-for="msg in messages" :key="msg.id" 
                                         :class="['flex w-full', msg.sender_id === user.id ? 'justify-end' : 'justify-start']">
                                        <div :class="['max-w-[70%] rounded-lg p-3', 
                                            msg.sender_id === user.id ? 'bg-blue-600 text-white rounded-br-none' : 'bg-gray-100 dark:bg-gray-700 text-black dark:text-white rounded-bl-none']">
                                            <p>{{ msg.content }}</p>
                                            <span class="text-xs opacity-70 block text-right mt-1">{{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</span>
                                        </div>
                                    </div>
                                    <div v-if="messages.length === 0" class="text-center text-gray-400 mt-10">
                                        No messages yet. Start the conversation!
                                    </div>
                                </div>
                                
                                <!-- Input Area -->
                                <div class="flex gap-2">
                                    <InputText v-model="newMessage" placeholder="Type a message..." class="flex-1" @keyup.enter="sendMessage" />
                                    <Button icon="pi pi-send" @click="sendMessage" />
                                </div>
                            </div>
                        </TabPanel>

                        <!-- INFO TAB -->
                        <TabPanel header="Information">
                             <!-- ... (Info Content) ... -->
                             <div class="h-full overflow-y-auto p-4">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <!-- Logistics Card -->
                                    <div class="col-span-1 border border-stroke dark:border-strokedark rounded-lg p-4">
                                        <h3 class="font-bold mb-4 text-black dark:text-white">Logistics Details</h3>
                                        <div class="space-y-3 text-sm">
                                            <div>
                                                <span class="block text-gray-500">Origin</span>
                                                <span class="font-medium dark:text-gray-300">{{ match.origin }}</span>
                                            </div>
                                            <div>
                                                <span class="block text-gray-500">Destination</span>
                                                <span class="font-medium dark:text-gray-300">{{ match.destination }}</span>
                                            </div>
                                             <div>
                                                <span class="block text-gray-500">Tentative Date</span>
                                                <span class="font-medium dark:text-gray-300">{{ formatDate(match.tentative_date) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Products List -->
                                    <div class="col-span-1 lg:col-span-2 space-y-4">
                                        <h3 class="font-bold text-black dark:text-white">Products Included</h3>
                                        <div v-for="product in products" :key="product.id" class="border border-stroke dark:border-strokedark rounded-lg p-4 flex gap-4 bg-gray-50 dark:bg-meta-4">
                                             <div class="w-24 h-24 rounded overflow-hidden flex-shrink-0 bg-white">
                                                 <img :src="product.primary_image ? `/storage/${product.primary_image}` : 'https://placehold.co/100'" class="w-full h-full object-cover" />
                                             </div>
                                             <div class="flex-1">
                                                 <div class="flex justify-between items-start">
                                                     <div>
                                                         <h4 class="font-bold text-lg dark:text-white">{{ product.name }}</h4>
                                                         <p class="text-sm text-primary">{{ formatCurrency(product.price_per_unit, product.currency) }} / {{ product.unit_of_measure }}</p>
                                                     </div>
                                                     <div class="flex gap-2">
                                                         <!-- View Details Button -->
                                                         <Button icon="pi pi-eye" size="small" severity="secondary" outlined @click="openDetailModal(product)" v-tooltip="'View Full Details'" />
                                                         
                                                         <!-- Edit Button (Producer Only) -->
                                                         <div v-if="isProducer">
                                                             <Button icon="pi pi-pencil" size="small" outlined @click="openEditProduct(product)" label="Edit" />
                                                         </div>
                                                     </div>
                                                 </div>
                                                 <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ product.description }}</p>
                                                 
                                                  <!-- Documents -->
                                                  <div v-if="product.documents && product.documents.length > 0" class="mt-3">
                                                      <h5 class="text-xs font-semibold text-gray-500 mb-2">Attached Documents:</h5>
                                                      <div class="flex flex-wrap gap-2">
                                                          <a v-for="(doc, idx) in product.documents" :key="idx" 
                                                             :href="`/storage/${doc.path}`" 
                                                             target="_blank"
                                                             class="no-underline">
                                                              <Tag :value="doc.original_name || 'Document'" icon="pi pi-file" severity="info" class="text-xs cursor-pointer hover:bg-blue-100 transition-colors" />
                                                          </a>
                                                      </div>
                                                  </div>
                                                  <div v-else class="mt-3 text-xs text-gray-400 italic">No attached documents.</div>
                                             </div>
                                        </div>
                                    </div>
                                </div>
                             </div>
                        </TabPanel>

                         <!-- TIMELINE TAB -->
                         <TabPanel header="Timeline">
                             <div class="h-full overflow-y-auto p-6">
                                 <Timeline :value="match.timelines" align="alternate" class="customized-timeline">
                                     <template #marker="slotProps">
                                         <span class="flex w-8 h-8 items-center justify-center text-white rounded-full shadow-sm" 
                                             :class="{
                                                 'bg-green-500': slotProps.item.new_status === 'completed',
                                                 'bg-red-500': slotProps.item.new_status === 'cancelled',
                                                 'bg-blue-500': slotProps.item.new_status === 'in_progress',
                                                 'bg-gray-400': slotProps.item.new_status === 'accepted' || slotProps.item.new_status === 'pending'
                                             }">
                                             <i :class="{
                                                 'pi pi-check': slotProps.item.new_status === 'completed',
                                                 'pi pi-times': slotProps.item.new_status === 'cancelled',
                                                 'pi pi-cog': slotProps.item.new_status === 'in_progress',
                                                 'pi pi-file': slotProps.item.new_status === 'accepted'
                                             }"></i>
                                         </span>
                                     </template>
                                     <template #content="slotProps">
                                         <Card class="mt-3">
                                             <template #title>
                                                 {{ statuses.find(s => s.value === slotProps.item.new_status)?.label || slotProps.item.new_status }}
                                             </template>
                                             <template #subtitle>
                                                 {{ new Date(slotProps.item.created_at).toLocaleString() }}
                                                 <br/>
                                                 <span class="text-xs">by {{ slotProps.item.user?.name || 'System' }} ({{ slotProps.item.user?.company?.name }})</span>
                                             </template>
                                             <template #content>
                                                 <p v-if="slotProps.item.notes" class="text-gray-600 dark:text-gray-300 italic">
                                                     "{{ slotProps.item.notes }}"
                                                 </p>
                                                 <p v-else class="text-xs text-gray-400">No notes provided.</p>
                                             </template>
                                         </Card>
                                     </template>
                                 </Timeline>
                                 
                                 <div v-if="(!match.timelines || match.timelines.length === 0)" class="text-center text-gray-500 mt-10">
                                     No timeline history available.
                                 </div>
                             </div>
                         </TabPanel>
                    </TabView>
                </div>
            </div>
        </div>
        
        <!-- Status Confirmation Dialog -->
        <Dialog v-model:visible="showStatusDialog" modal header="Confirm Status Change" :style="{ width: '400px' }">
             <div class="flex flex-col gap-4">
                 <p>Are you sure you want to change the status to <strong>{{ statuses.find(s => s.value === pendingStatus)?.label }}</strong>?</p>
                 <div class="flex flex-col gap-2">
                     <label class="text-sm font-medium">Add a note (optional)</label>
                     <Textarea v-model="statusNotes" rows="3" placeholder="e.g. Shipment dispatched..." />
                 </div>
             </div>
             <template #footer>
                 <Button label="Cancel" icon="pi pi-times" @click="showStatusDialog = false" text severity="secondary" />
                 <Button label="Confirm Update" icon="pi pi-check" @click="saveStatusChange" severity="primary" />
             </template>
        </Dialog>
        
        <!-- Detailed Product Modal -->
        <ProductDetailModal v-model:visible="showDetailModal" :product="detailedProduct" />
        
        <!-- Edit Product Dialog -->
        <Dialog v-model:visible="showEditModal" modal header="Edit Product Details" :style="{ width: '50vw' }">
             <div class="grid gap-4 py-4">
                  <div class="flex flex-col gap-2">
                      <label>Name</label>
                      <InputText v-model="editForm.name" />
                  </div>
                   <div class="flex flex-col gap-2">
                      <label>Price</label>
                      <div class="p-inputgroup">
                           <span class="p-inputgroup-addon">$</span>
                           <InputText v-model="editForm.price_per_unit" type="number" step="0.01" />
                           <span class="p-inputgroup-addon">{{ editForm.currency }}</span>
                      </div>
                  </div>
                  <div class="flex flex-col gap-2">
                      <label>Description</label>
                      <Textarea v-model="editForm.description" rows="3" />
                  </div>
             </div>
             <template #footer>
                 <Button label="Cancel" icon="pi pi-times" @click="showEditModal = false" text />
                 <Button label="Save Changes" icon="pi pi-check" @click="updateProduct" :loading="editForm.processing" />
             </template>
        </Dialog>
    </TailAdminLayout>
</template>

<style scoped>
/* Ensure TabView takes full height */
:deep(.p-tabview-panels) {
    height: 100%;
    padding: 0;
}
:deep(.p-tabview) {
    display: flex;
    flex-direction: column;
}
</style>
