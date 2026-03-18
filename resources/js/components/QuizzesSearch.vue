<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { search as quizSearch } from '@/routes/quiz';
import { Quiz } from '@/types';
import { useDebounceFn } from '@vueuse/core';
import axios, { AxiosError } from 'axios';
import { Search, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import Overlay from './Overlay.vue';

const emit = defineEmits(['addToQuizzes', 'filterQuizzes']);
interface Props {
    quizzes: Quiz[];
}
defineProps<Props>();

interface ValidationErrors {
    [field: string]: string[];
}
const quizTitle = ref('');
const processing = ref(false);
const searchQuizzes = ref<Quiz[]>([]);
const searchError = ref<ValidationErrors | []>([]);

const search = useDebounceFn(() => {
    searchError.value = [];
    processing.value = true;
    if (quizTitle.value.length < 3) {
        searchQuizzes.value = [];
        return;
    }
    axios
        .get(quizSearch().url, {
            params: {
                quiz_title: quizTitle.value,
            },
        })
        .then((response) => {
            if (response.data) {
                searchQuizzes.value = response.data;
            }
            processing.value = false;
        })
        .catch((error) => {
            if (axios.isAxiosError(error)) {
                const serverError = error as AxiosError<{
                    errors: ValidationErrors;
                }>;
                if (serverError.response?.data?.errors) {
                    searchError.value = serverError.response.data.errors;
                }
            }
            processing.value = false;
        });
}, 500);

watch(quizTitle, () => {
    search();
});
</script>

<template>
    <div class="space-y-8 lg:col-span-4">
        <div
            class="rounded-2xl border-2 border-slate-200 bg-white p-6 shadow-sm"
        >
            <h4
                class="mb-4 text-sm font-bold tracking-wider text-slate-800 uppercase"
            >
                Assign to Quizzes
            </h4>

            <div class="relative mb-4">
                <span
                    class="material-symbols-outlined absolute top-1/2 left-3 -translate-y-1/2 text-lg text-slate-400"
                    ><Search
                /></span>
                <Input
                    class="w-full rounded-xl border-slate-200 pr-4 pl-10 text-sm placeholder:text-slate-400 focus:border-primary focus:ring-primary"
                    v-model="quizTitle"
                    autocomplete="title"
                    placeholder="Search quizzes..."
                />
            </div>
            <InputError
                class="mt-2"
                v-if="'quiz_title' in searchError"
                :message="searchError.quiz_title[0]"
            />
            <Overlay :isBlur="processing">
                <div
                    class="custom-scrollbar max-h-[300px] space-y-3 overflow-y-auto pr-2"
                >
                    <label
                        v-for="search_quiz in searchQuizzes"
                        :key="search_quiz.id"
                        class="flex cursor-pointer items-center gap-3 rounded-lg p-3 transition-colors hover:bg-slate-50"
                        @click="emit('addToQuizzes', search_quiz)"
                    >
                        <div>
                            <p class="text-sm font-bold text-slate-800">
                                {{ search_quiz.title }}
                            </p>
                            <p
                                v-if="
                                    Object.hasOwn(
                                        search_quiz,
                                        'questions_count',
                                    )
                                "
                                class="text-[10px] font-medium text-slate-400"
                            >
                                {{ search_quiz.questions_count }} Questions
                            </p>
                        </div>
                    </label>
                </div>
            </Overlay>
            <div
                v-if="quizzes.length"
                class="mt-4 flex min-h-[40px] flex-wrap gap-2"
            >
                <div
                    v-for="quiz in quizzes"
                    :key="quiz.id"
                    class="flex items-center gap-1.5 rounded-full bg-[#6366f1]/10 px-3 py-1.5 text-xs font-bold text-[#6366f1]"
                >
                    <span>
                        <p class="text-sm font-bold text-slate-800">
                            {{ quiz.title }}
                        </p>
                        <p
                            v-if="Object.hasOwn(quiz, 'questions_count')"
                            class="text-[10px] font-medium text-slate-400"
                        >
                            {{ quiz.questions_count }} Questions
                        </p>
                    </span>
                    <button
                        @click="emit('filterQuizzes', quiz)"
                        class="flex items-center justify-center rounded-full transition-colors hover:bg-[#6366f1]/20"
                    >
                        <span class="material-symbols-outlined text-[14px]"
                            ><X :size="18"
                        /></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
