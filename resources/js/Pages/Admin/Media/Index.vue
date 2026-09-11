<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import type { Media, Paginated } from '@/types';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    media: Paginated<Media & { product?: { id: number; name: string } | null }>;
    filters: { q: string | null };
}>();

const search = ref(props.filters.q ?? '');

let debounce: ReturnType<typeof setTimeout> | undefined;
watch(search, (value) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.medias.index'), { q: value || undefined }, { preserveState: true, replace: true });
    }, 300);
});

function destroy(media: Media) {
    if (confirm('Supprimer définitivement cette image ?')) {
        router.delete(route('admin.medias.destroy', media.id), { preserveScroll: true });
    }
}
</script>

<template>
    <div>
        <div class="mb-6 flex flex-wrap items-center gap-4">
            <h1 class="font-display text-2xl font-semibold text-ink-900">Bibliothèque d'images</h1>
            <input
                v-model="search"
                type="search"
                placeholder="Filtrer par produit…"
                class="w-72 rounded-xl border-ink-200 text-sm focus:border-brand-400 focus:ring-brand-400"
            />
            <p class="ml-auto text-sm font-medium text-ink-400">{{ media.total }} image{{ media.total > 1 ? 's' : '' }}</p>
        </div>

        <div v-if="media.data.length" class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-6">
            <figure v-for="item in media.data" :key="item.id" class="group relative overflow-hidden rounded-2xl border border-ink-100 bg-white">
                <img :src="item.url" :alt="item.alt ?? ''" class="aspect-square w-full object-cover" loading="lazy" />
                <figcaption class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-2 bg-ink-950/70 px-3 py-2 text-xs font-medium text-white opacity-0 transition group-hover:opacity-100">
                    <span class="truncate">{{ item.product?.name ?? 'Sans produit' }}</span>
                    <button class="font-bold text-brand-300 hover:text-brand-200" @click="destroy(item)">Suppr.</button>
                </figcaption>
            </figure>
        </div>

        <p v-else class="rounded-3xl bg-white px-6 py-12 text-center text-sm font-medium text-ink-400">
            Aucune image trouvée.
        </p>

        <Pagination :paginated="media" />
    </div>
</template>
