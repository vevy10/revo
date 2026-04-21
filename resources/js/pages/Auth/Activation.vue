<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type { AxiosError } from 'axios';
import { computed, ref } from 'vue';
import { useAuth } from '@/composables/useAuth';

const search = new URLSearchParams(window.location.search);
const token = search.get('token') ?? '';
const firstName = ref('');
const lastName = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const error = ref('');
const loading = ref(false);
const auth = useAuth();

const canSubmit = computed(() => token.length > 0 && password.value === passwordConfirmation.value);

function messageErreur(message?: string) {
    if (!message) {
        return 'Activation impossible. Vérifiez les champs puis réessayez.';
    }

    const traductions: Record<string, string> = {
        'Invalid invitation token.': 'Lien d’invitation invalide.',
        'Invitation already accepted.': 'Cette invitation a déjà été utilisée.',
        'Invitation token expired.': 'Cette invitation a expiré.',
    };

    return traductions[message] ?? message;
}

async function submit() {
    error.value = '';
    loading.value = true;

    try {
        await auth.activate({
            token,
            first_name: firstName.value,
            last_name: lastName.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value,
        });
        window.location.href = '/dashboard';
    } catch (exception) {
        const axiosError = exception as AxiosError<{ message?: string; errors?: Record<string, string[]> }>;
        const errors = axiosError.response?.data?.errors;
        error.value = messageErreur(
            errors?.password?.[0] ?? errors?.first_name?.[0] ?? errors?.last_name?.[0] ?? axiosError.response?.data?.message,
        );
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="Activation du compte" />

    <main class="min-h-screen bg-slate-50 px-6 py-8 text-slate-950">
        <section class="mx-auto grid min-h-[calc(100vh-4rem)] max-w-6xl items-center gap-10 md:grid-cols-[1fr_470px]">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-700">Activation du compte</p>
                <h1 class="mt-5 text-4xl font-semibold tracking-normal md:text-6xl">
                    Finalisez votre accès Revo
                </h1>
                <p class="mt-6 max-w-xl text-base leading-7 text-slate-600">
                    Complétez votre profil et définissez un mot de passe fort pour activer le compte créé par l’administrateur.
                </p>
                <div class="mt-8 rounded-lg border border-blue-100 bg-white p-5 text-sm text-slate-600">
                    Le lien est valable 48 heures et ne peut être utilisé qu’une seule fois. Le mot de passe doit contenir au moins
                    10 caractères, des majuscules, des minuscules et des chiffres.
                </div>
            </div>

            <form class="rounded-lg border border-slate-200 bg-white p-6" @submit.prevent="submit">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-blue-700">Profil utilisateur</p>
                <h2 class="mt-2 text-2xl font-semibold">Créer votre accès</h2>

                <p v-if="!token" class="mt-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    Le lien d’invitation est incomplet.
                </p>

                <label class="mt-6 block text-sm font-medium text-slate-700" for="first_name">Prénom</label>
                <input
                    id="first_name"
                    v-model="firstName"
                    class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 outline-none transition focus:border-blue-600"
                    required
                    type="text"
                />

                <label class="mt-4 block text-sm font-medium text-slate-700" for="last_name">Nom</label>
                <input
                    id="last_name"
                    v-model="lastName"
                    class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 outline-none transition focus:border-blue-600"
                    required
                    type="text"
                />

                <label class="mt-4 block text-sm font-medium text-slate-700" for="password">Mot de passe</label>
                <input
                    id="password"
                    v-model="password"
                    class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 outline-none transition focus:border-blue-600"
                    required
                    type="password"
                />

                <label class="mt-4 block text-sm font-medium text-slate-700" for="password_confirmation">
                    Confirmer le mot de passe
                </label>
                <input
                    id="password_confirmation"
                    v-model="passwordConfirmation"
                    class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 outline-none transition focus:border-blue-600"
                    required
                    type="password"
                />

                <p v-if="error" class="mt-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ error }}
                </p>

                <button
                    class="mt-6 w-full cursor-pointer rounded-md bg-blue-700 px-4 py-2 font-semibold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="loading || !canSubmit"
                    type="submit"
                >
                    {{ loading ? 'Activation en cours...' : 'Activer le compte' }}
                </button>
            </form>
        </section>
    </main>
</template>
