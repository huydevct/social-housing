<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import { index as projectsIndex } from '@/routes/projects';
import type { ProvinceLink, StatusOption } from '@/types/models';

const props = defineProps<{
    provinces: ProvinceLink[];
    statuses: StatusOption[];
    filters: { province?: string; status?: string; keyword?: string };
}>();

const form = reactive({
    keyword: props.filters.keyword ?? '',
    province: props.filters.province ?? '',
    status: props.filters.status ?? '',
});

let timeout: ReturnType<typeof setTimeout>;
watch(form, () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        const query = Object.fromEntries(Object.entries(form).filter(([, v]) => v));
        router.get(projectsIndex.url(), query, { preserveState: true, replace: true });
    }, 300);
});
</script>

<template>
    <div class="grid gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:grid-cols-3">
        <input
            v-model="form.keyword"
            type="search"
            placeholder="Từ khóa tên dự án"
            class="rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none"
        />
        <select v-model="form.province" class="rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none">
            <option value="">Tất cả tỉnh/thành</option>
            <option v-for="p in provinces" :key="p.slug" :value="p.slug">{{ p.name }}</option>
        </select>
        <select v-model="form.status" class="rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none">
            <option value="">Tất cả trạng thái</option>
            <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
        </select>
    </div>
</template>
