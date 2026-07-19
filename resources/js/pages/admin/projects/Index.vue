<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Pagination from '@/components/Pagination.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { create, destroy, edit, publish } from '@/routes/admin/projects';
import type { Paginated } from '@/types/models';

interface AdminProjectRow {
    id: number;
    name: string;
    slug: string;
    province: string | null;
    status_label: string;
    published: boolean;
}

defineProps<{ projects: Paginated<AdminProjectRow> }>();

function togglePublish(slug: string) {
    router.post(publish.url(slug), {}, { preserveScroll: true });
}

function remove(slug: string) {
    if (confirm('Xóa dự án này?')) {
        router.delete(destroy.url(slug), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Quản lý dự án" />

    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold">Dự án</h1>
            <Link :href="create.url()" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                + Thêm dự án
            </Link>
        </div>

        <table class="mt-4 w-full overflow-hidden rounded-lg border border-slate-200 bg-white text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-2">Tên</th>
                    <th class="px-4 py-2">Tỉnh/thành</th>
                    <th class="px-4 py-2">Trạng thái</th>
                    <th class="px-4 py-2">Xuất bản</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="p in projects.data" :key="p.id" class="border-t border-slate-100">
                    <td class="px-4 py-2 font-medium">{{ p.name }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ p.province }}</td>
                    <td class="px-4 py-2">{{ p.status_label }}</td>
                    <td class="px-4 py-2">
                        <button
                            type="button"
                            class="rounded px-2 py-0.5 text-xs"
                            :class="p.published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600'"
                            @click="togglePublish(p.slug)"
                        >
                            {{ p.published ? 'Đã xuất bản' : 'Bản nháp' }}
                        </button>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <Link :href="edit.url(p.slug)" class="text-emerald-700 hover:underline">Sửa</Link>
                        <button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(p.slug)">Xóa</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <Pagination :links="projects.links" />
    </AdminLayout>
</template>
