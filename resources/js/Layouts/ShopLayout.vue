<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, ref } from 'vue';
import type { SharedProps } from '@/types';
import { useCart } from '@/Composables/useCart';
import WhatsAppButton from '@/Components/WhatsAppButton.vue';
import FlashToast from '@/Components/FlashToast.vue';

defineProps<{ title?: string }>();

const page = usePage<SharedProps>();
const categories = computed(() => page.props.categories);
const { count } = useCart();

const mobileOpen = ref(false);
const openMenu = ref<number | null>(null);

const year = new Date().getFullYear();
</script>

<template>
    <Head :title="title" />

    <div class="flex min-h-screen flex-col bg-white">
        <!-- Barre d'annonce -->
        <div class="bg-brand-500 text-white">
            <div class="mx-auto flex max-w-7xl items-center justify-center gap-6 px-4 py-2 text-xs font-semibold tracking-wide">
                <span class="hidden sm:inline">🚚 Livraison rapide à Abidjan</span>
                <span class="hidden md:inline">↩️ Retours faciles sous 30 jours</span>
                <span>🔒 Paiement 100&nbsp;% sécurisé</span>
                <span class="hidden sm:inline">💬 Support 7j/7</span>
            </div>
        </div>

        <!-- En-tête -->
        <header class="sticky top-0 z-40 border-b border-ink-100 bg-white/90 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3">
                <!-- Menu mobile -->
                <button
                    class="rounded-full p-2 text-ink-700 hover:bg-ink-50 lg:hidden"
                    aria-label="Ouvrir le menu"
                    @click="mobileOpen = true"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <Link :href="route('home')" class="flex items-center gap-2.5">
                    <img src="/images/brand/logo-bag.png" alt="Ariel Kids" class="h-11 w-auto" />
                    <img src="/images/brand/logo-icon.png" alt="Ariel Kids" class="hidden h-7 w-auto sm:block" />
                </Link>

                <!-- Navigation bureau -->
                <nav class="ml-6 hidden flex-1 items-center gap-1 lg:flex">
                    <div
                        v-for="cat in categories"
                        :key="cat.id"
                        class="relative"
                        @mouseenter="openMenu = cat.id"
                        @mouseleave="openMenu = null"
                    >
                        <Link
                            :href="route('category.show', cat.slug)"
                            class="flex items-center gap-1 rounded-full px-4 py-2 text-sm font-semibold text-ink-800 transition hover:bg-brand-50 hover:text-brand-600"
                        >
                            {{ cat.name }}
                            <svg
                                v-if="cat.children?.length"
                                class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </Link>

                        <div
                            v-if="cat.children?.length && openMenu === cat.id"
                            class="absolute left-0 top-full z-50 min-w-52 rounded-2xl border border-ink-100 bg-white p-2 shadow-xl shadow-ink-900/10"
                        >
                            <Link
                                v-for="child in cat.children"
                                :key="child.id"
                                :href="route('category.show', child.slug)"
                                class="block rounded-xl px-4 py-2 text-sm font-medium text-ink-700 transition hover:bg-brand-50 hover:text-brand-600"
                            >
                                {{ child.name }}
                            </Link>
                        </div>
                    </div>
                </nav>

                <div class="ml-auto flex items-center gap-2">
                    <!-- Panier -->
                    <Link
                        :href="route('cart.index')"
                        class="relative flex items-center gap-2 rounded-full bg-ink-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-ink-700"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        <span class="hidden sm:inline">Panier</span>
                        <span
                            v-if="count > 0"
                            class="absolute -right-1.5 -top-1.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-500 px-1 text-xs font-bold text-white"
                        >
                            {{ count }}
                        </span>
                    </Link>
                </div>
            </div>
        </header>

        <!-- Tiroir mobile -->
        <Teleport to="body">
            <div
                v-if="mobileOpen"
                class="fixed inset-0 z-50 lg:hidden"
                @keydown.escape="mobileOpen = false"
            >
                <div class="absolute inset-0 bg-ink-950/40" @click="mobileOpen = false" />
                <div class="absolute left-0 top-0 flex h-full w-80 max-w-[85vw] flex-col bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4">
                        <span class="font-display text-xl font-semibold">Menu</span>
                        <button class="rounded-full p-2 hover:bg-ink-50" aria-label="Fermer" @click="mobileOpen = false">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <nav class="flex-1 overflow-y-auto p-4">
                        <div v-for="cat in categories" :key="cat.id" class="mb-2">
                            <Link
                                :href="route('category.show', cat.slug)"
                                class="block rounded-xl bg-blush-100 px-4 py-3 font-display font-semibold text-ink-900"
                                @click="mobileOpen = false"
                            >
                                {{ cat.name }}
                            </Link>
                            <Link
                                v-for="child in cat.children"
                                :key="child.id"
                                :href="route('category.show', child.slug)"
                                class="block px-6 py-2 text-sm font-medium text-ink-700"
                                @click="mobileOpen = false"
                            >
                                {{ child.name }}
                            </Link>
                        </div>
                    </nav>
                </div>
            </div>
        </Teleport>

        <!-- Contenu -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Pied de page -->
        <footer class="mt-20 bg-ink-950 text-ink-200">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <img src="/images/brand/logo-white.png" alt="Ariel Kids" class="h-16 w-auto" />
                    <p class="mt-4 text-sm leading-relaxed text-ink-300">
                        Le style de vos enfants, notre affaire. Mode enfant, femme et homme — livrée partout en Côte d'Ivoire.
                    </p>
                </div>

                <div>
                    <h3 class="font-display font-semibold text-white">Boutique</h3>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li v-for="cat in categories" :key="cat.id">
                            <Link :href="route('category.show', cat.slug)" class="transition hover:text-brand-300">
                                {{ cat.name }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-display font-semibold text-white">Aide</h3>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li>Livraison &amp; retours</li>
                        <li>Paiement Mobile Money</li>
                        <li>Guide des tailles</li>
                        <li>Nous contacter</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-display font-semibold text-white">Paiement sécurisé</h3>
                    <p class="mt-4 text-sm text-ink-300">Orange Money · MTN · Moov · Wave · Cartes</p>
                    <p class="mt-2 text-sm text-ink-300">ou paiement à la livraison.</p>
                </div>
            </div>
            <div class="border-t border-ink-800 py-5 text-center text-xs text-ink-400">
                © {{ year }} Ariel Kid's — Abidjan, Côte d'Ivoire. Tous droits réservés.
            </div>
        </footer>

        <WhatsAppButton />
        <FlashToast />
    </div>
</template>
