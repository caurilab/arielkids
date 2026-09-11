<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useCart } from '@/Composables/useCart';
import { useCurrency } from '@/Composables/useCurrency';

defineOptions({ layout: ShopLayout });

const { cart, update, remove } = useCart();
const { format } = useCurrency();
</script>

<template>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <h1 class="font-display text-3xl font-semibold text-ink-900 sm:text-4xl">Votre panier</h1>

        <div v-if="cart.items.length" class="mt-8 grid gap-8 lg:grid-cols-[1fr_340px]">
            <!-- Articles -->
            <ul class="space-y-4">
                <li
                    v-for="item in cart.items"
                    :key="item.product_id"
                    class="flex gap-4 rounded-3xl border border-ink-100 bg-white p-4"
                >
                    <Link :href="route('product.show', item.slug)" class="h-24 w-24 shrink-0 overflow-hidden rounded-2xl bg-blush-100">
                        <img v-if="item.image" :src="item.image" :alt="item.name" class="h-full w-full object-cover" />
                    </Link>

                    <div class="flex flex-1 flex-col">
                        <Link :href="route('product.show', item.slug)" class="font-display font-semibold text-ink-900 hover:text-brand-600">
                            {{ item.name }}
                        </Link>
                        <span class="mt-1 text-sm font-semibold text-brand-600">{{ format(item.price) }}</span>

                        <div class="mt-auto flex items-center justify-between pt-2">
                            <div class="flex items-center rounded-full border border-ink-200">
                                <button
                                    class="px-3 py-1.5 font-bold text-ink-600 hover:text-brand-600"
                                    aria-label="Diminuer"
                                    @click="update(item.product_id, item.quantity - 1)"
                                >
                                    −
                                </button>
                                <span class="w-8 text-center text-sm font-semibold">{{ item.quantity }}</span>
                                <button
                                    class="px-3 py-1.5 font-bold text-ink-600 hover:text-brand-600 disabled:opacity-30"
                                    :disabled="item.quantity >= item.stock"
                                    aria-label="Augmenter"
                                    @click="update(item.product_id, item.quantity + 1)"
                                >
                                    +
                                </button>
                            </div>

                            <button
                                class="text-sm font-semibold text-ink-400 transition hover:text-brand-600"
                                @click="remove(item.product_id)"
                            >
                                Retirer
                            </button>
                        </div>
                    </div>
                </li>
            </ul>

            <!-- Récapitulatif -->
            <aside class="h-fit rounded-3xl border border-ink-100 bg-ink-50/60 p-6 lg:sticky lg:top-28">
                <h2 class="font-display text-xl font-semibold text-ink-900">Récapitulatif</h2>
                <dl class="mt-4 space-y-2 text-sm font-medium text-ink-600">
                    <div class="flex justify-between">
                        <dt>Articles ({{ cart.count }})</dt>
                        <dd>{{ format(cart.total) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Livraison</dt>
                        <dd>Calculée à la commande</dd>
                    </div>
                </dl>
                <div class="mt-4 flex justify-between border-t border-ink-200 pt-4 font-display text-lg font-semibold text-ink-900">
                    <span>Total</span>
                    <span class="text-brand-600">{{ format(cart.total) }}</span>
                </div>

                <Link
                    :href="route('checkout.index')"
                    class="mt-6 block rounded-full bg-brand-500 px-6 py-3.5 text-center font-display font-semibold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600"
                >
                    Passer commande →
                </Link>
                <Link :href="route('home')" class="mt-3 block text-center text-sm font-semibold text-ink-500 hover:text-brand-600">
                    Continuer mes achats
                </Link>
            </aside>
        </div>

        <div v-else class="mt-10 rounded-3xl bg-ink-50 px-6 py-20 text-center">
            <p class="text-5xl">🛒</p>
            <p class="mt-4 font-display text-2xl font-semibold text-ink-800">Votre panier est vide</p>
            <p class="mt-2 font-medium text-ink-400">Parcourez la boutique pour trouver votre bonheur.</p>
            <Link
                :href="route('home')"
                class="mt-6 inline-block rounded-full bg-brand-500 px-8 py-3 font-display font-semibold text-white transition hover:bg-brand-600"
            >
                Découvrir la boutique
            </Link>
        </div>
    </div>
</template>
