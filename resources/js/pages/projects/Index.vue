<script setup lang="ts">
import AppHead from '@/components/AppHead.vue';
import Pagination from '@/components/Pagination.vue';
import ProjectCard from '@/components/ProjectCard.vue';
import ProjectFilters from '@/components/ProjectFilters.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Paginated, ProjectCard as ProjectCardType, ProvinceLink, StatusOption } from '@/types/models';

defineProps<{
    projects: Paginated<ProjectCardType>;
    filters: { province?: string; status?: string; keyword?: string };
    provinces: ProvinceLink[];
    statuses: StatusOption[];
}>();
</script>

<template>
    <AppHead
        title="Danh sách dự án nhà ở xã hội"
        description="Tra cứu toàn bộ dự án nhà ở xã hội theo tỉnh/thành, trạng thái tiếp nhận hồ sơ và mức giá."
    />

    <PublicLayout>
        <h1 class="mb-4 text-2xl font-bold">Dự án nhà ở xã hội</h1>

        <ProjectFilters :provinces="provinces" :statuses="statuses" :filters="filters" />

        <p class="mt-4 text-sm text-slate-500">Tìm thấy {{ projects.total }} dự án</p>

        <div v-if="projects.data.length" class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <ProjectCard v-for="project in projects.data" :key="project.slug" :project="project" />
        </div>
        <p v-else class="mt-8 text-center text-slate-500">Không có dự án phù hợp.</p>

        <Pagination :links="projects.links" />
    </PublicLayout>
</template>
