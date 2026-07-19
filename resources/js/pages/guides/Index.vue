<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppHead from '@/components/AppHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { show as guideShow } from '@/routes/guides';
import type { GuideCard } from '@/types/models';

defineProps<{ guides: GuideCard[] }>();
</script>

<template>
    <AppHead
        title="Hướng dẫn nộp hồ sơ nhà ở xã hội"
        description="Điều kiện, giấy tờ cần thiết và quy trình nộp hồ sơ mua, thuê nhà ở xã hội theo quy định mới nhất."
    />

    <PublicLayout>
        <h1 class="mb-4 text-2xl font-bold">Hướng dẫn nộp hồ sơ</h1>
        <div class="grid gap-4 sm:grid-cols-2">
            <Link
                v-for="guide in guides"
                :key="guide.slug"
                :href="guideShow.url(guide.slug)"
                class="rounded-lg border border-slate-200 bg-white p-5 hover:shadow-md"
            >
                <h2 class="font-semibold text-slate-900">{{ guide.title }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ guide.excerpt }}</p>
            </Link>
        </div>
    </PublicLayout>
</template>
