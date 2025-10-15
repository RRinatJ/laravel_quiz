<script setup lang="ts">
import { ref } from 'vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { Button } from '@/components/ui/button';
import { FileImage } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { type Answer } from '@/types';
import {
  Card,
  CardContent,
  CardHeader,
} from '@/components/ui/card';
const emit = defineEmits(['addToFormAnswers', 'useInAnswers', 'setAnswerImage', 'deleteAnswer', 'deleteImageAnswer']);

interface Props {
    answers: Answer[],
    errors?: string,
}
defineProps<Props>();

const emptyAnswerDefault = {
    "id": "",
    "text": "",
    "image": '',
    "is_correct": false,
};
const cloneObject = (data: object) => {
    return JSON.parse(JSON.stringify(data));
}
const emptyAnswer = ref(cloneObject(emptyAnswerDefault));
const isPrev = ref<number[]>([]);

const generateRandomString = (length:number) => {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        result += characters.charAt(randomIndex);
    }
    return result;
}
const addToFormAnswers = () => {
    emptyAnswer.value.id = generateRandomString(10);
    emit('addToFormAnswers', cloneObject(emptyAnswer.value));
    emptyAnswer.value = cloneObject(emptyAnswerDefault);    
}
const getEventFile = (e:Event):File|false => {
    const files = (e.target as HTMLInputElement).files;
    if (!files || !files[0]){
        return false;
    }    
    return files[0];
}
const deleteAnswer = (index: number) => {
    emit('deleteAnswer', index);    
}
const deleteImageAnswer = (index: number) => {
    emit('deleteImageAnswer', index)
}
const setUploadedAnswerImage = (e: Event, i:number) => {    
    const answerFile = getEventFile(e);
    if(answerFile){
        emit('setAnswerImage', {
            index: i,
            file: answerFile,
        });
        isPrev.value.push(i);
    }
}
</script>

<template>
    <div>
        <Card>            
            <CardHeader class="flex px-6 pb-6">                                
                <Button @click="addToFormAnswers()">Add answer</Button>
                <InputError class="mt-2" :message="errors" />
            </CardHeader>
            <div class="p-4 space-y-4">
                <Card class="mt-4" v-for="(answer, index) in answers" :key="answer.id">
                    <CardContent>                                
                        <div>
                            <div class="grid items-center w-full gap-4 p-4">
                                <div class="flex flex-wrap -mx-3 mb-2">
                                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                                        <Label :for="'answer_form_text'+index">Text</Label>
                                        <Input :id="'answer_form_text'+index" placeholder="Text of answer" v-model="answer.text" />
                                    </div>
                                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                                        <Label :for="'answer_form_is_correct'+index">Is correct</Label>
                                        <div class="flex flex-row items-center">                                                    
                                            <Switch :id="'answer_form_is_correct'+index" v-model="answer.is_correct" />                                                    
                                        </div>
                                    </div>                                            
                                </div>                                 
                                <div class="flex flex-wrap -mx-3 mb-2 p-4">
                                    <div v-if="answer && answer.image" >
                                        <div class="w-48 mt-1 mr-4 mb-1" v-if="answer.image">
                                            <component v-if="isPrev.includes(index)" :is="FileImage" class="" /> 
                                            <img                           
                                                v-else 
                                                :src="'/storage/'+answer.image" 
                                                srcset=""
                                            >
                                        </div>                                
                                        <Button variant="destructive" @click="deleteImageAnswer(index)">Delete Image</Button>
                                    </div>
                                    <div>
                                        <Input 
                                            class="mt-1 block"                                                 
                                            type="file" 
                                            @input="setUploadedAnswerImage($event, index)" 
                                        />
                                    </div>                            
                                </div>
                                <div>                                            
                                    <Button variant="destructive" @click="deleteAnswer(index)">Delete Answer</Button>     
                                </div>
                            </div>                                    
                        </div>   
                    </CardContent>
                </Card>
            </div>
        </Card>
        
    </div>
</template>