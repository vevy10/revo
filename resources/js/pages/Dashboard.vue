<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';

const auth = useAuth();

function roleLabel(code?: string) {
    const labels: Record<string, string> = {
        ADMIN: 'Administrateur',
        MANAGER: 'Manager',
        FUEL_OPERATOR: 'Opérateur carburant',
        SHOP_MANAGER: 'Responsable boutique',
        CASHIER: 'Caissier',
    };

    return code ? (labels[code] ?? code) : 'Non défini';
}

onMounted(async () => {
    if (!auth.isAuthenticated.value) {
        window.location.href = '/login';

        return;
    }

    await auth.fetchUser();
});
</script>

<template>
    <Head title="Tableau de bord" />

    <main class="min-h-screen bg-slate-50 px-6 py-8 text-slate-950">
        <section class="mx-auto max-w-6xl">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-6">
                <div class="flex items-center gap-4">
                    <img alt="Revo" class="h-12 w-auto object-contain" height="56" src="/logo.png" width="103" />
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-700">Tableau de bord</p>
                        <h1 class="mt-2 text-3xl font-semibold">Espace station</h1>
                    </div>
                </div>
                <button class="cursor-pointer rounded-md border border-slate-300 bg-white px-4 py-2 text-sm hover:border-blue-600" @click="auth.logout">
                    Se déconnecter
                </button>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <article class="rounded-lg border border-slate-200 bg-white p-5">
                    <p class="text-sm text-slate-500">Utilisateur</p>
                    <p class="mt-2 text-xl font-semibold">{{ auth.user.value?.name || auth.user.value?.email }}</p>
                </article>
                <article class="rounded-lg border border-slate-200 bg-white p-5">
                    <p class="text-sm text-slate-500">Rôle</p>
                    <p class="mt-2 text-xl font-semibold">{{ roleLabel(auth.user.value?.role?.code) }}</p>
                </article>
                <article class="rounded-lg border border-slate-200 bg-white p-5">
                    <p class="text-sm text-slate-500">Accès</p>
                    <p class="mt-2 text-xl font-semibold">Contrôle par rôle actif</p>
                </article>
            </div>

            <a
                v-if="auth.user.value?.role?.code === 'ADMIN'"
                class="mt-6 inline-flex cursor-pointer rounded-md bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800"
                href="/admin/invitations"
            >
                Inviter un utilisateur
            </a>
        </section>
    </main>
</template>
