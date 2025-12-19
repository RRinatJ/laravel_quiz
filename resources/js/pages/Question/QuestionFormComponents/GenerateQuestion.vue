<script setup lang="ts">
import { ref } from 'vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import axios, { AxiosError } from 'axios';
import { type aiQuestionText } from '@/types';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import OnOffIcon from '@/components/OnOffIcon.vue';
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
    axios.get('/ai/get_question/',
        { params: { theme: theme.value, number_of_options:number_of_options.value } }
    ).then(response => {        
        if(response.data){
            questionText.value = response.data;
        }
        processing.value = false;
    }).catch(error => {
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
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Generate Question</CardTitle>
            <CardDescription>Get generated question from AI</CardDescription>
        </CardHeader>
        <CardContent>               
            <form class="w-full" @submit.prevent>
                <div class="grid items-center w-full gap-4">
                    <div class="flex flex-wrap -mx-3 mb-2">
                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                            <Label for="theme">Theme</Label>
                            <Input id="theme" placeholder="Theme of question" v-model="theme" />
                            <InputError class="mt-2" v-if="'theme' in questionError" :message="questionError.theme[0]" />
                            <InputError class="mt-2" v-if="'error' in questionError" :message="questionError.error[0]" />
                        </div>
                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                            <Label for="number_of_options">Number of options</Label>
                            <Input id="number_of_options" placeholder="4" v-model="number_of_options" />
                            <InputError class="mt-2" v-if="'number_of_options' in questionError" :message="questionError.number_of_options[0]" />
                        </div>
                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0 flex items-end">
                            <Button @click="generateQuestion()" :disabled="processing">Get</Button>
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
                    <table class="min-w-full shadow rounded-lg">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 text-left border-b">Answer</th>
                                <th class="py-2 px-4 text-left border-b">Is Correct</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(answer, index) in questionText.answers" :key="index">
                                <td class="py-2 px-4 border-b">{{ answer.answer }}</td>
                                <td class="py-2 px-4 border-b"><OnOffIcon :check-value="answer.is_correct" /></td>                             
                            </tr>
                        </tbody>
                    </table>     
                </div>
                <div>
                    <Button @click="useQuestion()" :disabled="processing">Use</Button>
                </div>                
            </div>            
        </CardContent>        
    </Card>
</template>