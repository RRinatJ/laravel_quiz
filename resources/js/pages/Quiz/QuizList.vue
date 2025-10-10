<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { type Quiz, type PaginatedResponse } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import OnOffIcon from '@/components/OnOffIcon.vue';
import { TailwindPagination } from 'laravel-vue-pagination';
import { create, edit, destroy } from '@/routes/quiz';

interface Props {
    quizzes: PaginatedResponse<Quiz>;
    message?: string;
    error?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Quiz List',
        href: '/quiz',
    },
];

const form = useForm({
    id: null,
});

const deleteQuiz = (id:number) => {
    if(confirm("Are you sure you want to remove this quiz?")){
        form.delete(destroy(id).url, {
            preserveScroll: true,
        });
    }
};

const getResults = async (page = 1) => {    
    window.location.href = `?page=${page}`;
}
</script>

<template>
    <Head title="Quiz List" />

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
                <table class="min-w-full shadow rounded-lg">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left border-b">ID</th>
                            <th class="py-2 px-4 text-left border-b">Title</th>
                            <th class="py-2 px-4 text-left border-b">Is work</th>
                            <th class="py-2 px-4 text-left border-b w-72">Image</th>
                            <th class="py-2 px-4 text-left border-b">Time count</th>
                            <th class="py-2 px-4 text-left border-b">Edit/Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="quiz in quizzes.data" :key="quiz.id" >
                            <td class="py-2 px-4 border-b">{{ quiz.id }}</td>
                            <td class="py-2 px-4 border-b">{{ quiz.title }}</td>                        
                            <td class="py-2 px-4 border-b">
                                <OnOffIcon v-if="quiz.is_work !== undefined" :check-value="quiz.is_work" />
                            </td>                        
                            <td class="py-2 px-4 border-b">
                                <img 
                                    v-if="quiz.image" 
                                    class="w-32 h-auto rounded"
                                    :src="'/storage/'+quiz.image" 
                                    srcset=""
                                >
                            </td>
                            <td class="py-2 px-4 border-b">{{ quiz.timer_count }}</td>
                            <td class="py-2 px-4 border-b">                            
                                <Button size="lg" class="mr-2 h-9 w-9">
                                    <Link :href="edit(quiz.id)" >
                                    Edit
                                    </Link>
                                </Button>
                                <Button variant="destructive" size="lg" class="mr-2 h-9 w-9"  @click="deleteQuiz(quiz.id)">
                                    Delete
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>                
                <div class="mt-4">
                    <TailwindPagination :data="quizzes" @pagination-change-page="getResults" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>