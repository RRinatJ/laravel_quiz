<script setup lang="ts">
import PublicAppTemplate from '@/components/PublicAppTemplate.vue';
import { edit } from '@/routes/game';
import { like } from '@/routes/quiz';
import { AnswerInGame, Game } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Heart, Share2 } from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref } from 'vue';

interface Props {
    game: Game;
    answers: AnswerInGame[];
    questionsCount: number;
    error: string;
    message: string;
    correct_answer_id: number | null;
    firstQuestion: boolean;
    countDown: number;
    tmdbImage: boolean;
    quizLikeInfo: {
        liked: boolean;
        count: string;
    };
}

const props = defineProps<Props>();

const timer = ref(0);

const page = usePage();
const user = page.props.auth.user;
const countDown = ref(props.countDown);
const isErrorMode = computed(() => props.error.length !== 0);
const isMessageMode = computed(() => props.message.length !== 0);
const isAudioMode = computed(() => !!props.game.question?.audio);
const isStarted = ref(true);
const quizLikeInfo = ref(props.quizLikeInfo);

if (props.firstQuestion === true && isAudioMode.value) {
    isStarted.value = false;
}

onBeforeUnmount(() => {
    if (timer.value !== null) {
        clearTimeout(timer.value);
    }
});

type typeForm = {
    answer_id: number | null;
    sort_array: (number | string)[];
    fifty_fifty_hint: boolean;
    can_skip: boolean;
};

const form = useForm<typeForm>({
    answer_id: null,
    sort_array: props.answers ? props.answers.map((a) => a.id) : [],
    fifty_fifty_hint: false,
    can_skip: false,
});

const correctPrcnt = computed(() => {
    if (props.game.correct_count === 0) {
        return 0;
    }
    return Math.round((props.game.correct_count / props.questionsCount) * 100);
});

const setAnswerId = (choserAnswerId: number) => {
    form.answer_id = choserAnswerId;
    sendRequest();
};

const NewGame = () => {
    router.get('/');
};

const fiftyFiftyHint = () => {
    form.fifty_fifty_hint = true;
    sendRequest();
};

const skipQuestion = () => {
    form.can_skip = true;
    sendRequest();
};

const sendRequest = () => {
    form.submit(edit(props.game.id), {
        preserveScroll: true,
        preserveState: 'errors',
    });
    clearTimeout(timer.value);
};

const countDownTimer = () => {
    if (countDown.value !== undefined && countDown.value > 0) {
        timer.value = setTimeout(() => {
            countDown.value -= 1;
            countDownTimer();
        }, 1000);
    } else {
        sendRequest();
    }
};

const startAudio = () => {
    axios.get('/game/set_update/' + props.game.id).then((response) => {
        if (response.data && response.data.status !== undefined) {
            isStarted.value = true;
            countDownTimer();
        }
    });
};

const processingLike = ref(false);

const likeQuiz = () => {
    processingLike.value = true;
    axios.get(like(props.game.quiz.id).url).then((response) => {
        if (response.data) {
            quizLikeInfo.value = response.data;
        }
        processingLike.value = false;
    });
};

if (isErrorMode.value || isMessageMode.value || isStarted.value === false) {
    //
} else {
    countDownTimer();
}
</script>

