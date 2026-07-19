<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppHead from '@/components/AppHead.vue';
import ProjectCard from '@/components/ProjectCard.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { show as guideShow } from '@/routes/guides';
import { index as projectsIndex } from '@/routes/projects';
import { show as provinceShow } from '@/routes/provinces';
import type { GuideCard, ProjectCard as ProjectCardType, ProvinceLink } from '@/types/models';

defineProps<{
    featured: ProjectCardType[];
    provinces: ProvinceLink[];
    guides: GuideCard[];
}>();

const keyword = ref('');

function search() {
    router.get(projectsIndex.url(), keyword.value ? { keyword: keyword.value } : {});
}
</script>

<template>
    <AppHead
        title="Nhà Ở Xã Hội Việt Nam — Tra cứu dự án & hướng dẫn nộp hồ sơ"
        description="Danh sách dự án nhà ở xã hội trên toàn quốc: thời gian tiếp nhận hồ sơ, giá bán, chủ đầu tư và hướng dẫn thủ tục nộp hồ sơ chi tiết."
    />

    <PublicLayout>
        <section class="rounded-xl bg-gradient-to-br from-emerald-600 to-emerald-700 px-6 py-12 text-center text-white">
            <h1 class="text-2xl font-bold sm:text-3xl">Tìm dự án nhà ở xã hội trên toàn quốc</h1>
            <p class="mx-auto mt-2 max-w-2xl text-emerald-50">
                Cập nhật thời gian mở bán, tiếp nhận hồ sơ và hướng dẫn thủ tục từ nguồn chính thức.
            </p>
            <form @submit.prevent="search" class="mx-auto mt-6 flex max-w-xl">
                <input
                    v-model="keyword"
                    type="search"
                    placeholder="Nhập tên dự án hoặc khu vực..."
                    class="w-full rounded-l-md px-4 py-3 text-slate-900 focus:outline-none"
                />
                <button type="submit" class="rounded-r-md bg-slate-900 px-6 font-medium hover:bg-slate-800">Tìm kiếm</button>
            </form>
        </section>

        <section class="mt-10">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Dự án mới cập nhật</h2>
                <Link :href="projectsIndex.url()" class="text-sm font-medium text-emerald-700 hover:underline">Xem tất cả →</Link>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <ProjectCard v-for="project in featured" :key="project.slug" :project="project" />
            </div>
        </section>

        <section v-if="provinces.length" class="mt-10">
            <h2 class="mb-4 text-xl font-bold">Tra cứu theo tỉnh/thành</h2>
            <div class="flex flex-wrap gap-2">
                <Link
                    v-for="p in provinces"
                    :key="p.slug"
                    :href="provinceShow.url(p.slug)"
                    class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm hover:border-emerald-500 hover:text-emerald-700"
                >
                    {{ p.name }} <span class="text-slate-400">({{ p.projects_count }})</span>
                </Link>
            </div>
        </section>

        <section v-if="guides.length" class="mt-10">
            <h2 class="mb-4 text-xl font-bold">Hướng dẫn nộp hồ sơ</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <Link
                    v-for="guide in guides"
                    :key="guide.slug"
                    :href="guideShow.url(guide.slug)"
                    class="rounded-lg border border-slate-200 bg-white p-4 hover:shadow-md"
                >
                    <h3 class="font-semibold text-slate-900">{{ guide.title }}</h3>
                    <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ guide.excerpt }}</p>
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
