<script setup lang="ts">
    import {
        type Quiz,
    } from '@/types';

    defineProps<{       
        quizzes: {
            attached: Quiz[];
            detached: Quiz[];
        };
    }>();
</script>

<template>
    <div v-for="quiz in quizzes.attached" :key="quiz.id" class="flex flex-wrap items-center gap-1">
        <span class="font-medium">
            <span class="text-green-600 mr-2">Attached</span> 
            <span class="text-gray-500 font-bold">ID: </span><span class="text-green-600 mr-2">{{ quiz.id }}</span> 
            <span class="text-gray-500 font-bold">Title: </span><span class="text-green-600">{{ quiz.title }}</span>
        </span>
    </div>
    <div v-for="quiz in quizzes.detached" :key="quiz.id" class="flex flex-wrap items-center gap-1">
        <span class="font-medium">
            <span class="text-red-600 mr-2">Detached</span> 
            <span class="text-gray-500 font-bold">ID: </span><span class="text-red-600 mr-2">{{ quiz.id }}</span> 
            <span class="text-gray-500 font-bold">Title: </span><span class="text-red-600">{{ quiz.title }}</span>
        </span>
    </div>
</template>