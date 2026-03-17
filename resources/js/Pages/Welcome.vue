<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { ref, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';
import debounce from 'lodash/debounce';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const showPlanes = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
const showDropdown = ref(false);
const searchContainer = ref(null);

const handleScroll = () => {
    if (window.scrollY > 50) {
        showPlanes.value = true;
    }
};

const performSearch = debounce(async (query) => {
    if (!query || query.length < 2) {
        searchResults.value = [];
        isSearching.value = false;
        return;
    }
    
    try {
        isSearching.value = true;
        const response = await axios.get(route('search.companies', { q: query }));
        searchResults.value = response.data;
        showDropdown.value = true;
    } catch (e) {
        console.error('Search failed', e);
    } finally {
        isSearching.value = false;
    }
}, 300);

const handleInput = () => {
    if (!searchQuery.value) {
        searchResults.value = [];
        showDropdown.value = false;
    } else {
        performSearch(searchQuery.value);
    }
};

const handleEnter = () => {
    if (searchQuery.value && searchQuery.value.length >= 2) {
        performSearch.flush();
    }
};

const handleClickOutside = (event) => {
    if (searchContainer.value && !searchContainer.value.contains(event.target)) {
        showDropdown.value = false;
    }
};

const handleInputFocus = () => {
    if (searchQuery.value.length >= 2) {
        showDropdown.value = true;
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <Head title="Bingo Match Exporter" />
    
    <div class="min-h-screen relative overflow-hidden text-white bg-slate-900 selection:bg-brand-500 selection:text-white">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Animated Gradient Blobs -->
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-brand-600/20 blur-[120px] animate-pulse-slow"></div>
            <div class="absolute top-[40%] -right-[10%] w-[40%] h-[40%] rounded-full bg-blue-600/20 blur-[100px] animate-pulse-slow delay-1000"></div>
            <div class="absolute -bottom-[10%] left-[20%] w-[30%] h-[30%] rounded-full bg-purple-600/20 blur-[80px] animate-pulse-slow delay-2000"></div>
            
            <!-- Grid Pattern -->
            <div class="absolute inset-0 bg-[url('/images/grid-pattern.svg')] opacity-[0.03]"></div>

            <!-- Flying Planes Animation -->
            <div v-if="showPlanes">
                <div v-for="n in 15" :key="n" 
                     class="absolute animate-fly-across"
                     :style="{
                        top: `${Math.random() * 100}%`,
                        left: '-10%',
                        animationDuration: `${15 + Math.random() * 20}s`,
                        animationDelay: `${Math.random() * 10}s`,
                        width: `${50 + Math.random() * 100}px`
                     }">
                     <img src="/images/avion-sin-fondo.png" alt="Plane" class="w-full h-auto opacity-30 transform -rotate-12" />
                </div>
            </div>
        </div>

        <!-- Navbar -->
        <nav class="fixed top-0 z-50 w-full transition-all duration-300 border-b backdrop-blur-md bg-white/5 border-white/10">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <div class="flex items-center flex-shrink-0 gap-3">
                        <div class="flex items-center justify-center w-10 h-10 shadow-lg rounded-xl bg-gradient-to-br from-brand-400 to-blue-600 shadow-brand-500/20">
                             <img src="/images/logo/bingo-logo-fondo-transparente.png" alt="Logo" class="object-contain w-8 h-auto" />
                        </div>
                        <span class="hidden text-xl font-bold tracking-tight text-white sm:block">Bingo Match Exporter</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <Link :href="route('login')">
                            <Button label="Iniciar Sesión" text class="!text-white hover:!bg-white/10 !px-6" />
                        </Link>
                        <Link :href="route('register')">
                            <Button label="Registrarse" class="!bg-brand-600 hover:!bg-brand-500 !border-brand-600 !px-6 !rounded-full shadow-lg shadow-brand-500/25" />
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 z-20">
            <div class="relative z-20 px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Logo Animation -->
                <div class="flex justify-center mb-12 animate-float">
                    <div class="relative group">
                        <div class="absolute inset-0 transition-all duration-500 rounded-full bg-brand-500/30 blur-3xl group-hover:bg-brand-500/40"></div>
                        <img 
                            src="/images/logo/bingo-logo-fondo-transparente.png" 
                            alt="Bingo Match Exporter Logo" 
                            class="relative h-auto transition-transform duration-500 w-64 md:w-80 drop-shadow-2xl hover:scale-105"
                        />
                    </div>
                </div>

                <h1 class="mb-8 text-5xl font-bold tracking-tight md:text-7xl animate-fade-in-up">
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-200 via-white to-brand-200">
                        Conectando Fronteras
                    </span>
                    <span class="block mt-2 text-4xl font-medium md:text-6xl text-slate-300">
                        Negocios sin Límites
                    </span>
                </h1>
                
                <p class="max-w-2xl mx-auto mt-6 text-xl leading-relaxed text-slate-400 animate-fade-in-up" style="animation-delay: 0.2s;">
                    La plataforma inteligente que une a <strong>Empresas Productoras</strong> con <strong>Importadores Globales</strong>. 
                    Encuentra proveedores y productos de forma rápida.
                </p>

                <!-- Ultimate Search Bar -->
                <div class="relative max-w-2xl mx-auto mt-12 animate-fade-in-up" style="animation-delay: 0.4s;" ref="searchContainer">
                    <div class="relative flex items-center w-full transition-all duration-300 group">
                        <div class="absolute inset-0 transition-opacity duration-300 rounded-full opacity-50 bg-gradient-to-r from-brand-500 to-blue-500 blur group-hover:opacity-100"></div>
                        <div class="relative flex items-center w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-full p-2 shadow-2xl focus-within:ring-2 focus-within:ring-brand-400/50 transition-all duration-300">
                            <i class="pi pi-search text-slate-300 ml-4 text-xl"></i>
                            <input 
                                v-model="searchQuery"
                                @focus="handleInputFocus"
                                @input="handleInput"
                                @keydown.enter="handleEnter"
                                type="text" 
                                placeholder="Busca empresas o etiquetas (ej. cítricos, aguacates)..." 
                                class="w-full px-4 py-3 text-lg text-white bg-transparent border-none outline-none placeholder:text-slate-400 focus:ring-0"
                            />
                            <div v-show="isSearching" class="mr-4">
                                <i class="pi pi-spinner pi-spin text-brand-400 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dropdown Results -->
                    <transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="transform scale-95 opacity-0"
                        enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0"
                    >
                        <div v-if="showDropdown && (searchResults.length > 0 || (searchQuery.length >= 2 && !isSearching))" 
                             class="absolute left-0 w-full mt-2 overflow-hidden border shadow-2xl bg-slate-800/95 backdrop-blur-2xl rounded-2xl border-white/10 z-50">
                            
                            <ul v-if="searchResults.length > 0" class="divide-y divide-white/5 max-h-96 overflow-y-auto custom-scrollbar">
                                <li v-for="company in searchResults" :key="company.id" class="transition-colors hover:bg-white/5">
                                    <Link :href="route('login')" class="flex items-start p-4 gap-4">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <div v-if="company.logo_path" class="w-12 h-12 rounded-xl bg-white/10 overflow-hidden border border-white/10">
                                                <img :src="'/storage/' + company.logo_path" alt="Logo" class="w-full h-full object-cover" />
                                            </div>
                                            <div v-else class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-600 to-blue-600 flex items-center justify-center border border-white/10">
                                                <span class="text-xl font-bold text-white">{{ company.name.substring(0, 1).toUpperCase() }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Details -->
                                        <div class="flex-1 min-w-0 text-left">
                                            <h4 class="text-white font-semibold flex items-center gap-2">
                                                {{ company.name }}
                                                <i class="pi pi-verified text-blue-400 text-sm" title="Verificada"></i>
                                            </h4>
                                            <p class="text-sm text-slate-400 truncate mt-1">
                                                {{ company.description ? company.description.substring(0, 80) + '...' : 'Empresa registrada en Bingo Match Exporter' }}
                                            </p>
                                            
                                            <!-- Matched Tags -->
                                            <div v-if="company.products && company.products.length > 0" class="flex flex-wrap gap-2 mt-2">
                                                <span v-for="product in company.products" :key="product.id" class="px-2 py-1 text-xs rounded-md bg-brand-500/20 text-brand-300 border border-brand-500/30">
                                                    {{ product.name }}
                                                </span>
                                            </div>
                                        </div>
                                    </Link>
                                </li>
                            </ul>
                            
                            <div v-else-if="!isSearching" class="p-8 text-center">
                                <i class="pi pi-search text-4xl text-slate-500 mb-3"></i>
                                <p class="text-slate-400">No encontramos resultados para "<span class="text-white font-medium">{{ searchQuery }}</span>"</p>
                                <p class="text-sm text-slate-500 mt-2">Intenta buscar empresas, productos o descripciones diferentes.</p>
                            </div>
                            
                            <div class="p-3 text-center bg-black/20 border-t border-white/5">
                                <p class="text-xs text-slate-500">
                                    Inicia sesión para ver los detalles completos y contactar a las empresas.
                                </p>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </main>

        <!-- Features/Advantages Section -->
        <section class="py-24 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="group relative p-8 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition-all duration-300 hover:-translate-y-2 animate-fade-in-up" style="animation-delay: 0.6s;">
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-500/10 to-transparent rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="w-14 h-14 rounded-2xl bg-brand-500/20 flex items-center justify-center mb-6 text-brand-400 group-hover:scale-110 transition-transform duration-300">
                            <i class="pi pi-globe text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Alcance Global</h3>
                        <p class="text-slate-400 leading-relaxed">
                            Rompe las barreras geográficas. Conectamos productores locales con compradores internacionales verificados en busca de tus productos.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group relative p-8 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition-all duration-300 hover:-translate-y-2 animate-fade-in-up" style="animation-delay: 0.8s;">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="w-14 h-14 rounded-2xl bg-blue-500/20 flex items-center justify-center mb-6 text-blue-400 group-hover:scale-110 transition-transform duration-300">
                            <i class="pi pi-verified text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Empresas Verificadas</h3>
                        <p class="text-slate-400 leading-relaxed">
                            Seguridad ante todo. Operamos con un estricto proceso de verificación para garantizar transacciones seguras y relaciones comerciales confiables.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group relative p-8 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition-all duration-300 hover:-translate-y-2 animate-fade-in-up" style="animation-delay: 1.0s;">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-transparent rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="w-14 h-14 rounded-2xl bg-purple-500/20 flex items-center justify-center mb-6 text-purple-400 group-hover:scale-110 transition-transform duration-300">
                            <i class="pi pi-comments text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Comunicación Directa</h3>
                        <p class="text-slate-400 leading-relaxed">
                            Sin intermediarios innecesarios. Nuestra plataforma facilita el contacto directo y la negociación transparente entre las partes interesadas.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 relative z-10 border-t border-white/5 bg-slate-900/50 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-slate-500 text-sm">
                    © 2025 Bingo Match Exporter. Todos los derechos reservados.
                    <span class="mx-2">•</span>
                    <Link :href="route('privacy-policy')" class="hover:text-white transition-colors">Aviso de Privacidad</Link>
                </div>
                <div class="flex gap-6">
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="pi pi-twitter text-xl"></i></a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="pi pi-linkedin text-xl"></i></a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="pi pi-instagram text-xl"></i></a>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes fly-across {
    0% {
        transform: translateX(-100%) translateY(0) rotate(5deg);
        opacity: 0;
    }
    10% {
        opacity: 0.6;
    }
    90% {
        opacity: 0.6;
    }
    100% {
        transform: translateX(110vw) translateY(-20vh) rotate(5deg);
        opacity: 0;
    }
}

.animate-fly-across {
    animation-name: fly-across;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
}
</style>
