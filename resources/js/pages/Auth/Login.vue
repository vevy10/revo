<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type { AxiosError } from 'axios';
import { ref } from 'vue';
import { useAuth } from '@/composables/useAuth';

const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);
const auth = useAuth();

function messageErreur(message?: string) {
    if (!message) {
        return 'Connexion impossible. Vérifiez vos informations puis réessayez.';
    }

    if (message === 'Invalid credentials.') {
        return 'Identifiants invalides ou compte non activé.';
    }

    return message;
}

async function submit() {
    error.value = '';
    loading.value = true;

    try {
        await auth.login(email.value, password.value);
        window.location.href = '/dashboard';
    } catch (exception) {
        const axiosError = exception as AxiosError<{ message?: string; errors?: Record<string, string[]> }>;
        error.value = messageErreur(axiosError.response?.data?.errors?.email?.[0] ?? axiosError.response?.data?.message);
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="Connexion" />

    <main class="min-h-screen bg-slate-50 px-6 py-8 text-slate-950">
        <section class="mx-auto grid min-h-[calc(100vh-4rem)] max-w-6xl items-center gap-10 md:grid-cols-[1fr_430px]">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-700">Gestion de station-service</p>
                <h1 class="mt-5 text-4xl font-semibold tracking-normal text-slate-950 md:text-6xl">
                    Revo, accès sécurisé
                </h1>
                <p class="mt-6 max-w-xl text-base leading-7 text-slate-600">
                    Connectez-vous pour gérer les ventes carburant, la boutique, les encaissements et les opérations de supervision.
                </p>
                <dl class="mt-8 grid gap-3 text-sm text-slate-600 sm:grid-cols-3">
                    <div class="rounded-lg border border-blue-100 bg-white p-4">
                        <dt class="font-semibold text-slate-950">RBAC</dt>
                        <dd class="mt-1">Accès par rôle</dd>
                    </div>
                    <div class="rounded-lg border border-blue-100 bg-white p-4">
                        <dt class="font-semibold text-slate-950">Sanctum</dt>
                        <dd class="mt-1">API protégée</dd>
                    </div>
                    <div class="rounded-lg border border-blue-100 bg-white p-4">
                        <dt class="font-semibold text-slate-950">Invitations</dt>
                        <dd class="mt-1">Activation contrôlée</dd>
                    </div>
                </dl>
            </div>

            <form class="rounded-lg border border-slate-200 bg-white p-6" @submit.prevent="submit">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-blue-700">Connexion</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-950">Accéder au tableau de bord</h2>

                <label class="mt-6 block text-sm font-medium text-slate-700" for="email">Email</label>
                <input
                    id="email"
                    v-model="email"
                    autocomplete="email"
                    class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-950 outline-none ring-0 transition focus:border-blue-600"
                    required
                    type="email"
                />

                <label class="mt-4 block text-sm font-medium text-slate-700" for="password">Mot de passe</label>
                <input
                    id="password"
                    v-model="password"
                    autocomplete="current-password"
                    class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-950 outline-none ring-0 transition focus:border-blue-600"
                    required
                    type="password"
                />

                <p v-if="error" class="mt-4 rounded-md border border-red-800 bg-red-950 px-3 py-2 text-sm text-red-100">
                    {{ error }}
                </p>

                <button
                    class="mt-6 w-full cursor-pointer rounded-md bg-blue-700 px-4 py-2 font-semibold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="loading"
                    type="submit"
                >
                    {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
                </button>
            </form>
        </section>
    </main>
</template>
