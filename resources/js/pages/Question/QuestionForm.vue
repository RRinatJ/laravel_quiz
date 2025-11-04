<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Quiz, type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { type Question, type Answer } from '@/types';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import FormAnswers from "@/pages/Question/QuestionFormComponents/FormAnswers.vue";
import { store, update } from '@/routes/question';

interface Props {
    question?: { data: object};
    message?: string;
    quizzes: Quiz[];
}

const props = defineProps<Props>();

const question = props.question?.data as Question;
const isEditMode = computed(() => !!props.question);

type questionForm = {
    id: number | null;
    question: string;
    uploaded_image: File | null;
    uploaded_audio: File | null;
    image: string;    
    audio: string;
    quizzes: number[];  
    answer_images: File[];    
    answers: Answer[];
};

const form = useForm<questionForm>({
    id: question?.id || null,
    question: question?.question || "",
    uploaded_image: null,
    uploaded_audio: null,
    image: question?.image || "",
    audio: question?.audio || "",
    quizzes: question?.quizzes_ids || [],
    answer_images: [],
    answers: question?.answers || [],
});

const answers = ref<Answer[]>(question?.answers || []);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Question List',
        href: '/question',
    },
];
if (isEditMode.value) {
    breadcrumbs.push(
        {
            title: question.id.toString(),
            href: '/question/' + question.id + '/edit',
        }
    )
} else {
    breadcrumbs.push(
        {
            title: 'Create',
            href: '/create',
        }   
    )
}

const submitText = computed(() => {
    if (isEditMode.value) {
        return form.processing ? 'Updating...' : 'Update';
    }else{
        return form.processing ? 'Creating...' : 'Create';
    }

});

const submit = () => {
    if (isEditMode.value) {
        form.transform((data) => ({
            ...data,
            answers: JSON.stringify(answers.value),
        })).post(update(question.id).url, {
            preserveScroll: true,
            preserveState : "errors",
            onSuccess: () => form.reset()
        });
    } else {
        form.transform((data) => ({
            ...data,
            answers: JSON.stringify(answers.value),
        })).post(store().url, {
            preserveScroll: true,
            preserveState : "errors",
            onSuccess: () => form.reset()
        });    
    }
};

const getEventFile = (e:Event):File|false => {
    const files = (e.target as HTMLInputElement).files;
    if (!files || !files[0]){
        return false;
    }    
    return files[0];
}
const setUploadedImage = (e: Event) => {
    const file = getEventFile(e);
    if(file){
        form.uploaded_image = file;
    }
}
const setUploadedAudio = (e: Event) => {
    const file = getEventFile(e);
    if(file){
        form.uploaded_audio = file;
    }
}
const setUploadedAnswerImage = (data:{file:File, index: number}) => { 
    form.answer_images.push(data.file);
    answers.value[data.index].image = data.file.name;
}
const deleteImage = () => {
    form.image = '';
    form.uploaded_image = null;
    question.image = '';    
}
const deleteAudio = () => {
    form.audio = '';
    form.uploaded_audio = null;
    question.audio = '';
}
const deleteImageAnswer = (i:number) => {
    answers.value[i].image = '';
}
const deleteAnswer = (i:number) => {
    answers.value.splice(i, 1);
}
const addToFormAnswers = (answer: Answer) => {    
    answers.value.push(answer);
}
const handleChange = (newValue: boolean | 'indeterminate', itemId: number)=>{
    if(newValue === 'indeterminate'){
        return;
    }
    if (newValue) {
        form.quizzes.push(itemId);
    } else {
        form.quizzes = form.quizzes.filter((id) => id !== itemId);
    }
}
</script>

<template>
    <Head :title="isEditMode ? 'Update Question' : 'Create Question'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 v-if="isEditMode" class="text-2xl font-bold">Update Question</h1>
                <h1 v-else class="text-2xl font-bold">Create Question</h1>
            </div>
            <div class="shadow rounded-lg p-4 mb-6">                
                <div>
                    <div v-if="props.message" class="mb-4">
                        <div
                            class="p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800"
                            role="alert"
                        >
                            <span class="font-medium">
                                {{props.message}}
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <Label for="question">Question</Label>
                        <Input 
                            id="question" 
                            class="mt-1 block w-full" 
                            v-model="form.question" 
                            required 
                            autocomplete="question" 
                            placeholder="Question text" 
                        />
                        <InputError class="mt-2" :message="form.errors.question" />                        
                    </div>                    
                    <div class="mb-4">                        
                        <Label for="image">Image</Label>
                        <img 
                            v-if="question && question.image" 
                            class="w-96 mt-1" 
                            :src="'/storage/'+question.image" 
                            srcset=""
                        >    
                        <div class="mt-2" v-if="question && question.image"> 
                            <Button variant="destructive" @click="deleteImage">Delete Image</Button>                  
                        </div>
                        <div class="mt-2"> 
                            <Input 
                                class="mt-1 block" 
                                id="image" 
                                type="file" 
                                @input="setUploadedImage" 
                            />                        
                            <InputError class="mt-2" :message="form.errors.image" />                        
                        </div>
                    </div>
                    <div class="mb-4">                        
                        <Label for="audio">Audio</Label>
                        <audio
                            v-if="question && question.audio"
                            id="audio"  
                            class="mt-1"  
                            ref="audio"                                            
                            controls                                        
                            :src="'/storage/'+question.audio"
                        />    
                        <div class="mt-2" v-if="question && question.audio"> 
                            <Button variant="destructive" @click="deleteAudio">Delete Audio</Button>                  
                        </div>
                        <div class="mt-2"> 
                            <Input 
                                class="mt-1 block" 
                                id="image" 
                                type="file" 
                                @input="setUploadedAudio" 
                            />                        
                            <InputError class="mt-2" :message="form.errors.audio" />
                        </div>
                    </div>
                    <div class="mb-4 space-y-2">        
                        <Label>Quizzes</Label>                                        
                        <div v-for="quiz in quizzes" :key="quiz.id" class="flex flex-row items-center space-x-3 space-y-0">                            
                            <Checkbox                
                                :id="'quizcheckbox'+quiz.id"
                                :model-value="form.quizzes.includes(quiz.id)"
                                @update:model-value="(newValue) => handleChange(newValue, quiz.id)"
                            />                                
                            <Label class="font-normal" :for="'quizcheckbox'+quiz.id">
                                {{ quiz.title }}
                            </Label>                            
                        </div>                        
                    </div>
                    <div class="mb-4">
                        <form-answers 
                            :answers="answers"       
                            :errors="form.errors.answers"                      
                            @add-to-form-answers="addToFormAnswers"
                            @set-answer-image="setUploadedAnswerImage"
                            @delete-answer="deleteAnswer"
                            @delete-image-answer="deleteImageAnswer"
                        ></form-answers>
                    </div>                    
                    <Button @click="submit" :disabled="form.processing" size="lg" >
                        {{ submitText }}
                    </Button>
                </div>
            </div>
        </div>        
    </AppLayout>
</template>