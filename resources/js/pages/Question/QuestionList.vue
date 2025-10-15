<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Quiz, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { type Question, type PaginatedResponse } from '@/types';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import OnOffIcon from '@/components/OnOffIcon.vue';
import { Badge } from '@/components/ui/badge';
import { ref, watch } from 'vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { TailwindPagination } from 'laravel-vue-pagination';
import { create, edit, destroy } from '@/routes/question';

interface Props {
    questions: PaginatedResponse<Question>;
    quizzes: Quiz[];
    message?: string;
    error?: string;
    filters?: {
        quiz_id: number | null;
    };
}

const props = defineProps<Props>();

const quiz_id = ref(props.filters?.quiz_id || null);

watch(quiz_id, () => {
    filter()
});

const filter = () => {
    router.get('/question', {
        quiz_id: quiz_id.value,
    }, {
        preserveState: true,
        replace: true
    })
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Question List',
        href: '/question',
    },
];

const form = useForm({
    id: null,
});

const deleteQuestion = (id:number) => {
    if(confirm("Are you sure you want to remove this question?")){        
        form.delete(destroy(id).url, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Question List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Quiz list</h1>
            </div>
            <div v-if="error" class="mb-6">
                <div
                    class="p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800"
                    role="alert"
                >
                    <span class="font-medium">
                    {{ error }}
                    </span>
                </div>
            </div>
            <div v-if="message" class="mb-6">
                <div
                class="p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800"
                role="alert"
                >
                <span class="font-medium">
                    {{ message }}
                </span>
                </div>
            </div>
            <div class="shadow rounded-lg p-4 mb-6">  
                <Button size="lg" class="mr-2 h-9 w-9">
                    <Link :href="create()" >
                        Create
                    </Link> 
                </Button>
                <br><br>
                <div class="max-w-7xl flex items-center py-4">                    
                    <Select v-model="quiz_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Choose a Quiz" />
                        </SelectTrigger>
                        <SelectContent>
                        <SelectGroup>                            
                            <SelectItem :value="null">
                                Choose a Quiz
                            </SelectItem>                            
                            <SelectItem v-for="quiz_option in quizzes" :value="quiz_option.id" :key="quiz_option.id">
                                {{ quiz_option.title }}
                            </SelectItem>                            
                        </SelectGroup>
                        </SelectContent>
                    </Select>                
                </div>                
                <table class="min-w-full shadow rounded-lg">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left border-b">ID</th>
                            <th class="py-2 px-4 text-left border-b">Question</th>
                            <th class="py-2 px-4 text-left border-b w-32">Image</th>                            
                            <th class="py-2 px-4 text-left border-b">Quizzes</th>
                            <th class="py-2 px-4 text-left border-b">Answers</th>
                            <th class="py-2 px-4 text-left border-b">Edit/Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="question in questions.data" :key="question.id" >
                            <td class="py-2 px-4 border-b">{{ question.id }}</td>
                            <td class="py-2 px-4 border-b">{{ question.question }}</td>                        
                            <td class="py-2 px-4 border-b">
                                <img 
                                    v-if="question.image" 
                                    class="w-32 h-auto rounded"
                                    :src="question.image" 
                                    srcset=""
                                >
                            </td>
                            <td class="py-2 px-4 border-b">                            
                                <ul>
                                    <li v-for="question_quiz in question.quizzes_full" :key="question_quiz.id">
                                        {{ question_quiz.title }} 
                                        <OnOffIcon v-if="question_quiz.is_work !== undefined" :check-value="question_quiz.is_work" :size="'sm'" />
                                    </li>
                                </ul>
                            </td>                        
                            <td class="py-2 px-4 border-b">   
                                <div class="grid auto-cols-max grid-flow-col gap-4">
                                    <div 
                                        v-for="answer in question.answers" 
                                        :key="answer.id" 
                                    >
                                        <img 
                                            v-if="answer.image" 
                                            class="w-16 h-auto rounded"
                                            :src="answer.image" 
                                            srcset=""
                                        >    
                                        <Badge     
                                            v-if="answer.text"                                   
                                            :variant="answer.is_correct ? 'destructive' : 'default'"
                                            class="mr-1"
                                        >
                                            {{ answer.text }}
                                        </Badge>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-4 border-b">                            
                                <Button size="lg" class="mb-2 mr-2 h-9 w-9">
                                    <Link :href="edit(question.id)" >
                                    Edit
                                    </Link>
                                </Button>
                                <Button variant="destructive" size="lg" class="mb-2 mr-2 h-9 w-9"  @click="deleteQuestion(question.id)">
                                    Delete
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>                
                <div class="mt-4" v-if="questions !== undefined">
                    <TailwindPagination :data="questions" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>