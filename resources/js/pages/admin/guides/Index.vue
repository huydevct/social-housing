<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { create, destroy, edit } from '@/routes/admin/guides';

interface AdminGuideRow {
    id: number;
    title: string;
    slug: string;
    published: boolean;
}

defineProps<{ guides: AdminGuideRow[] }>();

function remove(slug: string) {
    if (confirm('Xóa bài hướng dẫn này?')) {
        router.delete(destroy.url(slug), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Quản lý hướng dẫn" />

    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold">Hướng dẫn</h1>
            <Link :href="create.url()" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                + Thêm bài
            </Link>
        </div>

        <table class="mt-4 w-full overflow-hidden rounded-lg border border-slate-200 bg-white text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-2">Tiêu đề</th>
                    <th class="px-4 py-2">Trạng thái</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="g in guides" :key="g.id" class="border-t border-slate-100">
                    <td class="px-4 py-2 font-medium">{{ g.title }}</td>
                    <td class="px-4 py-2">{{ g.published ? 'Đã xuất bản' : 'Bản nháp' }}</td>
                    <td class="px-4 py-2 text-right">
                        <Link :href="edit.url(g.slug)" class="text-emerald-700 hover:underline">Sửa</Link>
                        <button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(g.slug)">Xóa</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </AdminLayout>
</template>
