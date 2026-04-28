<script setup lang="ts">
    import {
        type LogChanges
    } from '@/types';
    
    defineProps<{
        log: LogChanges;
    }>();
</script>

<template>
    <div v-for="(newValue, key) in log.attributes" :key="key" class="flex flex-wrap items-center gap-1">
        <span class="font-medium text-gray-600">{{ key }}:</span>
        <span class="text-red-500 line-through decoration-red-300">
            {{ log.old?.[key] ?? '—' }}
        </span>
        <span class="text-gray-400">→</span>
        <span class="text-green-600 font-medium">
            {{ newValue ?? '—' }}
        </span>
    </div>
</template>