<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, ref } from 'vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import type { Product, SharedProps } from '@/types';
import { useCart } from '@/Composables/useCart';
import { useCurrency } from '@/Composables/useCurrency';

defineOptions({ layout: ShopLayout });

const props = defineProps<{
    product: Product;
    related: Product[];
}>();

const { add } = useCart();
const { format } = useCurrency();
const page = usePage<SharedProps>();

const activeImage = ref(0);
const quantity = ref(1);

const whatsappHref = computed(() => {
    const url = route('product.show', props.product.slug);
    const absolute = url.startsWith('http') ? url : `${window.location.origin}${url}`;
    const message = `Bonjour Ariel Kid's, je souhaite commander : ${props.product.name} (${format(props.product.price)})\n${absolute}`;
    return `https://wa.me/${page.props.whatsappNumber}?text=${encodeURIComponent(message)}`;
});

function addToCart() {
    add(props.product.id, quantity.value);
}
</script>

<template>
    <div class="mx-auto max-w-7xl px-4 py-10">
        <nav class="mb-8 flex items-center gap-2 text-sm font-medium text-ink-400">
            <Link :href="route('home')" class="hover:text-brand-600">Accueil</Link>
            <span>/</span>
            <Link
                v-if="product.category"
                :href="route('category.show', product.category.slug)"
                class="hover:text-brand-600"
            >
                {{ product.category.name }}
            </Link>
            <span>/</span>
            <span class="text-ink-800">{{ product.name }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-2">
            <!-- Galerie -->
            <div>
                <div class="overflow-hidden rounded-[2rem] bg-blush-100">
                    <img
                        v-if="product.media[activeImage]"
                        :src="product.media[activeImage].url"
                        :alt="product.media[activeImage].alt ?? product.name"
                        class="aspect-square w-full object-cover"
                    />
                </div>
                <div v-if="product.media.length > 1" class="mt-4 flex gap-3">
                    <button
                        v-for="(media, i) in product.media"
                        :key="media.id"
                        class="h-20 w-20 overflow-hidden rounded-2xl border-2 transition"
                        :class="i === activeImage ? 'border-brand-500' : 'border-transparent opacity-70 hover:opacity-100'"
                        @click="activeImage = i"
                    >
                        <img :src="media.url" :alt="media.alt ?? ''" class="h-full w-full object-cover" />
                    </button>
                </div>
            </div>

            <!-- Infos -->
            <div class="flex flex-col">
                <h1 class="font-display text-3xl font-semibold text-ink-900 sm:text-4xl">{{ product.name }}</h1>

                <p class="mt-4 font-display text-3xl font-semibold text-brand-600">
                    {{ format(product.price) }}
                </p>

                <p class="mt-2 text-sm font-semibold" :class="product.stock > 0 ? 'text-mint-500' : 'text-brand-600'">
                    {{ product.stock > 0 ? `✔ En stock (${product.stock} dispo)` : '✖ Rupture de stock' }}
                </p>

                <p v-if="product.description" class="mt-6 leading-relaxed text-ink-600">
                    {{ product.description }}
                </p>

                <div v-if="product.stock > 0" class="mt-8 flex flex-wrap items-center gap-4">
                    <div class="flex items-center rounded-full border border-ink-200">
                        <button
                            class="px-4 py-3 text-lg font-bold text-ink-600 transition hover:text-brand-600 disabled:opacity-30"
                            :disabled="quantity <= 1"
                            aria-label="Diminuer la quantité"
                            @click="quantity--"
                        >
                            −
                        </button>
                        <span class="w-10 text-center font-display font-semibold">{{ quantity }}</span>
                        <button
                            class="px-4 py-3 text-lg font-bold text-ink-600 transition hover:text-brand-600 disabled:opacity-30"
                            :disabled="quantity >= product.stock"
                            aria-label="Augmenter la quantité"
                            @click="quantity++"
                        >
                            +
                        </button>
                    </div>

                    <button
                        class="flex-1 rounded-full bg-brand-500 px-8 py-3.5 font-display font-semibold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600 sm:flex-none"
                        @click="addToCart"
                    >
                        Ajouter au panier
                    </button>
                </div>

                <a
                    :href="whatsappHref"
                    target="_blank"
                    rel="noopener"
                    class="mt-4 inline-flex w-fit items-center gap-2 rounded-full bg-[#25D366] px-6 py-3 font-display font-semibold text-white transition hover:brightness-95"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                    </svg>
                    Commander sur WhatsApp
                </a>

                <ul class="mt-8 space-y-2 border-t border-ink-100 pt-6 text-sm font-medium text-ink-500">
                    <li>🚚 Livraison rapide à Abidjan et dans toute la Côte d'Ivoire</li>
                    <li>🔒 Paiement sécurisé : Mobile Money ou à la livraison</li>
                    <li>↩️ Retours faciles sous 30 jours</li>
                    <li v-if="product.sku">Réf. {{ product.sku }}</li>
                </ul>
            </div>
        </div>

        <!-- Produits liés -->
        <section v-if="related.length" class="mt-16">
            <h2 class="mb-6 font-display text-2xl font-semibold text-ink-900">Vous aimerez aussi</h2>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
                <ProductCard v-for="item in related" :key="item.id" :product="item" />
            </div>
        </section>
    </div>
</template>
