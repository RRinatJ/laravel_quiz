<script setup lang="ts">
interface Props {
    error?: string;
}

const { error = '' } = defineProps<Props>();
</script>

<template>
    <div v-if="error">
        <div
            class="rounded-lg bg-red-100 p-4 text-sm text-red-700 dark:bg-red-200 dark:text-red-800"
            role="alert"
        >
            <span class="font-medium">
                {{ error }}
            </span>
        </div>
    </div>
</template>
