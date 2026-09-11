<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import type { Order, Paginated } from '@/types';
import { useCurrency } from '@/Composables/useCurrency';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    orders: Paginated<Order>;
    filters: { status: string | null };
    statuses: { value: string; label: string }[];
}>();

const { format } = useCurrency();
const status = ref(props.filters.status ?? '');

watch(status, (value) => {
    router.get(route('admin.orders.index'), { status: value || undefined }, { preserveState: true, replace: true });
});

const statusClass: Record<Order['status'], string> = {
    en_attente: 'bg-sun-400/20 text-sun-500',
    payee: 'bg-sky-100 text-ink-600',
    en_preparation: 'bg-lav-100 text-ink-600',
    expediee: 'bg-mint-100 text-mint-500',
    livree: 'bg-mint-100 text-mint-500',
    annulee: 'bg-brand-100 text-brand-600',
};

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
    <div>
        <div class="mb-6 flex flex-wrap items-center gap-4">
            <h1 class="font-display text-2xl font-semibold text-ink-900">Commandes</h1>
            <select v-model="status" class="ml-auto rounded-xl border-ink-200 text-sm focus:border-brand-400 focus:ring-brand-400">
                <option value="">Tous les statuts</option>
                <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>
        </div>

        <div class="overflow-hidden rounded-3xl border border-ink-100 bg-white">
            <table class="w-full min-w-[820px] text-sm">
                <thead>
                    <tr class="border-b border-ink-100 text-left text-xs font-bold uppercase tracking-wide text-ink-400">
                        <th class="px-5 py-3.5">Référence</th>
                        <th class="px-5 py-3.5">Client</th>
                        <th class="px-5 py-3.5">Total</th>
                        <th class="px-5 py-3.5">Paiement</th>
                        <th class="px-5 py-3.5">Statut</th>
                        <th class="px-5 py-3.5">Date</th>
                        <th class="px-5 py-3.5" />
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    <tr v-for="order in orders.data" :key="order.id" class="hover:bg-ink-50/60">
                        <td class="px-5 py-3 font-display font-semibold text-ink-900">{{ order.reference }}</td>
                        <td class="px-5 py-3">
                            <p class="font-semibold text-ink-800">{{ order.customer?.name }}</p>
                            <p class="text-xs text-ink-400">{{ order.customer?.phone }}</p>
                        </td>
                        <td class="px-5 py-3 font-semibold text-ink-900">{{ format(order.total) }}</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-ink-600">
                                {{ order.payment_method === 'mobile_money' ? 'Mobile Money' : 'À la livraison' }}
                            </p>
                            <p class="text-xs text-ink-400">{{ paymentLabel[order.payment_status] }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-bold" :class="statusClass[order.status]">
                                {{ statusLabel[order.status] }}
                            </span>
                        </td>
                        <td class="px-5 py-3 font-medium text-ink-500">
                            {{ order.created_at ? new Date(order.created_at).toLocaleDateString('fr-FR') : '—' }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <Link :href="route('admin.orders.show', order.id)" class="font-semibold text-brand-600 hover:text-brand-700">
                                Détail →
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p v-if="!orders.data.length" class="px-6 py-12 text-center text-sm font-medium text-ink-400">
                Aucune commande pour ce filtre.
            </p>
        </div>

        <Pagination :paginated="orders" />
    </div>
</template>
