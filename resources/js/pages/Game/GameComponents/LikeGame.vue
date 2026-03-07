<script setup lang="ts">
import { like } from '@/routes/quiz';
import axios from 'axios';
import { Heart, Share2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    quizLikeInfo: {
        liked: boolean;
        count: string;
    };
    quiz_id: number;
    is_authenticated: boolean;
}
const props = defineProps<Props>();

const processingLike = ref(false);
const quizLikeInfo = ref(props.quizLikeInfo);
const likeQuiz = () => {
    processingLike.value = true;
    axios.get(like(props.quiz_id).url).then((response) => {
        if (response.data) {
            quizLikeInfo.value = response.data;
        }
        processingLike.value = false;
    });
};
</script>

<template>
    <div
        class="flex flex-col items-center justify-between gap-6 rounded-b-lg border-t border-[#dbe0e6] bg-white p-8 sm:flex-row"
    >
        <div class="text-center sm:text-left">
            <h4 class="font-bold text-[#111418]">Did you enjoy this quiz?</h4>
            <p class="!text-sm !text-[#617589]">
                Your feedback helps us create better content.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center">
                <button
                    @click="likeQuiz"
                    :disabled="processingLike || !is_authenticated"
                    class="group flex items-center gap-2 rounded-l-xl border-2 border-blue-500/20 px-6 py-2.5 font-bold text-blue-500 transition-all hover:bg-blue-500/5 active:scale-95"
                >
                    <span
                        class="material-symbols-outlined !text-2xl transition-transform group-hover:scale-125"
                    >
                        <Heart
                            :class="{
                                'fill-blue-500': quizLikeInfo?.liked,
                            }"
                        />
                    </span>
                    Like Quiz
                </button>
                <div
                    class="flex h-[48px] items-center justify-center rounded-r-xl border-2 border-l-0 border-blue-500/20 !bg-blue-500/5 px-4 !text-sm !font-bold !text-blue-500"
                >
                    {{ quizLikeInfo?.count || 0 }}
                </div>
            </div>
            <button
                v-if="false"
                class="flex items-center justify-center rounded-xl border-2 border-[#dbe0e6] p-2.5 text-[#617589] transition-all hover:bg-gray-50"
            >
                <span class="material-symbols-outlined !text-2xl">
                    <Share2 />
                </span>
            </button>
        </div>
    </div>
</template>
