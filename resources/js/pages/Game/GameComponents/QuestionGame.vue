<script setup lang="ts">
import { Question } from '@/types';

const emit = defineEmits(['startAudio']);

interface Props {
    question: Question;
    isStarted: boolean;
    isAudioMode: boolean;
    autoPlayAudio: boolean;
}
defineProps<Props>();
</script>

<template>
    <div>
        <img v-if="question.image" :src="'/storage/' + question.image" />
        <div class="audio" v-if="isAudioMode">
            <audio v-if="isStarted" controls :autoplay="autoPlayAudio">
                <source :src="'/storage/' + question.audio" type="audio/mpeg" />
                Your browser does not support the audio element.
            </audio>
            <button
                v-if="isStarted === false"
                @click="emit('startAudio')"
                class="option info"
            >
                Play
            </button>
            <div class="mt-4 text-center">
                <p
                    v-if="isStarted === false"
                    class="text-xs text-gray-600 italic"
                >
                    Countdown start when Play button push
                </p>
            </div>
        </div>
        <p v-if="question.question">
            {{ question.question }}
        </p>
    </div>
</template>
