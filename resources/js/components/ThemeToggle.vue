<template>
    <button @click="toggleTheme" class="px-4 py-2 rounded border">
      {{ appearance === 'dark' ? '🌙 Dark' : '☀️ Light' }}
    </button>
  </template>
  
  <script setup lang="ts">
  import { useAppearance } from '@/composables/useAppearance';
  const { appearance, updateAppearance } = useAppearance();
  const toggleTheme = () => {
    updateAppearance(appearance.value !== "dark" ? "dark" : "light");
  }
  </script>