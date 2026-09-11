<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import type { Order } from '@/types';
import { useCurrency } from '@/Composables/useCurrency';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    order: Order;
    statuses: { value: string; label: string }[];
    paymentStatuses: { value: string; label: string }[];
}>();

const { format } = useCurrency();

function setStatus(value: string) {
    router.patch(route('admin.orders.update', props.order.id), { status: value }, { preserveScroll: true });
}

function setPaymentStatus(value: string) {
    router.patch(route('admin.orders.update', props.order.id), { payment_status: value }, { preserveScroll: true });
}
</script>

<template>
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex flex-wrap items-center gap-4">
            <Link :href="route('admin.orders.index')" class="text-sm font-semibold text-ink-400 hover:text-brand-600">
                ← Commandes
            </Link>
            <h1 class="font-display text-2xl font-semibold text-ink-900">{{ order.reference }}</h1>
            <span class="text-sm font-medium text-ink-400">
                {{ order.created_at ? new Date(order.created_at).toLocaleString('fr-FR') : '' }}
            </span>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Client -->
            <section class="rounded-3xl border border-ink-100 bg-white p-6">
                <h2 class="font-display text-lg font-semibold text-ink-900">Client</h2>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="font-medium text-ink-400">Nom</dt><dd class="font-semibold text-ink-900">{{ order.customer?.name }}</dd></div>
                    <div class="flex justify-between"><dt class="font-medium text-ink-400">Téléphone</dt><dd class="font-semibold text-ink-900">{{ order.customer?.phone }}</dd></div>
                    <div v-if="order.customer?.email" class="flex justify-between"><dt class="font-medium text-ink-400">E-mail</dt><dd class="font-semibold text-ink-900">{{ order.customer.email }}</dd></div>
                    <div class="flex justify-between gap-6"><dt class="font-medium text-ink-400">Adresse</dt><dd class="text-right font-semibold text-ink-900">{{ order.customer?.address }}</dd></div>
                </dl>
            </section>

            <!-- Suivi -->
            <section class="rounded-3xl border border-ink-100 bg-white p-6">
                <h2 class="font-display text-lg font-semibold text-ink-900">Suivi</h2>
                <div class="mt-4 space-y-4">
                    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                        Statut de la commande
                        <select :value="order.status" class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400" @change="setStatus(($event.target as HTMLSelectElement).value)">
                            <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                        </select>
                    </label>
                    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                        Statut du paiement
                        <select :value="order.payment_status" class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400" @change="setPaymentStatus(($event.target as HTMLSelectElement).value)">
                            <option v-for="s in paymentStatuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                        </select>
                    </label>
                    <p class="text-sm font-medium text-ink-500">
                        Mode : <strong>{{ order.payment_method === 'mobile_money' ? 'Mobile Money (CinetPay)' : 'Paiement à la livraison' }}</strong>
                    </p>
                    <p v-if="order.cinetpay_transaction_id" class="text-xs font-medium text-ink-400">
                        Transaction CinetPay : {{ order.cinetpay_transaction_id }}
                    </p>
                </div>
            </section>
        </div>

        <!-- Articles -->
        <section class="mt-6 rounded-3xl border border-ink-100 bg-white p-6">
            <h2 class="font-display text-lg font-semibold text-ink-900">Articles</h2>
            <ul class="mt-4 divide-y divide-ink-100">
                <li v-for="item in order.items" :key="item.id" class="flex items-center justify-between gap-4 py-3 text-sm">
                    <span class="font-semibold text-ink-800">{{ item.product_name }}</span>
                    <span class="font-medium text-ink-500">{{ item.quantity }} × {{ format(item.unit_price) }}</span>
                    <span class="font-display font-semibold text-ink-900">{{ format(item.unit_price * item.quantity) }}</span>
                </li>
            </ul>
            <div class="mt-4 flex justify-end gap-8 border-t border-ink-100 pt-4 font-display text-lg font-semibold text-ink-900">
                <span>Total</span>
                <span class="text-brand-600">{{ format(order.total) }}</span>
            </div>
        </section>
    </div>
</template>
