<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { store } from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(store.url(), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Đăng nhập quản trị" />

    <div class="flex min-h-screen items-center justify-center bg-slate-100 px-4">
        <form @submit.prevent="submit" class="w-full max-w-sm rounded-lg border border-slate-200 bg-white p-6">
            <h1 class="mb-4 text-xl font-bold">Đăng nhập quản trị</h1>

            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input
                v-model="form.email"
                type="email"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none"
            />
            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>

            <label class="mt-3 block text-sm font-medium text-slate-700">Mật khẩu</label>
            <input
                v-model="form.password"
                type="password"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none"
            />

            <label class="mt-3 flex items-center gap-2 text-sm text-slate-600">
                <input v-model="form.remember" type="checkbox" /> Ghi nhớ đăng nhập
            </label>

            <button
                type="submit"
                :disabled="form.processing"
                class="mt-4 w-full rounded-md bg-emerald-600 py-2 font-medium text-white hover:bg-emerald-700 disabled:opacity-50"
            >
                Đăng nhập
            </button>
        </form>
    </div>
</template>
