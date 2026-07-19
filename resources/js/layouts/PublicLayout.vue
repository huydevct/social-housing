<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { home } from '@/routes';
import { index as guidesIndex } from '@/routes/guides';
import { index as projectsIndex } from '@/routes/projects';

const keyword = ref('');

function search() {
    router.get(projectsIndex.url(), keyword.value ? { keyword: keyword.value } : {}, {
        preserveState: true,
    });
}
</script>

<template>
    <div class="flex min-h-screen flex-col bg-slate-50 text-slate-900">
        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center gap-4 px-4 py-3">
                <Link :href="home.url()" class="shrink-0 text-lg font-bold text-emerald-700">
                    Nhà Ở Xã Hội
                </Link>

                <div class="hidden flex-1 md:block">
                    <form @submit.prevent="search" class="flex">
                        <input
                            v-model="keyword"
                            type="search"
                            placeholder="Tìm dự án nhà ở xã hội..."
                            class="w-full rounded-l-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none"
                        />
                        <button type="submit" class="rounded-r-md bg-emerald-600 px-4 text-sm font-medium text-white hover:bg-emerald-700">
                            Tìm
                        </button>
                    </form>
                </div>

                <nav class="flex shrink-0 items-center gap-4 text-sm font-medium">
                    <Link :href="projectsIndex.url()" class="hover:text-emerald-700">Dự án</Link>
                    <Link :href="guidesIndex.url()" class="hover:text-emerald-700">Hướng dẫn</Link>
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-6">
            <slot />
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-6xl px-4 py-6 text-sm text-slate-500">
                <p class="font-semibold text-slate-700">Nhà Ở Xã Hội Việt Nam</p>
                <p class="mt-1">Thông tin tổng hợp từ nguồn công khai của Sở Xây dựng các tỉnh/thành và chủ đầu tư. Vui lòng đối chiếu với nguồn chính thức trước khi nộp hồ sơ.</p>
            </div>
        </footer>
    </div>
</template>
