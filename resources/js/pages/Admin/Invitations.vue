<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { useAuth } from '@/composables/useAuth';
import api from '@/lib/api';

const auth = useAuth();
const email = ref('');
const role = ref('CASHIER');
const error = ref('');
const message = ref('');
const loading = ref(false);
const checkingAuth = ref(true);

const roles = [
    { code: 'ADMIN', label: 'Administrateur' },
    { code: 'MANAGER', label: 'Manager' },
    { code: 'FUEL_OPERATOR', label: 'Opérateur carburant' },
    { code: 'SHOP_MANAGER', label: 'Responsable boutique' },
    { code: 'CASHIER', label: 'Caissier' },
];

onMounted(async () => {
    if (!auth.isAuthenticated.value) {
        window.location.href = '/login';

        return;
    }

    try {
        const user = await auth.fetchUser();

        if (user?.role.code !== 'ADMIN') {
            window.location.href = '/dashboard';
        }
    } finally {
        checkingAuth.value = false;
    }
});

async function submit() {
    if (!auth.isAuthenticated.value || checkingAuth.value) {
        error.value = 'Votre session a expiré. Connectez-vous à nouveau.';
        window.location.href = '/login';

        return;
    }

    error.value = '';
    message.value = '';
    loading.value = true;

    try {
        await api.post('/invitations', {
            email: email.value,
            role: role.value,
        });

        message.value = 'Invitation envoyée. Vérifiez la boîte mail du destinataire, y compris Spam et Promotions.';
        email.value = '';
        role.value = 'CASHIER';
    } catch (exception: unknown) {
        const response = (exception as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }).response;

        error.value =
            response?.data?.errors?.email?.[0] ??
            response?.data?.errors?.role?.[0] ??
            response?.data?.message ??
            'Impossible d’envoyer l’invitation. Reconnectez-vous si votre session a expiré.';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="Inviter un utilisateur" />

    <main class="min-h-screen bg-slate-50 px-6 py-8 text-slate-950">
        <section class="mx-auto max-w-5xl">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-6">
                <div class="flex items-center gap-4">
                    <img alt="Revo" class="h-12 w-auto object-contain" height="56" src="/logo.png" width="103" />
                    <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-700">Administration</p>
                    <h1 class="mt-2 text-3xl font-semibold">Inviter un utilisateur</h1>
                    </div>
                </div>
                <a class="cursor-pointer rounded-md border border-slate-300 bg-white px-4 py-2 text-sm hover:border-blue-600" href="/dashboard">
                    Tableau de bord
                </a>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
                <form class="rounded-lg border border-slate-200 bg-white p-6" @submit.prevent="submit">
                    <img alt="Revo" class="h-10 w-auto object-contain" height="48" src="/logo.png" width="88" />
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-blue-700">Nouvelle invitation</p>
                    <h2 class="mt-2 text-2xl font-semibold">Créer un accès contrôlé</h2>

                    <label class="mt-6 block text-sm font-medium text-slate-700" for="email">Adresse email</label>
                    <input
                        id="email"
                        v-model="email"
                        class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-950 outline-none transition focus:border-blue-600"
                        placeholder="utilisateur@exemple.com"
                        required
                        type="email"
                    />

                    <label class="mt-4 block text-sm font-medium text-slate-700" for="role">Rôle</label>
                    <select
                        id="role"
                        v-model="role"
                        class="mt-2 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-950 outline-none transition focus:border-blue-600"
                    >
                        <option v-for="item in roles" :key="item.code" :value="item.code">
                            {{ item.label }}
                        </option>
                    </select>

                    <p v-if="error" class="mt-4 rounded-md border border-red-800 bg-red-950 px-3 py-2 text-sm text-red-100">
                        {{ error }}
                    </p>

                    <p v-if="message" class="mt-4 rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-800">
                        {{ message }}
                    </p>

                    <button
                        class="mt-6 cursor-pointer rounded-md bg-blue-700 px-4 py-2 font-semibold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="loading || checkingAuth"
                        type="submit"
                    >
                        {{ checkingAuth ? 'Vérification de la session...' : loading ? 'Envoi en cours...' : 'Envoyer l’invitation' }}
                    </button>
                </form>

                <aside class="rounded-lg border border-blue-100 bg-white p-6 text-sm text-slate-600">
                    <h2 class="text-lg font-semibold text-slate-950">Règles d’accès</h2>
                    <p class="mt-3 leading-6">
                        Seul un administrateur peut créer un utilisateur. Le compte reste inactif jusqu’à la validation du lien reçu par email.
                    </p>
                    <ul class="mt-5 space-y-3">
                        <li>ADMIN: accès complet</li>
                        <li>MANAGER: supervision et gestion</li>
                        <li>FUEL_OPERATOR: ventes carburant</li>
                        <li>SHOP_MANAGER: gestion boutique</li>
                        <li>CASHIER: encaissement</li>
                    </ul>
                </aside>
            </div>
        </section>
    </main>
</template>
