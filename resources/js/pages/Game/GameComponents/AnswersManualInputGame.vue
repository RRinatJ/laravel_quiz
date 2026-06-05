<script setup lang="ts">
import { AnswerInGame } from '@/types';
import { ref } from 'vue';
const emit = defineEmits(['sendAnswerManualInput']);

interface Props {
    answers: AnswerInGame[];
    showCorrectAnswerMode: boolean;
}

defineProps<Props>();
const answerText = ref('');
const showError = ref(false);

const sendAnswerManualInput = () => {
    if (answerText.value.length < 3) {
        showError.value = true;
        return;
    }
    emit('sendAnswerManualInput', answerText.value);
};
</script>

<template>
    <div class="grid place-items-center">
        <div v-if="!showCorrectAnswerMode" class="w-full max-w-md items-center">
            <div class="flex items-center">
                <input
                    type="text"
                    placeholder="Enter answer..."
                    v-model="answerText"
                    class="w-full rounded-l-lg border border-r-0 border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                />
                <button
                    @click="sendAnswerManualInput"
                    class="rounded-r-lg bg-blue-600 px-5 py-3 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                >
                    Send
                </button>
            </div>
            <div v-if="showError" class="mt-2" style="">
                <p class="!dark:text-red-500 !text-sm !text-red-600">
                    Minimum length is 3 characters.
                </p>
            </div>
        </div>

        <div v-else class="max w-full">
            <div
                v-for="answer in answers"
                :key="answer.id"
                class="option correct mb-2"
            >
                {{ answer.text }}
            </div>
        </div>
    </div>
</template>
