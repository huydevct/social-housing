<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppHead from '@/components/AppHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { index as guidesIndex } from '@/routes/guides';

const props = defineProps<{
    guide: {
        title: string;
        slug: string;
        excerpt: string | null;
        body: string;
        meta_title: string | null;
        meta_description: string | null;
        updated_at: string | null;
    };
}>();

const appUrl = computed(() => (usePage().props.appUrl as string | undefined)?.replace(/\/$/, '') ?? '');

const jsonLd = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: props.guide.title,
    description: props.guide.meta_description ?? props.guide.excerpt ?? undefined,
    url: appUrl.value + guidesIndex.url() + '/' + props.guide.slug,
    ...(props.guide.updated_at ? { dateModified: props.guide.updated_at } : {}),
}));
</script>

<template>
    <AppHead
        :title="guide.meta_title ?? guide.title"
        :description="guide.meta_description ?? guide.excerpt ?? undefined"
        :json-ld="jsonLd"
    />

    <PublicLayout>
        <nav class="mb-3 text-sm text-slate-500">
            <Link :href="guidesIndex.url()" class="hover:text-emerald-700">Hướng dẫn</Link>
        </nav>
        <article class="rounded-lg border border-slate-200 bg-white p-6">
            <h1 class="text-2xl font-bold">{{ guide.title }}</h1>
            <div class="prose mt-4 max-w-none prose-headings:font-bold prose-h2:text-lg" v-html="guide.body" />
        </article>
    </PublicLayout>
</template>
