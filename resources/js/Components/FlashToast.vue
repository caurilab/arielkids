<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import type { SharedProps } from '@/types';

const page = usePage<SharedProps>();
const message = ref<string | null>(null);
const kind = ref<'success' | 'error'>('success');
let timer: ReturnType<typeof setTimeout> | undefined;

const flash = computed(() => page.props.flash);

watch(
    flash,
    (value) => {
        const text = value?.success ?? value?.error ?? null;
        if (!text) return;

        kind.value = value?.success ? 'success' : 'error';
        message.value = text;

        clearTimeout(timer);
        timer = setTimeout(() => (message.value = null), 3500);
    },
    { immediate: true, deep: true },
);
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-y-4 opacity-0"
            leave-active-class="transition duration-200 ease-in"
            leave-to-class="translate-y-4 opacity-0"
        >
            <div
                v-if="message"
                class="fixed bottom-24 right-5 z-50 flex max-w-sm items-center gap-3 rounded-2xl px-5 py-4 text-sm font-semibold text-white shadow-xl"
                :class="kind === 'success' ? 'bg-mint-500' : 'bg-brand-600'"
                role="status"
            >
                <span>{{ kind === 'success' ? '✅' : '⚠️' }}</span>
                {{ message }}
            </div>
        </Transition>
    </Teleport>
</template>
