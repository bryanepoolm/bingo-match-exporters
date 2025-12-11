<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
</script>

<template>
    <Head title="Aviso de Privacidad - Bingo Match" />
    
    <div class="min-h-screen bg-slate-900 text-slate-300 relative selection:bg-brand-500 selection:text-white font-sans">
        <!-- Background Elements (Same as Welcome.vue for consistency) -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none fixed">
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-brand-600/10 blur-[120px]"></div>
            <div class="absolute top-[40%] -right-[10%] w-[40%] h-[40%] rounded-full bg-blue-600/10 blur-[100px]"></div>
        </div>

        <!-- Navbar -->
        <nav class="fixed top-0 w-full z-50 transition-all duration-300 backdrop-blur-md bg-slate-900/80 border-b border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <Link :href="route('welcome')" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-blue-600 flex items-center justify-center shadow-lg shadow-brand-500/20">
                             <img src="/images/logo/bingo-logo-fondo-transparente.png" alt="Logo" class="w-8 h-auto object-contain" />
                        </div>
                        <span class="font-bold text-xl tracking-tight text-white hidden sm:block">Bingo Match Exporter</span>
                    </Link>
                    <Link :href="route('welcome')">
                        <Button label="Volver" icon="pi pi-arrow-left" text class="!text-white hover:!bg-white/10" />
                    </Link>
                </div>
            </div>
        </nav>

        <main class="relative pt-32 pb-20 px-4 sm:px-6 lg:px-8 z-10">
            <div class="max-w-4xl mx-auto bg-slate-800/50 backdrop-blur-sm rounded-3xl p-8 md:p-12 border border-white/5 shadow-2xl">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-8 border-b border-white/10 pb-6">Aviso de Privacidad y Términos de Responsabilidad</h1>
                
                <div class="space-y-8 text-lg leading-relaxed">
                    <section>
                        <h2 class="text-2xl font-semibold text-white mb-4">1. Introducción</h2>
                        <p>
                            Bienvenido a <strong>Bingo Match Exporter</strong>. Este documento describe cómo manejamos su información y delimita nuestras responsabilidades como plataforma de conexión entre empresas productoras y empresas importadoras.
                        </p>
                    </section>

                    <section class="p-6 bg-red-500/10 border border-red-500/20 rounded-2xl">
                        <h2 class="text-2xl font-bold text-red-400 mb-4 flex items-center gap-2">
                            <i class="pi pi-exclamation-triangle"></i> 2. Limitación de Responsabilidad
                        </h2>
                        <p class="mb-4 text-white">
                            <strong>Bingo Match Exporter actúa exclusivamente como un facilitador de contacto</strong> entre usuarios (Productores e Importadores/Compradores).
                        </p>
                        <ul class="list-disc pl-5 space-y-2 marker:text-red-400">
                            <li>
                                <strong>Acuerdos entre Partes:</strong> Bingo Match Exporter <strong>NO</strong> es parte de ninguna transacción, contrato, o acuerdo comercial que se celebre entre los usuarios de la plataforma. Cualquier acuerdo al que lleguen los clientes al interactuar dentro de la plataforma es responsabilidad exclusiva de las partes involucradas.
                            </li>
                            <li>
                                <strong>Calidad y Cumplimiento:</strong> No garantizamos la calidad, seguridad, legalidad o existencia de los productos anunciados, ni la capacidad de los usuarios para vender o comprar dichos productos.
                            </li>
                            <li>
                                <strong>Conflictos:</strong> La plataforma se exime de toda responsabilidad ante cualquier disputa, reclamo, pérdida, daño o perjuicio derivado de las interacciones o transacciones entre usuarios.
                            </li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-white mb-4">3. Responsabilidad del Usuario</h2>
                        <p>
                            Los usuarios reconocen y aceptan que son los únicos responsables de realizar la debida diligencia ("due diligence") antes de concretar cualquier negocio. Se recomienda encarecidamente verificar la autenticidad y solvencia de la contraparte antes de realizar envíos de mercancía o pagos.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-white mb-4">4. Uso Indebido de la Plataforma</h2>
                        <p>
                            Bingo Match Exporter no se hace responsable por el uso indebido que los usuarios puedan dar a la información obtenida a través de la plataforma. Cualquier actividad fraudulenta o ilícita detectada resultará en la suspensión inmediata de la cuenta, sin perjuicio de las acciones legales que correspondan.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-white mb-4">5. Protección de Datos</h2>
                        <p>
                            Su información personal y empresarial es recopilada con el único fin de facilitar la conexión comercial. Implementamos medidas de seguridad para proteger sus datos, sin embargo, no nos hacemos responsables por brechas de seguridad ajenas a nuestro control directo.
                        </p>
                    </section>
                </div>

                <div class="mt-12 pt-8 border-t border-white/5 text-center text-slate-500 text-sm">
                    Última actualización: Diciembre 2025
                </div>
            </div>
        </main>

        <footer class="py-12 relative z-10 border-t border-white/5 bg-slate-900/50 backdrop-blur-xl">
             <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-slate-500">© 2025 Bingo Match Exporter. Todos los derechos reservados.</p>
             </div>
        </footer>
    </div>
</template>
