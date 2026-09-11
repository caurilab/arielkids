<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import type { Category, Product } from '@/types';
import { useCurrency } from '@/Composables/useCurrency';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    product: (Product & { media: { id: number; url: string; alt: string | null }[] }) | null;
    categories: (Category & { parent?: { id: number; name: string } | null })[];
    audiences: { value: string; label: string }[];
}>();

const isEdit = computed(() => props.product !== null);
const { format } = useCurrency();

const form = useForm<{
    category_id: number | null;
    name: string;
    slug: string;
    description: string;
    price: number | null;
    audience: string;
    sku: string;
    stock: number;
    is_active: boolean;
    is_featured: boolean;
    images: File[];
    delete_media: number[];
}>({
    category_id: props.product?.category_id ?? null,
    name: props.product?.name ?? '',
    slug: props.product?.slug ?? '',
    description: props.product?.description ?? '',
    price: props.product?.price ?? null,
    audience: props.product?.audience ?? 'enfant_fille',
    sku: props.product?.sku ?? '',
    stock: props.product?.stock ?? 0,
    is_active: props.product?.is_active ?? true,
    is_featured: props.product?.is_featured ?? false,
    images: [],
    delete_media: [],
});

const previews = ref<string[]>([]);

function onImages(event: Event) {
    const files = Array.from((event.target as HTMLInputElement).files ?? []);
    form.images = files;
    previews.value = files.map((f) => URL.createObjectURL(f));
}

function markForDeletion(mediaId: number) {
    const i = form.delete_media.indexOf(mediaId);
    if (i === -1) form.delete_media.push(mediaId);
    else form.delete_media.splice(i, 1);
}

function submit() {
    if (isEdit.value && props.product) {
        form.post(route('admin.products.update', props.product.id) + '?_method=PUT', {
            forceFormData: true,
        });
    } else {
        form.post(route('admin.products.store'), { forceFormData: true });
    }
}
</script>

<template>
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center gap-4">
            <Link :href="route('admin.products.index')" class="text-sm font-semibold text-ink-400 hover:text-brand-600">
                ← Produits
            </Link>
            <h1 class="font-display text-2xl font-semibold text-ink-900">
                {{ isEdit ? `Modifier « ${product!.name} »` : 'Nouveau produit' }}
            </h1>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <section class="rounded-3xl border border-ink-100 bg-white p-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700 sm:col-span-2">
                        Nom *
                        <input v-model="form.name" type="text" class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400" />
                        <span v-if="form.errors.name" class="text-xs text-brand-600">{{ form.errors.name }}</span>
                    </label>

                    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                        Catégorie *
                        <select v-model="form.category_id" class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400">
                            <option :value="null" disabled>Choisir…</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.parent ? `${cat.parent.name} › ` : '' }}{{ cat.name }}
                            </option>
                        </select>
                        <span v-if="form.errors.category_id" class="text-xs text-brand-600">{{ form.errors.category_id }}</span>
                    </label>

                    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                        Public *
                        <select v-model="form.audience" class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400">
                            <option v-for="a in audiences" :key="a.value" :value="a.value">{{ a.label }}</option>
                        </select>
                    </label>

                    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                        Prix (FCFA, sans décimales) *
                        <input v-model.number="form.price" type="number" min="0" step="1" class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400" />
                        <span v-if="form.price" class="text-xs font-medium text-ink-400">≈ {{ format(form.price) }}</span>
                        <span v-if="form.errors.price" class="text-xs text-brand-600">{{ form.errors.price }}</span>
                    </label>

                    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                        Stock *
                        <input v-model.number="form.stock" type="number" min="0" class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400" />
                        <span v-if="form.errors.stock" class="text-xs text-brand-600">{{ form.errors.stock }}</span>
                    </label>

                    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                        Référence (SKU)
                        <input v-model="form.sku" type="text" class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400" placeholder="AK-XXXXXX" />
                    </label>

                    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                        Slug (URL)
                        <input v-model="form.slug" type="text" class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400" placeholder="généré automatiquement" />
                        <span v-if="form.errors.slug" class="text-xs text-brand-600">{{ form.errors.slug }}</span>
                    </label>

                    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700 sm:col-span-2">
                        Description
                        <textarea v-model="form.description" rows="4" class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400" />
                    </label>

                    <div class="flex gap-6 sm:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-semibold text-ink-700">
                            <input v-model="form.is_active" type="checkbox" class="rounded border-ink-300 text-brand-500 focus:ring-brand-400" />
                            Actif (visible en boutique)
                        </label>
                        <label class="flex items-center gap-2 text-sm font-semibold text-ink-700">
                            <input v-model="form.is_featured" type="checkbox" class="rounded border-ink-300 text-brand-500 focus:ring-brand-400" />
                            ★ Coup de cœur (page d'accueil)
                        </label>
                    </div>
                </div>
            </section>

            <!-- Images -->
            <section class="rounded-3xl border border-ink-100 bg-white p-6">
                <h2 class="font-display text-lg font-semibold text-ink-900">Images</h2>

                <div v-if="product?.media.length" class="mt-4 flex flex-wrap gap-3">
                    <div v-for="media in product.media" :key="media.id" class="relative">
                        <img
                            :src="media.url"
                            :alt="media.alt ?? ''"
                            class="h-24 w-24 rounded-2xl object-cover"
                            :class="{ 'opacity-40': form.delete_media.includes(media.id) }"
                        />
                        <button
                            type="button"
                            class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold text-white"
                            :class="form.delete_media.includes(media.id) ? 'bg-ink-500' : 'bg-brand-500'"
                            :title="form.delete_media.includes(media.id) ? 'Annuler' : 'Supprimer'"
                            @click="markForDeletion(media.id)"
                        >
                            {{ form.delete_media.includes(media.id) ? '↺' : '×' }}
                        </button>
                    </div>
                </div>

                <label class="mt-4 flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-ink-200 px-6 py-8 text-center transition hover:border-brand-400 hover:bg-brand-50">
                    <span class="text-2xl">🖼️</span>
                    <span class="text-sm font-semibold text-ink-600">Ajouter des images (JPG, PNG, WebP — 4 Mo max)</span>
                    <input type="file" accept="image/*" multiple class="hidden" @change="onImages" />
                </label>
                <span v-if="form.errors.images" class="text-xs text-brand-600">{{ form.errors.images }}</span>

                <div v-if="previews.length" class="mt-4 flex flex-wrap gap-3">
                    <img v-for="(src, i) in previews" :key="i" :src="src" class="h-24 w-24 rounded-2xl object-cover" alt="Aperçu" />
                </div>
            </section>

            <div class="flex justify-end gap-3">
                <Link
                    :href="route('admin.products.index')"
                    class="rounded-full border border-ink-200 px-6 py-3 font-display font-semibold text-ink-600 transition hover:bg-ink-50"
                >
                    Annuler
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-full bg-brand-500 px-8 py-3 font-display font-semibold text-white transition hover:bg-brand-600 disabled:opacity-50"
                >
                    {{ form.processing ? 'Enregistrement…' : isEdit ? 'Enregistrer' : 'Créer le produit' }}
                </button>
            </div>
        </form>
    </div>
</template>
