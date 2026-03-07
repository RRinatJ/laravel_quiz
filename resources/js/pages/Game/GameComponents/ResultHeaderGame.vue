<script setup lang="ts">
import { Trophy } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    quiz_title: string;
    user_name: string;
    isErrorMode: boolean;
    correctPrcnt: number;
}
const props = defineProps<Props>();

const title = computed(() => {
    if (props.isErrorMode) {
        return `Wrong answer, ${props.user_name}!`;
    } else if (props.correctPrcnt >= 85) {
        return `Congratulations, ${props.user_name}!`;
    } else if (props.correctPrcnt >= 50 && props.correctPrcnt < 85) {
        return `Nice result, ${props.user_name}!`;
    } else {
        return `Keep at it, ${props.user_name}!`;
    }
});

const text = computed(() => {
    if (props.isErrorMode) {
        return '';
    } else if (props.correctPrcnt >= 85) {
        return `You've successfully mastered the <span class="font-semibold text-[#137fec]">${props.quiz_title}</span> quiz.`;
    } else if (props.correctPrcnt >= 50 && props.correctPrcnt < 85) {
        return `You did well on <span class="font-semibold text-[#137fec]">${props.quiz_title}</span> quiz at the medium level.`;
    } else {
        return `The result in <span class="font-semibold text-[#137fec]">${props.quiz_title}</span> quiz is still modest...`;
    }
});
</script>

<template>
    <div>
        <div class="mb-8 w-full max-w-[800px] space-y-4 text-center">
            <div>
                <div
                    v-if="props.correctPrcnt >= 85"
                    class="mb-2 inline-flex items-center justify-center rounded-full bg-yellow-100 p-4 text-yellow-700"
                >
                    <span
                        class="material-symbols-outlined filled-icon !text-4xl"
                    >
                        <Trophy />
                    </span>
                </div>
                <h1 class="mb-4 text-4xl font-extrabold tracking-tight">
                    {{ title }}!
                </h1>
                <p class="text-lg text-[#617589]" v-html="text"></p>
            </div>
        </div>
    </div>
</template>
