<script setup lang="ts">
interface Props {
    quiz_title: string;
    countDown: number;
    currentStep: number;
    questionsCount: number;
    сompletionPrcnt: number;
}
defineProps<Props>();
</script>

<template>
    <header class="px-10 py-3">
        <div class="logo">{{ quiz_title }}</div>
        <div class="timer text-2xl">{{ countDown }}</div>
        <div class="progress">
            <div class="flex justify-between gap-6">
                <p class="text-base leading-normal font-medium">
                    Question {{ currentStep }}/{{ questionsCount }}
                </p>
            </div>
            <div class="rounded bg-[#cedce8]">
                <div
                    class="h-2 rounded bg-[#0b80ee]"
                    :style="{ width: сompletionPrcnt + '%' }"
                ></div>
            </div>
        </div>
    </header>
</template>
