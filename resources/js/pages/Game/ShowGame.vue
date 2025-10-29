<script setup lang="ts">
import { AnswerInGame, Game } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref } from 'vue';
import PublicAppTemplate from '@/components/PublicAppTemplate.vue';
import { edit } from '@/routes/game';

interface Props {
    game: Game;    
    answers: AnswerInGame[];
    questionsCount: number;
    error: string;
    message: string;
    correct_answer_id: number | null;
    firstQuestion: boolean;
    countDown: number;
}

const props = defineProps<Props>();

const timer = ref(0);

const countDown = ref(props.countDown);
const isErrorMode = computed(() => props.error.length !== 0);
const isMessageMode = computed(() => props.message.length !== 0);

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
    sort_array: props.answers ? props.answers.map(a => a.id) : [],
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
}

const NewGame = () => {
    router.get('/');
}

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
        preserveState : "errors",
    });
    clearTimeout(timer.value);
};

const countDownTimer = () => {
    if (countDown.value !== undefined && countDown.value > 0) {
        timer.value = setTimeout(() => {
            countDown.value -= 1;
            countDownTimer();
        }, 1000)
    } else {
        sendRequest();
    }
};

if(isErrorMode.value || isMessageMode.value){
    // 
}else{    
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
                    <div class="progress"><div class="flex gap-6 justify-between"><p class="text-base font-medium leading-normal">Question {{ game.correct_count }}/{{ questionsCount }}</p></div>
                        <div class="rounded bg-[#cedce8]">
                            <div class="h-2 rounded bg-[#0b80ee]" :style="{'width': correctPrcnt+'%'}"></div>
                        </div></div>
                </header>
                <main class="mt-6">                    
                    <div class="question">
                        <img 
                            v-if="game.question?.image" 
                            :src="'/storage/' + game.question.image" 
                        >
                        <p v-if="game.question?.question">
                            {{ game.question.question }}
                        </p>
                        <div v-if="isErrorMode || isMessageMode" class="mb-4">
                            <p :class="
                                {
                                    'error-red': isErrorMode, 
                                    'message': isMessageMode
                                }
                            ">
                                {{ error }} {{ message }}
                            </p>
                        </div>                
                        <div class="answers mt-4">
                            <button 
                                v-for="answer in answers" 
                                :key="answer.id" 
                                class="option"
                                :class="{ correct: answer.id === correct_answer_id }"      
                                :disabled="isErrorMode || isMessageMode"                  
                                @click="setAnswerId(answer.id)"
                            >
                                {{ answer.text }}
                                <span v-if="answer.image"><br><img :src="'/storage/' + answer.image" :alt="answer.text"></span>
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
                            v-if="isErrorMode === false && isMessageMode === false"    
                            class="flex gap-4 mt-6 justify-center"         
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
                    </div>                    
                </main>                
            </div>
        </PublicAppTemplate>
    </div>    
</template>