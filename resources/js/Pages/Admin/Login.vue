<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('admin.login.store'));
}
</script>

<template>
    <Head title="Connexion admin" />

    <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-azure-100 via-mint-100 to-peach-100 px-4">
        <div class="w-full max-w-md rounded-[2rem] bg-white p-8 shadow-2xl shadow-brand-500/10">
            <div class="text-center">
                <img src="/images/brand/logo-black.png" alt="Ariel Kids" class="mx-auto h-20 w-auto" />
                <h1 class="mt-4 font-display text-2xl font-semibold text-ink-900">Back-office Ariel Kids</h1>
                <p class="mt-1 text-sm font-medium text-ink-400">Connectez-vous pour gérer la boutique</p>
            </div>

            <form class="mt-8 space-y-4" @submit.prevent="submit">
                <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                    E-mail
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="username"
                        class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400"
                        placeholder="admin@arielkids.ci"
                    />
                    <span v-if="form.errors.email" class="text-xs text-brand-600">{{ form.errors.email }}</span>
                </label>

                <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
                    Mot de passe
                    <input
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        class="rounded-xl border-ink-200 focus:border-brand-400 focus:ring-brand-400"
                        placeholder="••••••••"
                    />
                    <span v-if="form.errors.password" class="text-xs text-brand-600">{{ form.errors.password }}</span>
                </label>

                <label class="flex items-center gap-2 text-sm font-medium text-ink-600">
                    <input v-model="form.remember" type="checkbox" class="rounded border-ink-300 text-brand-500 focus:ring-brand-400" />
                    Rester connecté
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-full bg-brand-500 px-6 py-3.5 font-display font-semibold text-white transition hover:bg-brand-600 disabled:opacity-50"
                >
                    {{ form.processing ? 'Connexion…' : 'Se connecter' }}
                </button>
            </form>
        </div>
    </div>
</template>
