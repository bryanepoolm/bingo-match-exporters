<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Button from 'primevue/button';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Bingo Match Exporter" />
    
    <div class="min-h-screen bg-slate-900 text-white overflow-hidden relative selection:bg-brand-500 selection:text-white">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Animated Gradient Blobs -->
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-brand-600/20 blur-[120px] animate-pulse-slow"></div>
            <div class="absolute top-[40%] -right-[10%] w-[40%] h-[40%] rounded-full bg-blue-600/20 blur-[100px] animate-pulse-slow delay-1000"></div>
            <div class="absolute -bottom-[10%] left-[20%] w-[30%] h-[30%] rounded-full bg-purple-600/20 blur-[80px] animate-pulse-slow delay-2000"></div>
            
            <!-- Grid Pattern -->
            <div class="absolute inset-0 bg-[url('/images/grid-pattern.svg')] opacity-[0.03]"></div>
        </div>

        <!-- Navbar -->
        <nav class="fixed top-0 w-full z-50 transition-all duration-300 backdrop-blur-md bg-white/5 border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <div class="flex-shrink-0 flex items-center gap-3">
                         <!-- Small logo for navbar if needed commonly, but user asked for specific logo in hero. Using text/icon here for now or scaled down logo -->
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-blue-600 flex items-center justify-center shadow-lg shadow-brand-500/20">
                             <img src="/images/logo/bingo-logo-fondo-transparente.png" alt="Logo" class="w-8 h-auto object-contain" />
                        </div>
                        <span class="font-bold text-xl tracking-tight text-white hidden sm:block">Bingo Match Exporter</span>
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
        <main class="relative pt-32 pb-20 lg:pt-48 lg:pb-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                
                <!-- Logo Animation -->
                <div class="mb-12 flex justify-center animate-float">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-brand-500/30 blur-3xl rounded-full group-hover:bg-brand-500/40 transition-all duration-500"></div>
                        <img 
                            src="/images/logo/bingo-logo-fondo-transparente.png" 
                            alt="Bingo Match Exporter Logo" 
                            class="relative w-64 md:w-80 h-auto drop-shadow-2xl transition-transform duration-500 hover:scale-105"
                        />
                    </div>
                </div>

                <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-8 animate-fade-in-up">
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-200 via-white to-brand-200">
                        Conectando Fronteras
                    </span>
                    <span class="block text-4xl md:text-6xl mt-2 text-slate-300 font-medium">
                        Negocios sin Límites
                    </span>
                </h1>
                
                <p class="mt-6 max-w-2xl mx-auto text-xl text-slate-400 leading-relaxed animate-fade-in-up" style="animation-delay: 0.2s;">
                    La plataforma inteligente que une a <strong>Empresas Productoras</strong> con <strong>Importadores Globales</strong>. 
                    Simplificamos la exportación y abrimos puertas a nuevos mercados.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up" style="animation-delay: 0.4s;">
                    <Link :href="route('register')">
                        <Button label="Comenzar Ahora" icon="pi pi-rocket" size="large" class="!px-8 !py-4 !text-lg !rounded-full !bg-brand-600 !border-brand-600 hover:!bg-brand-500 !text-white shadow-xl shadow-brand-500/25 transition-transform hover:-translate-y-1" />
                    </Link>
                    <Link :href="route('login')">
                        <Button label="Explorar Oportunidades" size="large" severity="secondary" outlined class="!px-10 !py-4 !text-lg !rounded-full !text-white !border-white/20 hover:!bg-white/10 hover:!border-white/40 backdrop-blur-sm" />
                    </Link>
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
/* Any specific non-tailwind overrides if strictly necessary, but sticking to Tailwind utility classes mostly */
</style>
