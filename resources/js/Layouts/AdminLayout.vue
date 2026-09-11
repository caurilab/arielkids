<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref } from 'vue';
import FlashToast from '@/Components/FlashToast.vue';

defineProps<{ title?: string }>();

const page = usePage();
const sidebarOpen = ref(false);

const nav = [
    { label: 'Tableau de bord', icon: '📊', route: 'admin.dashboard' },
    { label: 'Produits', icon: '👟', route: 'admin.products.index' },
    { label: 'Catégories', icon: '🗂️', route: 'admin.categories.index' },
    { label: 'Commandes', icon: '📦', route: 'admin.orders.index' },
    { label: 'Médias', icon: '🖼️', route: 'admin.medias.index' },
];

function isActive(name: string): boolean {
    return route().current(`${name}*`) === true;
}

function logout() {
    router.post(route('admin.logout'));
}
</script>

<template>
    <Head :title="title" />

    <div class="flex min-h-screen bg-ink-50">
        <!-- Barre latérale -->
        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-ink-950 text-ink-200 transition-transform lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <Link :href="route('admin.dashboard')" class="flex items-center gap-2 px-6 py-5">
                <img src="/images/brand/logo-bag.png" alt="Ariel Kids" class="h-9 w-auto" />
                <span class="font-display text-lg font-semibold text-white">Ariel Kids</span>
                <span class="rounded-full bg-brand-500 px-2 py-0.5 text-[10px] font-bold text-white">ADMIN</span>
            </Link>

            <nav class="flex-1 space-y-1 px-3 py-4">
                <Link
                    v-for="item in nav"
                    :key="item.route"
                    :href="route(item.route)"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition"
                    :class="isActive(item.route)
                        ? 'bg-brand-500 text-white'
                        : 'text-ink-300 hover:bg-ink-800 hover:text-white'"
                >
                    <span>{{ item.icon }}</span>
                    {{ item.label }}
                </Link>
            </nav>

            <div class="border-t border-ink-800 p-4">
                <Link :href="route('home')" class="block rounded-xl px-4 py-2 text-sm font-semibold text-ink-300 hover:text-white">
                    ← Voir la boutique
                </Link>
                <button
                    class="mt-1 w-full rounded-xl px-4 py-2 text-left text-sm font-semibold text-brand-300 hover:text-brand-200"
                    @click="logout"
                >
                    Se déconnecter
                </button>
            </div>
        </aside>

        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-30 bg-ink-950/40 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Contenu -->
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-20 flex items-center gap-4 border-b border-ink-100 bg-white px-5 py-3">
                <button
                    class="rounded-full p-2 text-ink-700 hover:bg-ink-50 lg:hidden"
                    aria-label="Ouvrir le menu"
                    @click="sidebarOpen = true"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h1 class="font-display text-lg font-semibold text-ink-900">{{ title ?? 'Administration' }}</h1>
                <span class="ml-auto text-sm font-medium text-ink-400">
                    {{ (page.props as any).auth?.user?.name }}
                </span>
            </header>

            <main class="flex-1 p-5 lg:p-8">
                <slot />
            </main>
        </div>

        <FlashToast />
    </div>
</template>
