<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Paginated } from '@/types';

defineProps<{ paginated: Paginated<unknown> }>();
</script>

<template>
    <nav v-if="paginated.last_page > 1" class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="Pagination">
        <template v-for="link in paginated.links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                class="rounded-full px-4 py-2 text-sm font-semibold transition"
                :class="link.active
                    ? 'bg-ink-900 text-white'
                    : 'bg-ink-50 text-ink-700 hover:bg-brand-100 hover:text-brand-700'"
                preserve-scroll
                v-html="link.label"
            />
            <span
                v-else
                class="rounded-full px-4 py-2 text-sm font-semibold text-ink-300"
                v-html="link.label"
            />
        </template>
    </nav>
</template>
