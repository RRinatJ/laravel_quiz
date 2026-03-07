<script setup lang="ts">
import { AnswerInGame } from '@/types';

const emit = defineEmits(['setAnswerId']);

interface Props {
    answers: AnswerInGame[];
    correct_answer_id: number | null;
}
defineProps<Props>();
</script>

<template>
    <div class="answers mt-4">
        <button
            v-for="answer in answers"
            :key="answer.id"
            class="option"
            :class="{
                correct: answer.id === correct_answer_id,
            }"
            @click="emit('setAnswerId', answer.id)"
        >
            {{ answer.text }}
            <span v-if="answer.image"
                ><br /><img
                    :src="'/storage/' + answer.image"
                    :alt="answer.text"
            /></span>
        </button>
    </div>
</template>
