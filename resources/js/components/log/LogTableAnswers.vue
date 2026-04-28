<script setup lang="ts">
    import LogOldNew from '@/components/log/LogOldNew.vue';
    import {
        type Answer,
        type LogChanges
    } from '@/types';

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
        <span class="text-green-600 mr-2">Attached</span>
        <span class="text-gray-500 font-bold">ID: </span><span class="text-green-600 mr-2">{{ answer.id }}</span>                     
        <span class="font-medium">         
            <span class="text-gray-500 font-bold">Text: </span><span class="text-green-600 mr-2">{{ answer.text }}</span>
            <span class="text-gray-500 font-bold">Image: </span><span class="text-green-600 mr-2">{{ answer.image }}</span>
            <span class="text-gray-500 font-bold">Is correct: </span><span class="text-green-600">{{ answer.is_correct }}</span>
        </span>
    </div>
    <div
        v-for="answer in answers.updated" 
        :key="answer.id" 
        class="flex flex-wrap items-center gap-1"
    >
        <span class="text-green-600 mr-2">Updated</span>
        <span class="text-gray-500 font-bold">ID: </span><span class="text-green-600 mr-2">{{ answer.id }}</span>
        <LogOldNew :log="answer" />
    </div>
    <div
        v-for="answer in answers.detached" 
        :key="answer.id" 
        class="flex flex-wrap items-center gap-1"
    >
        <span class="text-red-600 mr-2">Detached</span>    
        <span class="text-gray-500 font-bold">ID: </span><span class="text-red-600 mr-2">{{ answer.id }}</span>                    
        <span class="font-medium">         
            <span class="text-gray-500 font-bold">Text: </span><span class="text-red-600 mr-2">{{ answer.text }}</span>
            <span class="text-gray-500 font-bold">Image: </span><span class="text-red-600 mr-2">{{ answer.image }}</span>
            <span class="text-gray-500 font-bold">Is correct: </span><span class="text-red-600">{{ answer.is_correct }}</span>
        </span>
    </div>
</template>