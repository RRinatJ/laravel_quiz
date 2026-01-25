<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import OnOffIcon from '@/components/OnOffIcon.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { get_question } from '@/routes/ai';
import { type aiQuestionText } from '@/types';
import axios, { AxiosError } from 'axios';
import { ref } from 'vue';
const emit = defineEmits(['useGenerateQuestion']);

interface ValidationErrors {
    [field: string]: string[];
}

const theme = ref('');
const number_of_options = ref(4);
const questionError = ref<ValidationErrors | []>([]);

const questionText = ref<aiQuestionText | null>(null);

const processing = ref(false);

const generateQuestion = () => {
    questionError.value = [];
    processing.value = true;
    axios
        .get(get_question().url, {
            params: {
                theme: theme.value,
                number_of_options: number_of_options.value,
            },
        })
        .then((response) => {
            if (response.data) {
                questionText.value = response.data;
            }
            processing.value = false;
        })
        .catch((error) => {
            if (axios.isAxiosError(error)) {
                const serverError = error as AxiosError<{
                    errors: ValidationErrors;
                }>;
                if (serverError.response?.data?.errors) {
                    questionError.value = serverError.response.data.errors;
                }
            }
            processing.value = false;
        });
};

const useQuestion = () => {
    emit('useGenerateQuestion', questionText.value);
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Generate Question</CardTitle>
            <CardDescription>Get generated question from AI</CardDescription>
        </CardHeader>
        <CardContent>
            <form class="w-full" @submit.prevent>
                <div class="grid w-full items-center gap-4">
                    <div class="-mx-3 mb-2 flex flex-wrap">
                        <div class="mb-6 w-full px-3 md:mb-0 md:w-1/3">
                            <Label for="theme">Theme</Label>
                            <Input
                                id="theme"
                                placeholder="Theme of question"
                                v-model="theme"
                            />
                            <InputError
                                class="mt-2"
                                v-if="'theme' in questionError"
                                :message="questionError.theme[0]"
                            />
                            <InputError
                                class="mt-2"
                                v-if="'error' in questionError"
                                :message="questionError.error[0]"
                            />
                        </div>
                        <div class="mb-6 w-full px-3 md:mb-0 md:w-1/3">
                            <Label for="number_of_options"
                                >Number of options</Label
                            >
                            <Input
                                id="number_of_options"
                                placeholder="4"
                                v-model="number_of_options"
                            />
                            <InputError
                                class="mt-2"
                                v-if="'number_of_options' in questionError"
                                :message="questionError.number_of_options[0]"
                            />
                        </div>
                        <div
                            class="mb-6 flex w-full items-end px-3 md:mb-0 md:w-1/3"
                        >
                            <Button
                                @click="generateQuestion()"
                                :disabled="processing"
                                >Get</Button
                            >
                        </div>
                    </div>
                </div>
            </form>
            <div v-if="questionText !== null" class="mt-4">
                <div class="mb-4">
                    <p class="font-bold">Text</p>
                    <p>{{ questionText.text }}</p>
                </div>
                <div class="mb-4">
                    <p class="font-bold">Description</p>
                    <p>{{ questionText.description }}</p>
                </div>
                <div class="mb-4">
                    <table class="min-w-full rounded-lg shadow">
                        <thead>
                            <tr>
                                <th class="border-b px-4 py-2 text-left">
                                    Answer
                                </th>
                                <th class="border-b px-4 py-2 text-left">
                                    Is Correct
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(answer, index) in questionText.answers"
                                :key="index"
                            >
                                <td class="border-b px-4 py-2">
                                    {{ answer.answer }}
                                </td>
                                <td class="border-b px-4 py-2">
                                    <OnOffIcon
                                        :check-value="answer.is_correct"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <Button @click="useQuestion()" :disabled="processing"
                        >Use</Button
                    >
                </div>
            </div>
        </CardContent>
    </Card>
</template>
