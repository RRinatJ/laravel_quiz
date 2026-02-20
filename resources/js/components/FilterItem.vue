<script setup lang="ts">
interface Props {
    checkValue: boolean;
    text: string;
}

defineProps<Props>();
</script>

<template>
    <button
        class="group flex h-12 items-center gap-2 rounded-xl border px-4 whitespace-nowrap shadow-sm transition-colors hover:bg-slate-50"
        :class="
            checkValue
                ? 'filter-selected border-transparent'
                : 'border-[#e7edf4] bg-white'
        "
    >
        <span
            class="material-symbols-outlined transition-transform group-active:scale-125"
        >
            <slot></slot>
        </span>
        <span
            class="text-sm font-semibold"
            :class="checkValue ? 'text-white' : 'text-[#0d141c]'"
        >
            {{ text }}
        </span>
    </button>
</template>
