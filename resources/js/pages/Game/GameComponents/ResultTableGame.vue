<script setup lang="ts">
import {
    Ban,
    CircleCheck,
    CircleFadingArrowUp,
    Lightbulb,
    Target,
    Timer,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    correctCount: number;
    questionsCount: number;
    correctPrcnt: number;
    isErrorMode: boolean;
    timeTaken?: string;
    hintsUsed?: string;
}
const props = defineProps<Props>();

const icon = computed(() => {
    if (props.isErrorMode) {
        return Ban;
    } else if (props.correctPrcnt >= 85) {
        return CircleCheck;
    } else if (props.correctPrcnt >= 50 && props.correctPrcnt < 85) {
        return CircleFadingArrowUp;
    } else {
        return Ban;
    }
});
const className = computed(() => {
    if (props.isErrorMode) {
        return 'bg-red-100 text-red-700';
    } else if (!props.isErrorMode && props.correctPrcnt >= 85) {
        return 'bg-green-100 text-green-700';
    } else if (
        !props.isErrorMode &&
        props.correctPrcnt >= 50 &&
        props.correctPrcnt < 85
    ) {
        return 'bg-yellow-100 text-yellow-700';
    } else {
        return 'bg-gray-100 text-gray-700';
    }
});
const description = computed(() => {
    if (props.isErrorMode) {
        return 'Better luck next time!';
    } else if (props.correctPrcnt >= 85) {
        return 'Excellent performance!';
    } else if (props.correctPrcnt >= 50 && props.correctPrcnt < 85) {
        return 'Good job, keep improving!';
    } else {
        return 'Keep practicing to improve your score!';
    }
});
</script>

<template>
    <div>
        <div class="w-full max-w-[600px] overflow-hidden bg-white">
            <div
                class="flex flex-col items-center border-b border-[#dbe0e6] bg-[#137fec]/5 p-10"
            >
                <span
                    class="mb-2 text-sm font-bold tracking-widest text-[#137fec] uppercase"
                >
                    Your Final Score
                </span>
                <div class="flex items-baseline gap-1">
                    <span class="text-7xl font-black text-[#111418]">{{
                        correctCount
                    }}</span>
                    <span class="text-3xl font-bold text-[#617589]"
                        >/{{ questionsCount }}</span
                    >
                </div>
                <div
                    class="mt-4 flex items-center gap-2 rounded-full bg-green-100 px-4 py-1.5 text-sm font-bold text-green-700"
                    :class="className"
                >
                    <span class="material-symbols-outlined !text-lg">
                        <component :is="icon" class="!text-lg" />
                    </span>
                    {{ description }}
                </div>
            </div>
            <div
                class="grid grid-cols-1 divide-y border-[#dbe0e6] md:grid-cols-3 md:gap-12 md:divide-x md:divide-y-0"
            >
                <div class="flex flex-col items-center p-8 text-center">
                    <span
                        class="material-symbols-outlined mb-2 !text-3xl text-[#137fec]"
                        ><Timer
                    /></span>
                    <span class="text-sm font-medium text-[#617589]"
                        >Time Taken</span
                    >
                    <span class="text-xl font-bold text-[#111418]">{{
                        timeTaken
                    }}</span>
                </div>
                <div class="flex flex-col items-center p-8 text-center">
                    <span
                        class="material-symbols-outlined mb-2 !text-3xl text-[#137fec]"
                        ><Target
                    /></span>
                    <span class="text-sm font-medium text-[#617589]"
                        >Accuracy Rate</span
                    >
                    <span class="text-xl font-bold text-[#111418]"
                        >{{ correctPrcnt }}%</span
                    >
                </div>
                <div class="flex flex-col items-center p-8 text-center">
                    <span
                        class="material-symbols-outlined mb-2 !text-3xl text-[#137fec]"
                        ><Lightbulb
                    /></span>
                    <span class="text-sm font-medium text-[#617589]"
                        >Hints Used</span
                    >
                    <span class="text-xl font-bold text-[#111418]">{{
                        hintsUsed
                    }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
