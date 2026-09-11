<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { reactive, watch } from 'vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import Pagination from '@/Components/Pagination.vue';
import type { Category, Paginated, Product } from '@/types';

defineOptions({ layout: ShopLayout });

const props = defineProps<{
    category: Category & { parent?: Category | null };
    products: Paginated<Product>;
    filters: {
        audience: string | null;
        min: number | null;
        max: number | null;
        disponible: boolean;
        tri: string;
    };
    audiences: { value: string; label: string }[];
}>();

const form = reactive({
    audience: props.filters.audience ?? '',
    min: props.filters.min ?? '',
    max: props.filters.max ?? '',
    disponible: props.filters.disponible,
    tri: props.filters.tri ?? 'nouveaute',
});

let debounce: ReturnType<typeof setTimeout> | undefined;

function applyFilters() {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(
            route('category.show', props.category.slug),
            {
                audience: form.audience || undefined,
                min: form.min || undefined,
                max: form.max || undefined,
                disponible: form.disponible || undefined,
                tri: form.tri !== 'nouveaute' ? form.tri : undefined,
            },
            { preserveState: true, replace: true },
        );
    }, 250);
}

watch(form, applyFilters);
</script>

<template>
    <div class="mx-auto max-w-7xl px-4 py-10">
        <!-- Fil d'Ariane -->
        <nav class="mb-6 flex items-center gap-2 text-sm font-medium text-ink-400">
            <Link :href="route('home')" class="hover:text-brand-600">Accueil</Link>
            <span>/</span>
            <Link
                v-if="category.parent"
                :href="route('category.show', category.parent.slug)"
                class="hover:text-brand-600"
            >
                {{ category.parent.name }}
            </Link>
            <span v-if="category.parent">/</span>
            <span class="text-ink-800">{{ category.name }}</span>
        </nav>

        <header class="mb-8 rounded-3xl bg-azure-100 px-6 py-8">
            <h1 class="font-display text-3xl font-semibold text-ink-900 sm:text-4xl">{{ category.name }}</h1>
            <p class="mt-1 font-medium text-ink-500">{{ products.total }} article{{ products.total > 1 ? 's' : '' }}</p>
        </header>

        <!-- Sous-catégories -->
        <div v-if="category.children?.length" class="mb-8 flex flex-wrap gap-2">
            <Link
                v-for="child in category.children"
                :key="child.id"
                :href="route('category.show', child.slug)"
                class="rounded-full border border-ink-200 px-4 py-2 text-sm font-semibold text-ink-700 transition hover:border-brand-400 hover:bg-brand-50 hover:text-brand-600"
            >
                {{ child.name }}
            </Link>
        </div>

        <!-- Filtres -->
        <div class="mb-8 flex flex-wrap items-end gap-4 rounded-3xl border border-ink-100 bg-white p-5">
            <label class="flex flex-col gap-1 text-sm font-semibold text-ink-700">
                Rayon
                <select v-model="form.audience" class="rounded-xl border-ink-200 text-sm focus:border-brand-400 focus:ring-brand-400">
                    <option value="">Tous</option>
                    <option v-for="a in audiences" :key="a.value" :value="a.value">{{ a.label }}</option>
                </select>
            </label>

            <label class="flex flex-col gap-1 text-sm font-semibold text-ink-700">
                Prix min (F)
                <input v-model="form.min" type="number" min="0" placeholder="0" class="w-28 rounded-xl border-ink-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
            </label>

            <label class="flex flex-col gap-1 text-sm font-semibold text-ink-700">
                Prix max (F)
                <input v-model="form.max" type="number" min="0" placeholder="∞" class="w-28 rounded-xl border-ink-200 text-sm focus:border-brand-400 focus:ring-brand-400" />
            </label>

            <label class="flex items-center gap-2 pb-2 text-sm font-semibold text-ink-700">
                <input v-model="form.disponible" type="checkbox" class="rounded border-ink-300 text-brand-500 focus:ring-brand-400" />
                En stock
            </label>

            <label class="ml-auto flex flex-col gap-1 text-sm font-semibold text-ink-700">
                Trier par
                <select v-model="form.tri" class="rounded-xl border-ink-200 text-sm focus:border-brand-400 focus:ring-brand-400">
                    <option value="nouveaute">Nouveautés</option>
                    <option value="prix_asc">Prix croissant</option>
                    <option value="prix_desc">Prix décroissant</option>
                    <option value="nom">Nom A–Z</option>
                </select>
            </label>
        </div>

        <!-- Grille produits -->
        <div v-if="products.data.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
            <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
        </div>

        <div v-else class="rounded-3xl bg-ink-50 px-6 py-16 text-center">
            <p class="text-4xl">🧺</p>
            <p class="mt-4 font-display text-xl font-semibold text-ink-800">Aucun article ne correspond</p>
            <p class="mt-1 font-medium text-ink-400">Essayez d'élargir vos filtres.</p>
        </div>

        <Pagination :paginated="products" />
    </div>
</template>
