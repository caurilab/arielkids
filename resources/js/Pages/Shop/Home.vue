<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import InstaFeed from '@/Components/InstaFeed.vue';
import type { InstaPost, Product } from '@/types';

defineOptions({ layout: ShopLayout });

defineProps<{
    featured: Product[];
    newArrivals: Product[];
    audiences: { label: string; audience: string; slug: string | null; image: string | null }[];
    instagramPosts?: InstaPost[];
}>();

const audienceBg: Record<string, string> = {
    Filles: 'bg-blush-100',
    Garçons: 'bg-sky-100',
    Bébé: 'bg-mint-100',
    Femmes: 'bg-lav-100',
    Hommes: 'bg-peach-100',
};

const guarantees = [
    { icon: '👕', title: 'Qualité premium', text: 'Matières douces, saines et durables' },
    { icon: '🛡️', title: 'Pensé pour les enfants', text: 'Articles testés et certifiés' },
    { icon: '🚚', title: 'Livraison rapide', text: 'Abidjan et toute la Côte d\'Ivoire' },
    { icon: '🏷️', title: 'Prix justes', text: 'Le meilleur rapport qualité-prix' },
];
</script>

<template>
    <!-- Hero -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blush-100 via-peach-100 to-lav-100">
        <div class="pointer-events-none absolute -left-16 top-10 h-48 w-48 animate-float rounded-full bg-brand-200/50 blur-2xl" />
        <div class="pointer-events-none absolute -right-10 bottom-6 h-56 w-56 animate-float-slow rounded-full bg-sky-300/40 blur-2xl" />

        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-14 lg:grid-cols-2 lg:py-20">
            <div class="relative text-center lg:text-left">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-brand-600">
                    ♥ Qualité premium pour les petits
                </span>
                <h1 class="mt-5 font-display text-4xl font-semibold leading-tight text-ink-900 sm:text-5xl lg:text-6xl">
                    Le style de vos <span class="text-brand-500">enfants</span>,<br />
                    notre affaire.
                </h1>
                <p class="mx-auto mt-5 max-w-md text-lg font-medium text-ink-500 lg:mx-0">
                    Mode enfant, femme et homme — élégante, confortable et livrée partout en Côte d'Ivoire.
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-3 lg:justify-start">
                    <Link
                        v-if="audiences[0]?.slug"
                        :href="route('category.show', audiences[0].slug)"
                        class="rounded-full bg-brand-500 px-8 py-3.5 font-display font-semibold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600"
                    >
                        Découvrir la boutique →
                    </Link>
                    <Link
                        v-if="audiences[3]?.slug"
                        :href="route('category.show', audiences[3].slug)"
                        class="rounded-full border-2 border-ink-900 px-8 py-3.5 font-display font-semibold text-ink-900 transition hover:bg-ink-900 hover:text-white"
                    >
                        Côté adultes
                    </Link>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-md">
                <div class="animate-float overflow-hidden rounded-[2.5rem] bg-white p-3 shadow-2xl shadow-brand-500/20">
                    <img
                        v-if="audiences[0]?.image"
                        :src="audiences[0].image"
                        alt="Sélection Ariel Kid's"
                        class="aspect-square w-full rounded-[2rem] object-cover"
                    />
                </div>
                <div class="absolute -bottom-5 -left-5 animate-float-slow rounded-3xl bg-white px-5 py-3 shadow-xl">
                    <p class="font-display text-sm font-semibold text-ink-900">🎁 Nouveautés</p>
                    <p class="text-xs font-medium text-ink-400">chaque semaine</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Garanties -->
    <section class="mx-auto max-w-7xl px-4 py-12">
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div
                v-for="g in guarantees"
                :key="g.title"
                class="flex items-center gap-4 rounded-3xl border border-ink-100 bg-white p-5"
            >
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blush-100 text-2xl">
                    {{ g.icon }}
                </span>
                <div>
                    <p class="font-display font-semibold text-ink-900">{{ g.title }}</p>
                    <p class="text-xs font-medium text-ink-400">{{ g.text }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rayons -->
    <section class="mx-auto max-w-7xl px-4 py-10">
        <h2 class="text-center font-display text-3xl font-semibold text-ink-900">Explorer les rayons</h2>
        <p class="mt-2 text-center font-medium text-ink-400">Les enfants d'abord — et toute la famille ensuite</p>

        <div class="mt-8 flex flex-wrap justify-center gap-6">
            <Link
                v-for="card in audiences"
                :key="card.audience"
                :href="card.slug ? route('category.show', card.slug) : '#'"
                class="group flex w-32 flex-col items-center gap-3"
            >
                <div
                    class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border-4 border-white shadow-lg transition group-hover:scale-105"
                    :class="audienceBg[card.label] ?? 'bg-ink-50'"
                >
                    <img
                        v-if="card.image"
                        :src="card.image"
                        :alt="`Rayon ${card.label}`"
                        class="h-full w-full object-cover"
                        loading="lazy"
                    />
                    <span v-else class="text-3xl">🧸</span>
                </div>
                <span class="font-display font-semibold text-ink-800 transition group-hover:text-brand-600">
                    {{ card.label }}
                </span>
            </Link>
        </div>
    </section>

    <!-- Coups de cœur -->
    <section class="mx-auto max-w-7xl px-4 py-12">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h2 class="font-display text-3xl font-semibold text-ink-900">Nos coups de cœur</h2>
                <p class="mt-1 font-medium text-ink-400">La sélection du moment, enfants d'abord</p>
            </div>
            <span class="hidden rounded-full bg-brand-100 px-4 py-1.5 text-sm font-bold text-brand-700 sm:inline">
                Meilleures ventes
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
            <ProductCard v-for="product in featured" :key="product.id" :product="product" />
        </div>
    </section>

    <!-- Bandeau promo -->
    <section class="mx-auto max-w-7xl px-4 py-8">
        <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-r from-mint-100 via-sky-100 to-lav-100 px-8 py-12 lg:px-16">
            <div class="pointer-events-none absolute -right-8 -top-8 h-40 w-40 animate-float rounded-full bg-sun-300/50 blur-xl" />
            <div class="max-w-lg">
                <span class="font-display text-sm font-bold uppercase tracking-widest text-mint-500">
                    Chaque semaine
                </span>
                <h2 class="mt-2 font-display text-3xl font-semibold text-ink-900 sm:text-4xl">
                    Des nouveautés rien que pour eux
                </h2>
                <p class="mt-3 font-medium text-ink-500">
                    Découvrez les dernières tendances mode enfant, sélectionnées avec amour.
                </p>
                <Link
                    v-if="audiences[1]?.slug"
                    :href="route('category.show', audiences[1].slug)"
                    class="mt-6 inline-block rounded-full bg-ink-900 px-7 py-3 font-display font-semibold text-white transition hover:bg-brand-500"
                >
                    Explorer maintenant →
                </Link>
            </div>
        </div>
    </section>

    <!-- Nouveautés -->
    <section class="mx-auto max-w-7xl px-4 py-12">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h2 class="font-display text-3xl font-semibold text-ink-900">Nouveautés</h2>
                <p class="mt-1 font-medium text-ink-400">Fraîchement arrivés en boutique</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
            <ProductCard v-for="product in newArrivals" :key="product.id" :product="product" />
        </div>
    </section>

    <!-- Instagram -->
    <InstaFeed v-if="instagramPosts?.length" :posts="instagramPosts" />

    <!-- Réassurance finale -->
    <section class="border-t border-ink-100 bg-ink-50/50">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 py-10 text-center lg:grid-cols-4">
            <div>
                <p class="text-3xl">🔒</p>
                <p class="mt-2 font-display font-semibold text-ink-900">Paiement sécurisé</p>
                <p class="text-sm font-medium text-ink-400">Mobile Money &amp; cartes</p>
            </div>
            <div>
                <p class="text-3xl">↩️</p>
                <p class="mt-2 font-display font-semibold text-ink-900">Retours faciles</p>
                <p class="text-sm font-medium text-ink-400">Sous 30 jours</p>
            </div>
            <div>
                <p class="text-3xl">💛</p>
                <p class="mt-2 font-display font-semibold text-ink-900">Adoré des parents</p>
                <p class="text-sm font-medium text-ink-400">Des centaines de familles</p>
            </div>
            <div>
                <p class="text-3xl">💬</p>
                <p class="mt-2 font-display font-semibold text-ink-900">Support 7j/7</p>
                <p class="text-sm font-medium text-ink-400">Sur WhatsApp</p>
            </div>
        </div>
    </section>
</template>
