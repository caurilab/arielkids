<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useCart } from '@/Composables/useCart';
import { useCurrency } from '@/Composables/useCurrency';

defineOptions({ layout: ShopLayout });

defineProps<{
    paymentMethods: { value: string; label: string }[];
}>();

const { cart } = useCart();
const { format } = useCurrency();

const form = useForm({
    name: '',
    phone: '',
    email: '',
    address: '',
    payment_method: 'mobile_money',
});

function submit() {
    form.post(route('checkout.store'));
}
</script>

<template>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <h1 class="font-display text-3xl font-semibold text-ink-900 sm:text-4xl">Finaliser la commande</h1>

        <form class="mt-8 grid gap-8 lg:grid-cols-[1fr_340px]" @submit.prevent="submit">
            <div class="space-y-6">
                <!-- Coordonnées -->
                <section class="rounded-3xl border border-ink-100 bg-white p-6">
                    <h2 class="font-display text-xl font-semibold text-ink-900">Vos coordonnées</h2>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                            Nom complet *
                            <input
                                v-model="form.name"
                                type="text"
                                autocomplete="name"
                                class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400"
                                placeholder="Awa Koné"
                            />
                            <span v-if="form.errors.name" class="text-xs text-brand-600">{{ form.errors.name }}</span>
                        </label>

                        <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                            Téléphone *
                            <input
                                v-model="form.phone"
                                type="tel"
                                autocomplete="tel"
                                class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400"
                                placeholder="07 00 00 00 00"
                            />
                            <span v-if="form.errors.phone" class="text-xs text-brand-600">{{ form.errors.phone }}</span>
                        </label>

                        <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700 sm:col-span-2">
                            E-mail (facultatif)
                            <input
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400"
                                placeholder="awa@exemple.ci"
                            />
                            <span v-if="form.errors.email" class="text-xs text-brand-600">{{ form.errors.email }}</span>
                        </label>

                        <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700 sm:col-span-2">
                            Adresse de livraison *
                            <textarea
                                v-model="form.address"
                                rows="3"
                                class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400"
                                placeholder="Cocody, rue des Jardins…"
                            />
                            <span v-if="form.errors.address" class="text-xs text-brand-600">{{ form.errors.address }}</span>
                        </label>
                    </div>
                </section>

                <!-- Paiement -->
                <section class="rounded-3xl border border-ink-100 bg-white p-6">
                    <h2 class="font-display text-xl font-semibold text-ink-900">Mode de paiement</h2>

                    <div class="mt-5 space-y-3">
                        <label
                            v-for="method in paymentMethods"
                            :key="method.value"
                            class="flex cursor-pointer items-start gap-4 rounded-2xl border-2 p-4 transition"
                            :class="form.payment_method === method.value
                                ? 'border-brand-500 bg-brand-50'
                                : 'border-ink-100 hover:border-brand-200'"
                        >
                            <input
                                v-model="form.payment_method"
                                type="radio"
                                :value="method.value"
                                class="mt-1 text-brand-500 focus:ring-brand-400"
                            />
                            <span>
                                <span class="block font-display font-semibold text-ink-900">{{ method.label }}</span>
                                <span v-if="method.value === 'mobile_money'" class="mt-0.5 block text-sm font-medium text-ink-500">
                                    Orange Money, MTN, Moov, Wave ou carte bancaire via CinetPay. Paiement immédiat et sécurisé.
                                </span>
                                <span v-else class="mt-0.5 block text-sm font-medium text-ink-500">
                                    Réglez en espèces ou par Mobile Money à la réception de votre colis.
                                </span>
                            </span>
                        </label>
                    </div>
                    <span v-if="form.errors.payment_method" class="mt-2 block text-xs text-brand-600">
                        {{ form.errors.payment_method }}
                    </span>
                </section>
            </div>

            <!-- Récapitulatif -->
            <aside class="h-fit rounded-3xl border border-ink-100 bg-ink-50/60 p-6 lg:sticky lg:top-28">
                <h2 class="font-display text-xl font-semibold text-ink-900">Votre commande</h2>

                <ul class="mt-4 space-y-3 text-sm">
                    <li v-for="item in cart.items" :key="item.product_id" class="flex justify-between gap-3 font-medium text-ink-600">
                        <span class="line-clamp-1">{{ item.quantity }} × {{ item.name }}</span>
                        <span class="shrink-0">{{ format(item.price * item.quantity) }}</span>
                    </li>
                </ul>

                <div class="mt-4 flex justify-between border-t border-ink-200 pt-4 font-display text-lg font-semibold text-ink-900">
                    <span>Total</span>
                    <span class="text-brand-600">{{ format(cart.total) }}</span>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="mt-6 w-full rounded-full bg-brand-500 px-6 py-3.5 font-display font-semibold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600 disabled:opacity-50"
                >
                    {{ form.processing ? 'Traitement…' : 'Confirmer la commande' }}
                </button>
                <p class="mt-3 text-center text-xs font-medium text-ink-400">
                    🔒 Vos informations restent confidentielles.
                </p>
            </aside>
        </form>
    </div>
</template>
