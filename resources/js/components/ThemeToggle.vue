<template>
    <button @click="toggleTheme" class="rounded border px-4 py-2">
        {{ appearance === 'dark' ? '🌙 Dark' : '☀️ Light' }}
    </button>
</template>

<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
const { appearance, updateAppearance } = useAppearance();
const toggleTheme = () => {
    updateAppearance(appearance.value !== 'dark' ? 'dark' : 'light');
};
</script>
