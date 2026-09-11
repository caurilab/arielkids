<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import type { Category } from '@/types';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    tree: (Category & { products_count: number; children: (Category & { products_count: number })[] })[];
}>();

const editing = ref<Category | null>(null);
const creatingChildOf = ref<number | null | undefined>(undefined);

const form = useForm({
    parent_id: null as number | null,
    name: '',
    is_active: true,
});

function startCreate(parentId: number | null) {
    editing.value = null;
    creatingChildOf.value = parentId;
    form.reset();
    form.parent_id = parentId;
    form.is_active = true;
}

function startEdit(category: Category) {
    creatingChildOf.value = undefined;
    editing.value = category;
    form.parent_id = category.parent_id;
    form.name = category.name;
    form.is_active = category.is_active;
}

function cancel() {
    editing.value = null;
    creatingChildOf.value = undefined;
    form.reset();
}

function submit() {
    if (editing.value) {
        form.put(route('admin.categories.update', editing.value.id), { onSuccess: cancel });
    } else {
        form.post(route('admin.categories.store'), { onSuccess: cancel });
    }
}

function toggleActive(category: Category) {
    router.put(route('admin.categories.update', category.id), {
        parent_id: category.parent_id,
        name: category.name,
        is_active: !category.is_active,
    }, { preserveScroll: true });
}

function destroy(category: Category) {
    if (confirm(`Supprimer « ${category.name} » ?`)) {
        router.delete(route('admin.categories.destroy', category.id), { preserveScroll: true });
    }
}

function move(list: (Category & { products_count: number })[], index: number, direction: -1 | 1) {
    const ids = list.map((c) => c.id);
    const target = index + direction;
    if (target < 0 || target >= ids.length) return;
    [ids[index], ids[target]] = [ids[target], ids[index]];
    router.patch(route('admin.categories.reorder'), { ordered_ids: ids }, { preserveScroll: true });
}
</script>

<template>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="font-display text-2xl font-semibold text-ink-900">Catégories</h1>
            <button
                class="rounded-full bg-brand-500 px-5 py-2.5 font-display text-sm font-semibold text-white transition hover:bg-brand-600"
                @click="startCreate(null)"
            >
                + Nouveau rayon
            </button>
        </div>

        <!-- Formulaire -->
        <form
            v-if="editing || creatingChildOf !== undefined"
            class="mb-6 flex flex-wrap items-end gap-3 rounded-3xl border border-brand-200 bg-brand-50 p-5"
            @submit.prevent="submit"
        >
            <label class="flex flex-1 flex-col gap-1.5 text-sm font-semibold text-ink-700">
                Nom de la catégorie
                <input v-model="form.name" type="text" class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400" />
                <span v-if="form.errors.name" class="text-xs text-brand-600">{{ form.errors.name }}</span>
            </label>
            <label class="flex items-center gap-2 pb-2.5 text-sm font-semibold text-ink-700">
                <input v-model="form.is_active" type="checkbox" class="rounded border-ink-300 text-brand-500 focus:ring-brand-400" />
                Active
            </label>
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-full bg-brand-500 px-6 py-2.5 font-display text-sm font-semibold text-white hover:bg-brand-600 disabled:opacity-50"
            >
                {{ editing ? 'Enregistrer' : 'Créer' }}
            </button>
            <button type="button" class="rounded-full px-4 py-2.5 text-sm font-semibold text-ink-500 hover:text-brand-600" @click="cancel">
                Annuler
            </button>
        </form>

        <!-- Arbre -->
        <div class="space-y-4">
            <section
                v-for="(parent, pi) in tree"
                :key="parent.id"
                class="rounded-3xl border border-ink-100 bg-white"
            >
                <div class="flex items-center gap-2 border-b border-ink-100 px-5 py-3.5">
                    <span class="font-display font-semibold text-ink-900">{{ parent.name }}</span>
                    <span class="rounded-full bg-ink-100 px-2.5 py-0.5 text-xs font-bold text-ink-500">
                        {{ parent.products_count }} produit{{ parent.products_count > 1 ? 's' : '' }}
                    </span>
                    <span
                        class="rounded-full px-2.5 py-0.5 text-xs font-bold"
                        :class="parent.is_active ? 'bg-mint-100 text-mint-500' : 'bg-ink-100 text-ink-400'"
                    >
                        {{ parent.is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <div class="ml-auto flex items-center gap-1 text-sm">
                        <button class="rounded-lg px-2 py-1 hover:bg-ink-50 disabled:opacity-30" :disabled="pi === 0" title="Monter" @click="move(tree, pi, -1)">↑</button>
                        <button class="rounded-lg px-2 py-1 hover:bg-ink-50 disabled:opacity-30" :disabled="pi === tree.length - 1" title="Descendre" @click="move(tree, pi, 1)">↓</button>
                        <button class="rounded-lg px-2 py-1 font-semibold text-brand-600 hover:bg-brand-50" @click="startEdit(parent)">Renommer</button>
                        <button class="rounded-lg px-2 py-1 font-semibold text-ink-500 hover:bg-ink-50" @click="toggleActive(parent)">
                            {{ parent.is_active ? 'Désactiver' : 'Activer' }}
                        </button>
                        <button class="rounded-lg px-2 py-1 font-semibold text-ink-400 hover:text-brand-600" @click="destroy(parent)">Supprimer</button>
                    </div>
                </div>

                <ul class="divide-y divide-ink-50">
                    <li
                        v-for="(child, ci) in parent.children"
                        :key="child.id"
                        class="flex items-center gap-2 px-5 py-2.5 pl-8"
                    >
                        <span class="text-ink-300">└</span>
                        <span class="text-sm font-semibold text-ink-800">{{ child.name }}</span>
                        <span class="text-xs font-medium text-ink-400">
                            {{ child.products_count }} produit{{ child.products_count > 1 ? 's' : '' }}
                        </span>
                        <div class="ml-auto flex items-center gap-1 text-sm">
                            <button class="rounded-lg px-2 py-1 hover:bg-ink-50 disabled:opacity-30" :disabled="ci === 0" title="Monter" @click="move(parent.children, ci, -1)">↑</button>
                            <button class="rounded-lg px-2 py-1 hover:bg-ink-50 disabled:opacity-30" :disabled="ci === parent.children.length - 1" title="Descendre" @click="move(parent.children, ci, 1)">↓</button>
                            <button class="rounded-lg px-2 py-1 font-semibold text-brand-600 hover:bg-brand-50" @click="startEdit(child)">Renommer</button>
                            <button class="rounded-lg px-2 py-1 font-semibold text-ink-500 hover:bg-ink-50" @click="toggleActive(child)">
                                {{ child.is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                            <button class="rounded-lg px-2 py-1 font-semibold text-ink-400 hover:text-brand-600" @click="destroy(child)">Supprimer</button>
                        </div>
                    </li>
                    <li class="px-5 py-2.5 pl-8">
                        <button
                            class="text-sm font-semibold text-brand-500 hover:text-brand-600"
                            @click="startCreate(parent.id)"
                        >
                            + Ajouter une sous-catégorie
                        </button>
                    </li>
                </ul>
            </section>
        </div>
    </div>
</template>
