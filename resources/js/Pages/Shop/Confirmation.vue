<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import type { Order } from '@/types';
import { useCurrency } from '@/Composables/useCurrency';

defineOptions({ layout: ShopLayout });

const props = defineProps<{ order: Order }>();

const { format } = useCurrency();

const statusLabel: Record<Order['status'], string> = {
    en_attente: 'En attente',
    payee: 'Payée',
    en_preparation: 'En préparation',
    expediee: 'Expédiée',
    livree: 'Livrée',
    annulee: 'Annulée',
};

const paymentLabel: Record<Order['payment_status'], string> = {
    en_attente: 'En attente',
    paye: 'Payé',
    echoue: 'Échoué',
};
</script>

<template>
    <div class="mx-auto max-w-3xl px-4 py-14">
        <div class="rounded-[2.5rem] border border-ink-100 bg-white p-8 text-center sm:p-12">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-mint-100 text-4xl">
                🎉
            </div>

            <h1 class="mt-6 font-display text-3xl font-semibold text-ink-900">Merci {{ order.customer?.name }} !</h1>
            <p class="mt-2 font-medium text-ink-500">
                Votre commande <strong class="text-ink-900">{{ order.reference }}</strong> est bien enregistrée.
            </p>

            <div class="mt-8 grid gap-4 rounded-3xl bg-ink-50 p-6 text-left sm:grid-cols-2">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-ink-400">Statut</p>
                    <p class="mt-1 font-display font-semibold text-ink-900">{{ statusLabel[order.status] }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-ink-400">Paiement</p>
                    <p class="mt-1 font-display font-semibold text-ink-900">
                        {{ order.payment_method === 'mobile_money' ? 'Mobile Money' : 'À la livraison' }}
                        · {{ paymentLabel[order.payment_status] }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-ink-400">Livraison</p>
                    <p class="mt-1 font-medium text-ink-700">{{ order.customer?.address }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-ink-400">Contact</p>
                    <p class="mt-1 font-medium text-ink-700">{{ order.customer?.phone }}</p>
                </div>
            </div>

            <ul class="mt-6 space-y-2 text-left">
                <li
                    v-for="item in order.items"
                    :key="item.id"
                    class="flex justify-between rounded-2xl border border-ink-100 px-4 py-3 text-sm font-medium text-ink-700"
                >
                    <span>{{ item.quantity }} × {{ item.product_name }}</span>
                    <span>{{ format(item.unit_price * item.quantity) }}</span>
                </li>
            </ul>

            <div class="mt-4 flex justify-between px-2 font-display text-lg font-semibold text-ink-900">
                <span>Total</span>
                <span class="text-brand-600">{{ format(order.total) }}</span>
            </div>

            <p v-if="order.payment_method === 'livraison'" class="mt-6 rounded-2xl bg-peach-100 px-5 py-4 text-sm font-medium text-ink-700">
                💵 Vous réglerez à la réception du colis. Notre équipe vous contactera au
                <strong>{{ order.customer?.phone }}</strong> pour convenir de la livraison.
            </p>
            <p v-else class="mt-6 rounded-2xl bg-sky-100 px-5 py-4 text-sm font-medium text-ink-700">
                📱 Suivez les instructions Mobile Money pour finaliser votre paiement.
            </p>

            <Link
                :href="route('home')"
                class="mt-8 inline-block rounded-full bg-brand-500 px-8 py-3.5 font-display font-semibold text-white transition hover:bg-brand-600"
            >
                Retour à la boutique
            </Link>
        </div>
    </div>
</template>
