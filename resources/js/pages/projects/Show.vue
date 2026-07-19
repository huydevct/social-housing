<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppHead from '@/components/AppHead.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { formatArea, formatDate, formatPricePerM2 } from '@/lib/format';
import { index as projectsIndex } from '@/routes/projects';
import { show as provinceShow } from '@/routes/provinces';
import type { ProjectDetail } from '@/types/models';

const props = defineProps<{ project: ProjectDetail }>();

const appUrl = computed(() => (usePage().props.appUrl as string | undefined)?.replace(/\/$/, '') ?? '');

const metaTitle = computed(() => props.project.meta_title ?? `${props.project.name} — Nhà ở xã hội`);
const metaDescription = computed(
    () =>
        props.project.meta_description ??
        `${props.project.name} tại ${props.project.address ?? props.project.province}. Giá ${formatPricePerM2(props.project.price_from, props.project.price_to)}, trạng thái: ${props.project.status_label}.`,
);

const jsonLd = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'Residence',
    name: props.project.name,
    description: metaDescription.value,
    url: appUrl.value + projectsIndex.url() + '/' + props.project.slug,
    ...(props.project.cover ? { image: props.project.cover } : {}),
    address: {
        '@type': 'PostalAddress',
        streetAddress: props.project.address ?? undefined,
        addressLocality: props.project.district ?? undefined,
        addressRegion: props.project.province ?? undefined,
        addressCountry: 'VN',
    },
}));

const infoRows = computed(() =>
    [
        { label: 'Chủ đầu tư', value: props.project.investor?.name },
        { label: 'Địa chỉ (sau sáp nhập)', value: props.project.address },
        { label: 'Địa chỉ trước sáp nhập', value: props.project.former_address },
        { label: 'Giá bán', value: formatPricePerM2(props.project.price_from, props.project.price_to) },
        { label: 'Diện tích', value: formatArea(props.project.area_from, props.project.area_to) },
        { label: 'Số căn', value: props.project.total_units ? `${props.project.total_units} căn` : null },
        { label: 'Bắt đầu nhận hồ sơ', value: formatDate(props.project.application_start_at) },
        { label: 'Hạn nhận hồ sơ', value: formatDate(props.project.application_end_at) },
    ].filter((row) => row.value && row.value !== '—'),
);
</script>

<template>
    <AppHead :title="metaTitle" :description="metaDescription" :image="project.cover ?? undefined" :json-ld="jsonLd" />

    <PublicLayout>
        <nav class="mb-3 text-sm text-slate-500">
            <Link :href="projectsIndex.url()" class="hover:text-emerald-700">Dự án</Link>
            <span> / </span>
            <Link v-if="project.province_slug" :href="provinceShow.url(project.province_slug)" class="hover:text-emerald-700">
                {{ project.province }}
            </Link>
        </nav>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="mb-3 flex items-center gap-3">
                    <StatusBadge :status="project.status" :label="project.status_label" />
                </div>
                <h1 class="text-2xl font-bold">{{ project.name }}</h1>
                <p class="mt-1 text-slate-500">{{ [project.district, project.province].filter(Boolean).join(', ') }}</p>

                <div v-if="project.cover" class="mt-4 overflow-hidden rounded-lg">
                    <img :src="project.cover" :alt="project.name" class="w-full object-cover" />
                </div>

                <div v-if="project.description" class="prose mt-6 max-w-none">
                    <h2 class="text-lg font-bold">Giới thiệu dự án</h2>
                    <p class="whitespace-pre-line text-slate-700">{{ project.description }}</p>
                </div>

                <div v-if="project.application_guide" class="mt-6">
                    <h2 class="text-lg font-bold">Hướng dẫn nộp hồ sơ</h2>
                    <p class="whitespace-pre-line text-slate-700">{{ project.application_guide }}</p>
                </div>
            </div>

            <aside class="lg:col-span-1">
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <h2 class="mb-3 font-bold">Thông tin dự án</h2>
                    <dl class="space-y-2 text-sm">
                        <div v-for="row in infoRows" :key="row.label" class="flex justify-between gap-3">
                            <dt class="text-slate-500">{{ row.label }}</dt>
                            <dd class="text-right font-medium text-slate-900">{{ row.value }}</dd>
                        </div>
                    </dl>
                    <p v-if="project.source_name" class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-400">
                        Nguồn:
                        <a v-if="project.source_url" :href="project.source_url" target="_blank" rel="noopener" class="underline">{{ project.source_name }}</a>
                        <span v-else>{{ project.source_name }}</span>
                    </p>
                </div>
            </aside>
        </div>
    </PublicLayout>
</template>
