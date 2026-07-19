<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { crawl } from '@/routes/admin';

defineProps<{ stats: { projects: number; published: number; guides: number } }>();

function runCrawl() {
    router.post(crawl.url());
}
</script>

<template>
    <Head title="Tổng quan" />

    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold">Tổng quan</h1>
            <button type="button" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700" @click="runCrawl">
                Chạy crawl dữ liệu
            </button>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Tổng dự án</p>
                <p class="text-2xl font-bold">{{ stats.projects }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Đã xuất bản</p>
                <p class="text-2xl font-bold">{{ stats.published }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Bài hướng dẫn</p>
                <p class="text-2xl font-bold">{{ stats.guides }}</p>
            </div>
        </div>
    </AdminLayout>
</template>
