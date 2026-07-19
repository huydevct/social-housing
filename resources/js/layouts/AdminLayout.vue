<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { logout } from '@/routes';
import { dashboard } from '@/routes/admin';
import { index as adminGuides } from '@/routes/admin/guides';
import { index as adminProjects } from '@/routes/admin/projects';

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string } | undefined);

watch(
    () => flash.value?.success,
    (msg) => {
        if (msg) {
window.setTimeout(() => {}, 0);
}
    },
);

function signOut() {
    router.post(logout.url());
}
</script>

<template>
    <div class="flex min-h-screen bg-slate-100 text-slate-900">
        <aside class="w-56 shrink-0 bg-slate-900 text-slate-100">
            <div class="p-4 text-lg font-bold">Quản trị</div>
            <nav class="flex flex-col gap-1 px-2 text-sm">
                <Link :href="dashboard.url()" class="rounded px-3 py-2 hover:bg-slate-800">Tổng quan</Link>
                <Link :href="adminProjects.url()" class="rounded px-3 py-2 hover:bg-slate-800">Dự án</Link>
                <Link :href="adminGuides.url()" class="rounded px-3 py-2 hover:bg-slate-800">Hướng dẫn</Link>
                <button type="button" class="mt-4 rounded px-3 py-2 text-left text-red-300 hover:bg-slate-800" @click="signOut">
                    Đăng xuất
                </button>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <div v-if="flash?.success" class="mb-4 rounded-md bg-emerald-100 px-4 py-2 text-sm text-emerald-800">
                {{ flash.success }}
            </div>
            <slot />
        </main>
    </div>
</template>
