<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { store, update } from '@/routes/admin/guides';

const props = defineProps<{ guide?: Record<string, unknown> }>();

const isEdit = !!props.guide;

const form = useForm({
    title: (props.guide?.title as string) ?? '',
    slug: (props.guide?.slug as string) ?? '',
    excerpt: (props.guide?.excerpt as string) ?? '',
    body: (props.guide?.body as string) ?? '',
    meta_title: (props.guide?.meta_title as string) ?? '',
    meta_description: (props.guide?.meta_description as string) ?? '',
    sort: (props.guide?.sort as number) ?? 0,
    published: !!props.guide?.published_at,
});

function slugify() {
    if (!isEdit) {
        form.slug = form.title
            .normalize('NFD')
            .replace(/[̀-ͯ]/g, '')
            .replace(/đ/g, 'd')
            .replace(/Đ/g, 'D')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
}

function submit() {
    if (isEdit) {
        form.put(update.url(props.guide!.slug as string));
    } else {
        form.post(store.url());
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Sửa hướng dẫn' : 'Thêm hướng dẫn'" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-bold">{{ isEdit ? 'Sửa hướng dẫn' : 'Thêm hướng dẫn' }}</h1>

        <form @submit.prevent="submit" class="max-w-2xl space-y-4 rounded-lg border border-slate-200 bg-white p-6">
            <div>
                <label class="block text-sm font-medium">Tiêu đề</label>
                <input v-model="form.title" @blur="slugify" type="text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                <p v-if="form.errors.title" class="text-sm text-red-600">{{ form.errors.title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium">Slug</label>
                <input v-model="form.slug" type="text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                <p v-if="form.errors.slug" class="text-sm text-red-600">{{ form.errors.slug }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium">Tóm tắt</label>
                <input v-model="form.excerpt" type="text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="block text-sm font-medium">Nội dung (HTML)</label>
                <textarea v-model="form.body" rows="12" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 font-mono text-sm" />
                <p v-if="form.errors.body" class="text-sm text-red-600">{{ form.errors.body }}</p>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.published" type="checkbox" /> Xuất bản công khai
            </label>
            <button type="submit" :disabled="form.processing" class="rounded-md bg-emerald-600 px-5 py-2 font-medium text-white hover:bg-emerald-700 disabled:opacity-50">
                {{ isEdit ? 'Cập nhật' : 'Tạo bài' }}
            </button>
        </form>
    </AdminLayout>
</template>
