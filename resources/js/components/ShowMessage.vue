<script setup lang="ts">
interface Props {
    message?: string;
}

const { message = '' } = defineProps<Props>();
</script>

<template>
    <div v-if="message">
        <div
            class="rounded-lg bg-green-100 p-4 text-sm text-green-700 dark:bg-green-200 dark:text-green-800"
            role="alert"
        >
            <span class="font-medium">
                {{ message }}
            </span>
        </div>
    </div>
</template>
