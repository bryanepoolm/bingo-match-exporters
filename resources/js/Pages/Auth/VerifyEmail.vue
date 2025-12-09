<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Email Verification" />

    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-screen overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 mb-4">
                            <i class="pi pi-envelope text-3xl"></i>
                        </div>
                        <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Verify Email</div>
                        <p class="text-surface-600 dark:text-surface-200 font-medium max-w-md mx-auto">
                            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
                        </p>
                    </div>

                    <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400 text-center">
                        A new verification link has been sent to the email address you provided during registration.
                    </div>

                    <form @submit.prevent="submit">
                        <div class="flex flex-col gap-4">
                            <Button type="submit" label="Resend Verification Email" :loading="form.processing" class="w-full" />
                            
                            <div class="flex justify-center items-center mt-4">
                                <Link :href="route('logout')" method="post" as="button" class="text-sm text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-surface-0 underline">
                                    Log Out
                                </Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
