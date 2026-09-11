<script setup lang="ts">
import { onMounted, ref } from 'vue';

interface LookbookVideo {
    src: string;
    poster: string;
    title: string;
}

const videos: LookbookVideo[] = [
    { src: '/videos/lookbook-1.mp4', poster: '/videos/lookbook-1.jpg', title: 'Suède marron' },
    { src: '/videos/lookbook-2.mp4', poster: '/videos/lookbook-2.jpg', title: 'Rouge & lilas' },
    { src: '/videos/lookbook-3.mp4', poster: '/videos/lookbook-3.jpg', title: 'Sarcelle & jaune' },
    { src: '/videos/lookbook-4.mp4', poster: '/videos/lookbook-4.jpg', title: 'Violet profond' },
    { src: '/videos/lookbook-5.mp4', poster: '/videos/lookbook-5.jpg', title: 'Vert forêt' },
];

const els = ref<HTMLVideoElement[]>([]);

// Lecture automatique uniquement quand la vidéo est visible (économie de données mobile).
onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                const video = entry.target as HTMLVideoElement;
                if (entry.isIntersecting) {
                    video.play().catch(() => {});
                } else {
                    video.pause();
                }
            }
        },
        { threshold: 0.4 },
    );

    els.value.forEach((v) => observer.observe(v));
});
</script>

<template>
    <section class="mx-auto max-w-7xl px-4 py-12">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <span class="font-display text-sm font-bold uppercase tracking-widest text-azure-500">
                    En vidéo
                </span>
                <h2 class="mt-1 font-display text-3xl font-semibold text-ink-900">Le lookbook</h2>
                <p class="mt-1 font-medium text-ink-400">Nos modèles filmés en boutique, sous tous les angles</p>
            </div>
            <span class="hidden text-sm font-semibold text-ink-400 sm:inline">Faites défiler →</span>
        </div>

        <div class="no-scrollbar -mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2">
            <figure
                v-for="(video, i) in videos"
                :key="video.src"
                class="group relative w-56 shrink-0 snap-start overflow-hidden rounded-3xl bg-ink-950 sm:w-64"
            >
                <video
                    :ref="(el) => { if (el) els[i] = el as HTMLVideoElement; }"
                    :src="video.src"
                    :poster="video.poster"
                    class="aspect-[4/5] w-full object-cover"
                    muted
                    loop
                    playsinline
                    preload="metadata"
                />
                <figcaption class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink-950/80 to-transparent px-4 pb-3 pt-10">
                    <span class="font-display text-sm font-semibold text-white">{{ video.title }}</span>
                </figcaption>
            </figure>
        </div>
    </section>
</template>
