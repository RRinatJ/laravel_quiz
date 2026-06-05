<script setup lang="ts">
import LogOldNew from '@/components/log/LogOldNew.vue';
import { type Answer, type LogChanges } from '@/types';

defineProps<{
    answers: {
        attached: Answer[];
        detached: Answer[];
        updated: LogChanges[];
    };
}>();
</script>

<template>
    <div
        v-for="answer in answers.attached"
        :key="answer.id"
        class="flex flex-wrap items-center gap-1"
    >
        <span class="mr-2 text-green-600">Attached</span>
        <span class="font-bold text-gray-500">ID: </span
        ><span class="mr-2 text-green-600">{{ answer.id }}</span>
        <span class="font-medium">
            <span class="font-bold text-gray-500">Text: </span
            ><span class="mr-2 text-green-600">{{ answer.text }}</span>
            <span class="font-bold text-gray-500">Image: </span
            ><span class="mr-2 text-green-600">{{ answer.image }}</span>
            <span class="font-bold text-gray-500">Is correct: </span
            ><span class="text-green-600">{{ answer.is_correct }}</span>
        </span>
    </div>
    <div
        v-for="answer in answers.updated"
        :key="answer.id"
        class="flex flex-wrap items-center gap-1"
    >
        <span class="mr-2 text-green-600">Updated</span>
        <span class="font-bold text-gray-500">ID: </span
        ><span class="mr-2 text-green-600">{{ answer.id }}</span>
        <LogOldNew :log="answer" />
    </div>
    <div
        v-for="answer in answers.detached"
        :key="answer.id"
        class="flex flex-wrap items-center gap-1"
    >
        <span class="mr-2 text-red-600">Detached</span>
        <span class="font-bold text-gray-500">ID: </span
        ><span class="mr-2 text-red-600">{{ answer.id }}</span>
        <span class="font-medium">
            <span class="font-bold text-gray-500">Text: </span
            ><span class="mr-2 text-red-600">{{ answer.text }}</span>
            <span class="font-bold text-gray-500">Image: </span
            ><span class="mr-2 text-red-600">{{ answer.image }}</span>
            <span class="font-bold text-gray-500">Is correct: </span
            ><span class="text-red-600">{{ answer.is_correct }}</span>
        </span>
    </div>
</template>
