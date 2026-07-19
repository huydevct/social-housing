<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { store, update } from '@/routes/admin/projects';
import type { StatusOption } from '@/types/models';

const props = defineProps<{
    provinces: { id: number; name: string }[];
    investors: { id: number; name: string }[];
    statuses: StatusOption[];
    project?: Record<string, unknown>;
}>();

const isEdit = !!props.project;

const form = useForm({
    name: (props.project?.name as string) ?? '',
    slug: (props.project?.slug as string) ?? '',
    province_id: (props.project?.province_id as number) ?? props.provinces[0]?.id ?? null,
    investor_id: (props.project?.investor_id as number) ?? null,
    district: (props.project?.district as string) ?? '',
    address: (props.project?.address as string) ?? '',
    former_address: (props.project?.former_address as string) ?? '',
    status: (props.project?.status as string) ?? props.statuses[0]?.value ?? 'upcoming',
    application_start_at: (props.project?.application_start_at as string)?.slice(0, 10) ?? '',
    application_end_at: (props.project?.application_end_at as string)?.slice(0, 10) ?? '',
    total_units: (props.project?.total_units as number) ?? null,
    price_from: (props.project?.price_from as number) ?? null,
    price_to: (props.project?.price_to as number) ?? null,
    area_from: (props.project?.area_from as number) ?? null,
    area_to: (props.project?.area_to as number) ?? null,
    description: (props.project?.description as string) ?? '',
    application_guide: (props.project?.application_guide as string) ?? '',
    source_name: (props.project?.source_name as string) ?? '',
    source_url: (props.project?.source_url as string) ?? '',
    meta_title: (props.project?.meta_title as string) ?? '',
    meta_description: (props.project?.meta_description as string) ?? '',
    published: !!props.project?.published_at,
});

function slugify() {
    if (!isEdit) {
        form.slug = form.name
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
        form.put(update.url(props.project!.slug as string));
    } else {
        form.post(store.url());
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Sửa dự án' : 'Thêm dự án'" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-bold">{{ isEdit ? 'Sửa dự án' : 'Thêm dự án' }}</h1>

        <form @submit.prevent="submit" class="max-w-2xl space-y-4 rounded-lg border border-slate-200 bg-white p-6">
            <div>
                <label class="block text-sm font-medium">Tên dự án</label>
                <input v-model="form.name" @blur="slugify" type="text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium">Slug (đường dẫn)</label>
                <input v-model="form.slug" type="text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                <p v-if="form.errors.slug" class="text-sm text-red-600">{{ form.errors.slug }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium">Tỉnh/thành</label>
                    <select v-model="form.province_id" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option v-for="p in provinces" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Chủ đầu tư</label>
                    <select v-model="form.investor_id" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option :value="null">— Không —</option>
                        <option v-for="inv in investors" :key="inv.id" :value="inv.id">{{ inv.name }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Địa chỉ (sau sáp nhập)</label>
                <input v-model="form.address" type="text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="block text-sm font-medium">Địa chỉ trước sáp nhập</label>
                <input v-model="form.former_address" type="text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium">Trạng thái</label>
                    <select v-model="form.status" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Số căn</label>
                    <input v-model.number="form.total_units" type="number" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Bắt đầu nhận hồ sơ</label>
                    <input v-model="form.application_start_at" type="date" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Hạn nhận hồ sơ</label>
                    <input v-model="form.application_end_at" type="date" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Giá từ (đồng/m²)</label>
                    <input v-model.number="form.price_from" type="number" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Giá đến (đồng/m²)</label>
                    <input v-model.number="form.price_to" type="number" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Diện tích từ (m²)</label>
                    <input v-model.number="form.area_from" type="number" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Diện tích đến (m²)</label>
                    <input v-model.number="form.area_to" type="number" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Mô tả dự án</label>
                <textarea v-model="form.description" rows="4" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="block text-sm font-medium">Hướng dẫn nộp hồ sơ (riêng dự án)</label>
                <textarea v-model="form.application_guide" rows="3" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium">Tên nguồn</label>
                    <input v-model="form.source_name" type="text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium">URL nguồn</label>
                    <input v-model="form.source_url" type="url" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.published" type="checkbox" /> Xuất bản công khai
            </label>

            <button type="submit" :disabled="form.processing" class="rounded-md bg-emerald-600 px-5 py-2 font-medium text-white hover:bg-emerald-700 disabled:opacity-50">
                {{ isEdit ? 'Cập nhật' : 'Tạo dự án' }}
            </button>
        </form>
    </AdminLayout>
</template>
