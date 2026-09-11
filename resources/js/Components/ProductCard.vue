<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import type { Product } from '@/types';
import { useCart } from '@/Composables/useCart';
import { useCurrency } from '@/Composables/useCurrency';

const props = defineProps<{ product: Product }>();

const { add } = useCart();
const { format } = useCurrency();

const audienceLabel: Record<Product['audience'], string> = {
    enfant_fille: 'Fille',
    enfant_garcon: 'Garçon',
    bebe: 'Bébé',
    femme: 'Femme',
    homme: 'Homme',
};

const pastelBg: Record<Product['audience'], string> = {
    enfant_fille: 'bg-blush-100',
    enfant_garcon: 'bg-sky-100',
    bebe: 'bg-mint-100',
    femme: 'bg-lav-100',
    homme: 'bg-peach-100',
};
</script>

<template>
    <div class="group flex flex-col overflow-hidden rounded-3xl border border-ink-100 bg-white transition hover:-translate-y-1 hover:shadow-xl hover:shadow-ink-900/10">
        <Link :href="route('product.show', product.slug)" class="relative block aspect-square overflow-hidden" :class="pastelBg[product.audience]">
            <img
                v-if="product.media[0]"
                :src="product.media[0].url"
                :alt="product.media[0].alt ?? product.name"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                loading="lazy"
            />
            <span
                v-if="product.is_featured"
                class="absolute left-3 top-3 rounded-full bg-brand-500 px-3 py-1 text-xs font-bold text-white"
            >
                Coup de cœur
            </span>
            <span
                v-if="product.stock === 0"
                class="absolute right-3 top-3 rounded-full bg-ink-900 px-3 py-1 text-xs font-bold text-white"
            >
                Épuisé
            </span>
        </Link>

        <div class="flex flex-1 flex-col gap-1 p-4">
            <span class="text-xs font-semibold uppercase tracking-wide text-ink-400">
                {{ audienceLabel[product.audience] }}
            </span>
            <Link :href="route('product.show', product.slug)" class="font-display font-semibold leading-snug text-ink-900 transition hover:text-brand-600">
                {{ product.name }}
            </Link>

            <div class="mt-auto flex items-center justify-between pt-3">
                <span class="font-display text-lg font-semibold text-brand-600">
                    {{ format(product.price) }}
                </span>
                <button
                    v-if="product.stock > 0"
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-ink-900 text-white transition hover:bg-brand-500"
                    :aria-label="`Ajouter ${product.name} au panier`"
                    @click="add(product.id)"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
