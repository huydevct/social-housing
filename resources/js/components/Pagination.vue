<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: { url: string | null; label: string; active: boolean }[];
}>();
</script>

<template>
    <nav v-if="links.length > 3" class="mt-6 flex flex-wrap justify-center gap-1">
        <template v-for="(link, i) in links" :key="i">
            <span
                v-if="!link.url"
                class="rounded px-3 py-1.5 text-sm text-slate-400"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                preserve-scroll
                class="rounded px-3 py-1.5 text-sm"
                :class="link.active ? 'bg-emerald-600 text-white' : 'text-slate-700 hover:bg-slate-200'"
            >
                <span v-html="link.label" />
            </Link>
        </template>
    </nav>
</template>
