<script setup lang="ts">
import { type Quiz } from '@/types';

defineProps<{
    quizzes: {
        attached: Quiz[];
        detached: Quiz[];
    };
}>();
</script>

<template>
    <div
        v-for="quiz in quizzes.attached"
        :key="quiz.id"
        class="flex flex-wrap items-center gap-1"
    >
        <span class="font-medium">
            <span class="mr-2 text-green-600">Attached</span>
            <span class="font-bold text-gray-500">ID: </span
            ><span class="mr-2 text-green-600">{{ quiz.id }}</span>
            <span class="font-bold text-gray-500">Title: </span
            ><span class="text-green-600">{{ quiz.title }}</span>
        </span>
    </div>
    <div
        v-for="quiz in quizzes.detached"
        :key="quiz.id"
        class="flex flex-wrap items-center gap-1"
    >
        <span class="font-medium">
            <span class="mr-2 text-red-600">Detached</span>
            <span class="font-bold text-gray-500">ID: </span
            ><span class="mr-2 text-red-600">{{ quiz.id }}</span>
            <span class="font-bold text-gray-500">Title: </span
            ><span class="text-red-600">{{ quiz.title }}</span>
        </span>
    </div>
</template>
