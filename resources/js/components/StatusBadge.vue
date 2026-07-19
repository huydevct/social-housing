<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{ status: string; label: string }>();

const classes = computed(() => {
    const map: Record<string, string> = {
        upcoming: 'bg-amber-100 text-amber-800',
        receiving: 'bg-emerald-100 text-emerald-800',
        closed: 'bg-slate-200 text-slate-700',
        completed: 'bg-sky-100 text-sky-800',
    };

    return map[props.status] ?? 'bg-slate-200 text-slate-700';
});
</script>

<template>
    <span :class="classes" class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium">
        {{ label }}
    </span>
</template>
