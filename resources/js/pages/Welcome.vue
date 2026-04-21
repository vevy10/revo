<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Gestion de station-service">
        <meta
            head-key="description"
            name="description"
            content="Revo centralise la gestion des ventes carburant, de la boutique, des encaissements et des accès utilisateurs par rôle."
        />
        <meta head-key="og:title" property="og:title" content="Revo - Gestion de station-service" />
        <meta
            head-key="og:description"
            property="og:description"
            content="Application professionnelle de gestion de station-service avec invitations sécurisées et contrôle d’accès par rôle."
        />
    </Head>

    <main class="min-h-screen bg-slate-50 px-6 py-8 text-slate-950">
        <section class="mx-auto grid min-h-[calc(100vh-4rem)] max-w-6xl items-center gap-10 md:grid-cols-[1fr_430px]">
            <div class="max-w-2xl">
                <img alt="Revo" class="h-16 w-auto object-contain" height="72" src="/logo.png" width="132" />
                <p class="mt-8 text-sm font-semibold uppercase tracking-[0.22em] text-blue-700">Station-service</p>
                <h1 class="mt-5 text-4xl font-semibold tracking-normal md:text-6xl">Revo, gestion opérationnelle sécurisée</h1>
                <p class="mt-6 max-w-xl text-base leading-7 text-slate-600">
                    Pilotez les équipes, les ventes carburant, la boutique et les encaissements depuis un espace clair, sécurisé et
                    adapté aux rôles de chaque utilisateur.
                </p>
                <a
                    class="mt-8 inline-flex cursor-pointer rounded-md bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800"
                    href="/login"
                >
                    Accéder à l’application
                </a>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-6">
                <h2 class="text-xl font-semibold">Modules principaux</h2>
                <dl class="mt-6 grid gap-4 text-sm text-slate-600">
                    <div class="rounded-lg border border-blue-100 p-4">
                        <dt class="font-semibold text-slate-950">Invitations sécurisées</dt>
                        <dd class="mt-1">Activation par email avec lien valable 48 heures et utilisable une seule fois.</dd>
                    </div>
                    <div class="rounded-lg border border-blue-100 p-4">
                        <dt class="font-semibold text-slate-950">Contrôle par rôle</dt>
                        <dd class="mt-1">ADMIN, MANAGER, FUEL_OPERATOR, SHOP_MANAGER et CASHIER.</dd>
                    </div>
                    <div class="rounded-lg border border-blue-100 p-4">
                        <dt class="font-semibold text-slate-950">API protégée</dt>
                        <dd class="mt-1">Authentification Sanctum et middleware RBAC côté Laravel.</dd>
                    </div>
                </dl>
            </div>
        </section>
    </main>
</template>
