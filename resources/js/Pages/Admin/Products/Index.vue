<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import type { Paginated, Product } from '@/types';
import { useCurrency } from '@/Composables/useCurrency';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    products: Paginated<Product & { media_count: number }>;
    filters: { q: string | null };
}>();

const { format } = useCurrency();
const search = ref(props.filters.q ?? '');

let debounce: ReturnType<typeof setTimeout> | undefined;
watch(search, (value) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.products.index'), { q: value || undefined }, { preserveState: true, replace: true });
    }, 300);
});

function destroy(product: Product) {
    if (confirm(`Supprimer « ${product.name} » ?`)) {
        router.delete(route('admin.products.destroy', product.id));
    }
}
</script>

<template>
    <div>
        <div class="mb-6 flex flex-wrap items-center gap-4">
            <h1 class="font-display text-2xl font-semibold text-ink-900">Produits</h1>
            <input
                v-model="search"
                type="search"
                placeholder="Rechercher un produit, un SKU…"
                class="w-72 rounded-xl border-ink-200 text-sm focus:border-brand-400 focus:ring-brand-400"
            />
            <Link
                :href="route('admin.products.create')"
                class="ml-auto rounded-full bg-brand-500 px-5 py-2.5 font-display text-sm font-semibold text-white transition hover:bg-brand-600"
            >
                + Nouveau produit
            </Link>
        </div>

        <div class="overflow-hidden rounded-3xl border border-ink-100 bg-white">
            <table class="w-full min-w-[760px] text-sm">
                <thead>
                    <tr class="border-b border-ink-100 text-left text-xs font-bold uppercase tracking-wide text-ink-400">
                        <th class="px-5 py-3.5">Produit</th>
                        <th class="px-5 py-3.5">Catégorie</th>
                        <th class="px-5 py-3.5">Prix</th>
                        <th class="px-5 py-3.5">Stock</th>
                        <th class="px-5 py-3.5">Statut</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    <tr v-for="product in products.data" :key="product.id" class="hover:bg-ink-50/60">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-ink-50">
                                    <img v-if="product.media[0]" :src="product.media[0].url" :alt="product.name" class="h-full w-full object-cover" />
                                </div>
                                <div>
                                    <p class="font-semibold text-ink-900">{{ product.name }}</p>
                                    <p class="text-xs text-ink-400">
                                        {{ product.sku ?? '—' }} · {{ product.media_count }} image{{ product.media_count > 1 ? 's' : '' }}
                                        <span v-if="product.is_featured" class="ml-1 rounded-full bg-brand-100 px-2 py-0.5 font-bold text-brand-600">★ vedette</span>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 font-medium text-ink-600">{{ product.category?.name ?? '—' }}</td>
                        <td class="px-5 py-3 font-semibold text-ink-900">{{ format(product.price) }}</td>
                        <td class="px-5 py-3">
                            <span :class="product.stock > 0 ? 'text-mint-500' : 'text-brand-600'" class="font-semibold">
                                {{ product.stock }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span
                                class="rounded-full px-3 py-1 text-xs font-bold"
                                :class="product.is_active ? 'bg-mint-100 text-mint-500' : 'bg-ink-100 text-ink-500'"
                            >
                                {{ product.is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <Link
                                :href="route('admin.products.edit', product.id)"
                                class="font-semibold text-brand-600 hover:text-brand-700"
                            >
                                Modifier
                            </Link>
                            <button class="ml-4 font-semibold text-ink-400 hover:text-brand-600" @click="destroy(product)">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p v-if="!products.data.length" class="px-6 py-12 text-center text-sm font-medium text-ink-400">
                Aucun produit trouvé.
            </p>
        </div>

        <Pagination :paginated="products" />
    </div>
</template>
