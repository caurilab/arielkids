<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import type { Order } from '@/types';
import { useCurrency } from '@/Composables/useCurrency';

defineOptions({ layout: AdminLayout });

defineProps<{
    stats: {
        orders_total: number;
        revenue: number;
        orders_pending: number;
        products_active: number;
        products_out_of_stock: number;
    };
    recentOrders: Order[];
}>();

const { format } = useCurrency();

const statusLabel: Record<Order['status'], string> = {
    en_attente: 'En attente',
    payee: 'Payée',
    en_preparation: 'En préparation',
    expediee: 'Expédiée',
    livree: 'Livrée',
    annulee: 'Annulée',
};

const statusClass: Record<Order['status'], string> = {
    en_attente: 'bg-sun-400/20 text-sun-500',
    payee: 'bg-sky-100 text-ink-600',
    en_preparation: 'bg-lav-100 text-ink-600',
    expediee: 'bg-mint-100 text-mint-500',
    livree: 'bg-mint-100 text-mint-500',
    annulee: 'bg-brand-100 text-brand-600',
};
</script>

<template>
    <div>
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
            <div class="rounded-3xl border border-ink-100 bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-ink-400">Commandes</p>
                <p class="mt-2 font-display text-3xl font-semibold text-ink-900">{{ stats.orders_total }}</p>
            </div>
            <div class="rounded-3xl border border-ink-100 bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-ink-400">Chiffre d'affaires</p>
                <p class="mt-2 font-display text-2xl font-semibold text-brand-600">{{ format(stats.revenue) }}</p>
            </div>
            <div class="rounded-3xl border border-ink-100 bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-ink-400">En attente</p>
                <p class="mt-2 font-display text-3xl font-semibold text-sun-500">{{ stats.orders_pending }}</p>
            </div>
            <div class="rounded-3xl border border-ink-100 bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-ink-400">Produits actifs</p>
                <p class="mt-2 font-display text-3xl font-semibold text-mint-500">{{ stats.products_active }}</p>
            </div>
            <div class="rounded-3xl border border-ink-100 bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-ink-400">Ruptures</p>
                <p class="mt-2 font-display text-3xl font-semibold text-ink-900">{{ stats.products_out_of_stock }}</p>
            </div>
        </div>

        <section class="mt-8 rounded-3xl border border-ink-100 bg-white">
            <div class="flex items-center justify-between border-b border-ink-100 px-6 py-4">
                <h2 class="font-display text-lg font-semibold text-ink-900">Dernières commandes</h2>
                <Link :href="route('admin.orders.index')" class="text-sm font-semibold text-brand-600 hover:text-brand-700">
                    Tout voir →
                </Link>
            </div>

            <div v-if="recentOrders.length" class="divide-y divide-ink-100">
                <Link
                    v-for="order in recentOrders"
                    :key="order.id"
                    :href="route('admin.orders.show', order.id)"
                    class="flex flex-wrap items-center gap-3 px-6 py-3.5 transition hover:bg-ink-50"
                >
                    <span class="font-display font-semibold text-ink-900">{{ order.reference }}</span>
                    <span class="text-sm font-medium text-ink-500">{{ order.customer?.name }}</span>
                    <span class="ml-auto font-display font-semibold text-ink-900">{{ format(order.total) }}</span>
                    <span class="rounded-full px-3 py-1 text-xs font-bold" :class="statusClass[order.status]">
                        {{ statusLabel[order.status] }}
                    </span>
                </Link>
            </div>
            <p v-else class="px-6 py-10 text-center text-sm font-medium text-ink-400">
                Aucune commande pour le moment.
            </p>
        </section>
    </div>
</template>
