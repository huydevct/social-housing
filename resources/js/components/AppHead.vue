<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        title: string;
        description?: string;
        image?: string;
        canonicalPath?: string;
        jsonLd?: Record<string, unknown> | Record<string, unknown>[];
    }>(),
    {
        description: 'Tra cứu thông tin dự án nhà ở xã hội trên toàn quốc: thời gian tiếp nhận hồ sơ, giá bán, hướng dẫn thủ tục nộp hồ sơ.',
    },
);

const page = usePage();

const appUrl = computed(() => (page.props.appUrl as string | undefined)?.replace(/\/$/, '') ?? '');

const canonical = computed(() => {
    const path = props.canonicalPath ?? page.url;

    return appUrl.value + path;
});

const jsonLdString = computed(() => (props.jsonLd ? JSON.stringify(props.jsonLd) : null));
</script>

<template>
    <Head :title="title">
        <meta name="description" :content="description" />
        <link rel="canonical" :href="canonical" />

        <meta property="og:type" content="website" />
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:url" :content="canonical" />
        <meta v-if="image" property="og:image" :content="image" />

        <meta name="twitter:card" :content="image ? 'summary_large_image' : 'summary'" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />

        <component :is="'script'" v-if="jsonLdString" type="application/ld+json">{{ jsonLdString }}</component>
    </Head>
</template>
