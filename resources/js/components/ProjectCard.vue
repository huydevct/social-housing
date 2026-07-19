<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { formatArea, formatPricePerM2 } from '@/lib/format';
import { show as projectShow } from '@/routes/projects';
import type { ProjectCard } from '@/types/models';
import StatusBadge from './StatusBadge.vue';

defineProps<{ project: ProjectCard }>();
</script>

<template>
    <Link
        :href="projectShow.url(project.slug)"
        class="group flex flex-col overflow-hidden rounded-lg border border-slate-200 bg-white transition hover:shadow-md"
    >
        <div class="aspect-video overflow-hidden bg-slate-100">
            <img
                v-if="project.cover"
                :src="project.cover"
                :alt="project.name"
                loading="lazy"
                class="h-full w-full object-cover transition group-hover:scale-105"
            />
            <div v-else class="flex h-full items-center justify-center text-slate-300">Chưa có ảnh</div>
        </div>

        <div class="flex flex-1 flex-col gap-2 p-4">
            <StatusBadge :status="project.status" :label="project.status_label" />
            <h3 class="line-clamp-2 font-semibold text-slate-900 group-hover:text-emerald-700">
                {{ project.name }}
            </h3>
            <p class="line-clamp-1 text-sm text-slate-500">
                {{ [project.district, project.province].filter(Boolean).join(', ') }}
            </p>
            <div class="mt-auto flex items-center justify-between pt-2 text-sm">
                <span class="font-semibold text-emerald-700">{{ formatPricePerM2(project.price_from, project.price_to) }}</span>
                <span class="text-slate-500">{{ formatArea(project.area_from, project.area_to) }}</span>
            </div>
        </div>
    </Link>
</template>
