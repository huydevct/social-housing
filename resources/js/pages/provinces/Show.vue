<script setup lang="ts">
import AppHead from '@/components/AppHead.vue';
import Pagination from '@/components/Pagination.vue';
import ProjectCard from '@/components/ProjectCard.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Paginated, ProjectCard as ProjectCardType } from '@/types/models';

defineProps<{
    province: { name: string; slug: string };
    projects: Paginated<ProjectCardType>;
}>();
</script>

<template>
    <AppHead
        :title="`Nhà ở xã hội ${province.name}`"
        :description="`Danh sách ${projects.total} dự án nhà ở xã hội tại ${province.name}: thời gian tiếp nhận hồ sơ, giá bán và hướng dẫn thủ tục.`"
    />

    <PublicLayout>
        <h1 class="mb-1 text-2xl font-bold">Nhà ở xã hội tại {{ province.name }}</h1>
        <p class="mb-4 text-sm text-slate-500">{{ projects.total }} dự án</p>

        <div v-if="projects.data.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <ProjectCard v-for="project in projects.data" :key="project.slug" :project="project" />
        </div>
        <p v-else class="mt-8 text-center text-slate-500">Chưa có dự án nào tại khu vực này.</p>

        <Pagination :links="projects.links" />
    </PublicLayout>
</template>
