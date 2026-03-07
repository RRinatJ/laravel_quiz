<script setup lang="ts">
import PublicAppTemplate from '@/components/PublicAppTemplate.vue';
import { edit, set_update } from '@/routes/game';
import { AnswerInGame, Game } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onBeforeUnmount, ref } from 'vue';
import AnswersGame from './GameComponents/AnswersGame.vue';
import HeaderGame from './GameComponents/HeaderGame.vue';
import HintsGame from './GameComponents/HintsGame.vue';
import LikeGame from './GameComponents/LikeGame.vue';
import NavigationBottomButtons from './GameComponents/NavigationBottomButtons.vue';
import QuestionGame from './GameComponents/QuestionGame.vue';
import QuestionIsAi from './GameComponents/QuestionIsAi.vue';
import ResultHeaderGame from './GameComponents/ResultHeaderGame.vue';
import ResultTableGame from './GameComponents/ResultTableGame.vue';
import TmdbImageGame from './GameComponents/TmdbImageGame.vue';

interface Props {
    game: Game;
    answers: AnswerInGame[];
    questionsCount: number;
    currentStep: number;
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

const completionPrcnt = computed(() => {
    if (props.currentStep === 0) {
        return 0;
    }
    return Math.round((props.currentStep / props.questionsCount) * 100);
});

const correctPrcnt = computed(() => {
    if (props.game.correct_count === 0) {
        return 0;
    }
    return Math.round((props.game.correct_count / props.questionsCount) * 100);
});

const hintsUsed = computed(() => {
    let hintsUsed = 0;
    let hintsAvailable = 0;
    if (props.game.quiz.fifty_fifty_hint) {
        hintsAvailable += 1;
        if (!props.game.fifty_fifty_hint) {
            hintsUsed += 1;
        }
    }
    if (props.game.quiz.can_skip) {
        hintsAvailable += 1;
        if (!props.game.can_skip) {
            hintsUsed += 1;
        }
    }
    return hintsUsed + '/' + hintsAvailable;
});

const setAnswerId = (choserAnswerId: number) => {
    form.answer_id = choserAnswerId;
    sendRequest();
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
    axios.get(set_update.url(props.game.id)).then((response) => {
        if (response.data && response.data.status !== undefined) {
            isStarted.value = true;
            countDownTimer();
        }
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
                <HeaderGame
                    v-if="isErrorMode === false && isMessageMode === false"
                    :quiz_title="game.quiz.title"
                    :countDown="countDown"
                    :currentStep="currentStep"
                    :questionsCount="questionsCount"
                    :сompletionPrcnt="completionPrcnt"
                />
                <main class="mt-6">
                    <div
                        v-if="isErrorMode === false && isMessageMode === false"
                        class="question"
                    >
                        <QuestionGame
                            v-if="game.question"
                            :question="game.question"
                            :isAudioMode="isAudioMode"
                            :autoPlayAudio="
                                isErrorMode === false && isMessageMode === false
                            "
                            :isStarted="isStarted"
                            @startAudio="startAudio"
                        />
                        <AnswersGame
                            :answers="answers"
                            :correct_answer_id="correct_answer_id"
                            @setAnswerId="setAnswerId"
                        />
                        <HintsGame
                            :fifty_fifty_hint="game.fifty_fifty_hint"
                            :can_skip="game.can_skip"
                            @fiftyFiftyHint="fiftyFiftyHint()"
                            @skipQuestion="skipQuestion()"
                        />
                        <TmdbImageGame v-if="tmdbImage" />
                        <QuestionIsAi v-if="game.question?.is_ai" />
                    </div>
                    <div v-else>
                        <ResultHeaderGame
                            :quiz_title="game.quiz.title"
                            :user_name="user ? user.name : 'Player'"
                            :isErrorMode="isErrorMode"
                            :correctPrcnt="correctPrcnt"
                        />
                        <div
                            class="rounded-xl border border-[#dbe0e6] shadow-sm"
                        >
                            <ResultTableGame
                                :correctCount="game.correct_count"
                                :questionsCount="questionsCount"
                                :correctPrcnt="correctPrcnt"
                                :timeTaken="game.time_taken"
                                :hintsUsed="hintsUsed"
                                :isErrorMode="isErrorMode"
                            />
                            <LikeGame
                                :quizLikeInfo="quizLikeInfo"
                                :quiz_id="game.quiz.id"
                                :is_authenticated="!!user"
                            />
                        </div>
                        <NavigationBottomButtons :quiz_id="game.quiz.id" />
                    </div>
                </main>
            </div>
        </PublicAppTemplate>
    </div>
</template>
