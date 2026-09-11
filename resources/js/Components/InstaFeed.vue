<script setup lang="ts">
import type { InstaPost } from '@/types';

defineProps<{ posts: InstaPost[] }>();
</script>

<template>
    <section v-if="posts.length" class="mx-auto max-w-7xl px-4 py-14">
        <div class="mb-8 text-center">
            <h2 class="font-display text-3xl font-semibold text-ink-900">Suivez-nous sur Instagram</h2>
            <p class="mt-2 font-medium text-ink-400">@arielkids — nouveautés et coulisses de la boutique</p>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <a
                v-for="post in posts"
                :key="post.id"
                :href="post.permalink"
                target="_blank"
                rel="noopener"
                class="group relative aspect-square overflow-hidden rounded-2xl bg-ink-50"
            >
                <img
                    :src="post.media_url"
                    :alt="post.caption ?? 'Publication Instagram Ariel Kid\'s'"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                    loading="lazy"
                />
                <div class="absolute inset-0 flex items-center justify-center bg-ink-950/0 opacity-0 transition group-hover:bg-ink-950/40 group-hover:opacity-100">
                    <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z" />
                    </svg>
                </div>
            </a>
        </div>
    </section>
</template>