<template>
    <Head title="Game" />
    <div>
        <PublicAppTemplate>
            <div id="showGame">
                <header class="px-10 py-3">
                    <div class="logo">{{ game.quiz.title }}</div>
                    <div class="timer text-2xl">{{ countDown }}</div>
                    <div class="progress">
                        <div class="flex justify-between gap-6">
                            <p class="text-base leading-normal font-medium">
                                Question {{ game.correct_count }}/{{
                                    questionsCount
                                }}
                            </p>
                        </div>
                        <div class="rounded bg-[#cedce8]">
                            <div
                                class="h-2 rounded bg-[#0b80ee]"
                                :style="{ width: correctPrcnt + '%' }"
                            ></div>
                        </div>
                    </div>
                </header>
                <main class="mt-6">
                    <div class="question">
                        <img
                            v-if="game.question?.image"
                            :src="'/storage/' + game.question.image"
                        />
                        <div class="audio" v-if="isAudioMode">
                            <audio
                                v-if="isStarted"
                                controls
                                :autoplay="
                                    isErrorMode === false &&
                                    isMessageMode === false
                                "
                            >
                                <source
                                    :src="'/storage/' + game.question?.audio"
                                    type="audio/mpeg"
                                />
                                Your browser does not support the audio element.
                            </audio>
                            <button
                                v-if="isStarted === false"
                                @click="startAudio"
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
                        <p v-if="game.question?.question">
                            {{ game.question.question }}
                        </p>
                        <div v-if="isErrorMode || isMessageMode" class="mb-4">
                            <p
                                :class="{
                                    'error-red': isErrorMode,
                                    message: isMessageMode,
                                }"
                            >
                                {{ error }} {{ message }}
                            </p>
                        </div>
                        <div class="answers mt-4">
                            <button
                                v-for="answer in answers"
                                :key="answer.id"
                                class="option"
                                :class="{
                                    correct: answer.id === correct_answer_id,
                                }"
                                :disabled="isErrorMode || isMessageMode"
                                @click="setAnswerId(answer.id)"
                            >
                                {{ answer.text }}
                                <span v-if="answer.image"
                                    ><br /><img
                                        :src="'/storage/' + answer.image"
                                        :alt="answer.text"
                                /></span>
                            </button>
                        </div>
                        <div class="mt-6">
                            <button
                                v-if="isErrorMode || isMessageMode"
                                class="option info"
                                @click="NewGame"
                            >
                                New Game
                            </button>
                        </div>
                        <div
                            v-if="
                                isErrorMode === false && isMessageMode === false
                            "
                            class="mt-6 flex justify-center gap-4"
                        >
                            <button
                                v-if="game.fifty_fifty_hint"
                                class="option info"
                                @click="fiftyFiftyHint()"
                            >
                                50/50
                            </button>
                            <button
                                v-if="game.can_skip"
                                class="option info"
                                @click="skipQuestion()"
                            >
                                Skip
                            </button>
                        </div>
                        <div
                            v-if="tmdbImage"
                            class="mt-4 flex px-4 pt-1 pb-3 text-sm leading-normal font-normal text-[#49749c]"
                        >
                            <span class="mr-2">Image provided by</span>
                            <a
                                href="https://www.themoviedb.org/"
                                target="_blank"
                                rel="noreferrer noopener"
                            >
                                <img
                                    class="!mb-0"
                                    alt="TMDB Logo"
                                    loading="lazy"
                                    width="40"
                                    height="40"
                                    decoding="async"
                                    data-nimg="1"
                                    style="color: transparent"
                                    src="https://www.themoviedb.org/assets/2/v4/logos/v2/blue_square_2-d537fb228cf3ded904ef09b136fe3fec72548ebc1fea3fbbd1ad9e36364db38b.svg"
                                />
                            </a>
                        </div>
                        <div v-if="game.question?.is_ai" class="mt-4 text-left">
                            <span
                                class="px-4 pt-1 pb-3 text-sm leading-normal font-normal text-[#49749c]"
                                >The question was generated by AI</span
                            >
                        </div>
                        <div
                            v-if="isMessageMode || isErrorMode"
                            class="mt-4 flex flex-col items-center justify-between gap-6 border-t border-[#dbe0e6] bg-white p-8 sm:flex-row"
                        >
                            <div class="text-center sm:text-left">
                                <h4 class="font-bold text-[#111418]">
                                    Did you enjoy this quiz?
                                </h4>
                                <p class="!text-sm !text-[#617589]">
                                    Your feedback helps us create better
                                    content.
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center">
                                    <button
                                        @click="likeQuiz"
                                        :disabled="
                                            processingLike || user === null
                                        "
                                        class="group flex items-center gap-2 rounded-l-xl border-2 border-blue-500/20 px-6 py-2.5 font-bold text-blue-500 transition-all hover:bg-blue-500/5 active:scale-95"
                                    >
                                        <span
                                            class="material-symbols-outlined !text-2xl transition-transform group-hover:scale-125"
                                        >
                                            <Heart
                                                :class="{
                                                    'fill-blue-500':
                                                        quizLikeInfo?.liked,
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
                                    <span
                                        class="material-symbols-outlined !text-2xl"
                                        ><Share2
                                    /></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </PublicAppTemplate>
    </div>
</template>
