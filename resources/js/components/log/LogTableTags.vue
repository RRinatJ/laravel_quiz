<script setup lang="ts">
defineProps<{
    tags: {
        attached: { name: string }[];
        detached: { name: string }[];
    };
}>();
</script>

<template>
    <div
        v-for="tag in tags.attached"
        :key="tag.name"
        class="flex flex-wrap items-center gap-1"
    >
        <span class="font-medium">
            <span class="mr-2 text-green-600">Attached</span>
            <span class="font-bold text-gray-500">Name: </span
            ><span class="text-green-600">{{ tag.name }}</span>
        </span>
    </div>
    <div
        v-for="tag in tags.detached"
        :key="tag.name"
        class="flex flex-wrap items-center gap-1"
    >
        <span class="font-medium">
            <span class="mr-2 text-red-600">Detached</span>
            <span class="font-bold text-gray-500">Name: </span
            ><span class="text-red-600">{{ tag.name }}</span>
        </span>
    </div>
</template>
