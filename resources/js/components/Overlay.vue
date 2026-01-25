<script lang="ts" setup>
const { isBlur = false } = defineProps<{
    isBlur?: boolean;
}>();
</script>

<template>
    <div :class="isBlur ? 'inactive-blurred' : ''">
        <slot></slot>
    </div>
</template>

<style scoped>
.inactive-blurred {
    filter: blur(5px);
    pointer-events: none;
    opacity: 0.5;
    cursor: not-allowed;
    user-select: none;
}
</style>
