<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { type Quiz, type Question } from '@/types';
import { Head } from '@inertiajs/vue3';
import OnOffIcon from '@/components/OnOffIcon.vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

interface Props {
    latestQuestions: Question[];
    latestQuizzes: Quiz[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-2">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <h2 class="text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Latest Questions</h2>    
                    <table class="min-w-full shadow rounded-lg">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 text-left border-b">Question</th>
                                <th class="py-2 px-4 text-left border-b w-16">Image</th>
                                <th class="py-2 px-4 text-left border-b">Quiz</th>
                                <th class="py-2 px-4 text-left border-b">Added</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="question in latestQuestions" :key="question.id" >                                
                                <td class="py-2 px-4 border-b">{{ question.question }}</td>
                                <td class="py-2 px-4 border-b">
                                    <img 
                                        v-if="question.image" 
                                        class="w-16 h-auto rounded"
                                        :src="'/storage/'+question.image" 
                                        srcset=""
                                    >
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <ul>
                                        <li v-for="question_quiz in question.quizzes" :key="question_quiz.id">
                                            {{ question_quiz.title }} 
                                        </li>
                                    </ul>
                                </td>
                                <td class="py-2 px-4 border-b">
                                    {{ question.created_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <h2 class="text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Latest Quizzes</h2>
                    <table class="min-w-full shadow rounded-lg">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 text-left border-b">Title</th>
                                <th class="py-2 px-4 text-left border-b w-16">Image</th>
                                <th class="py-2 px-4 text-left border-b">Is work</th>
                                <th class="py-2 px-4 text-left border-b">Added</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="quiz in latestQuizzes" :key="quiz.id" >                                
                                <td class="py-2 px-4 border-b">{{ quiz.title }}</td>
                                <td class="py-2 px-4 border-b">
                                    <img 
                                        v-if="quiz.image" 
                                        class="w-16 h-auto rounded"
                                        :src="'/storage/'+quiz.image" 
                                        srcset=""
                                    >
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <OnOffIcon v-if="quiz.is_work !== undefined" :check-value="quiz.is_work" />
                                </td>
                                <td class="py-2 px-4 border-b">
                                    {{ quiz.created_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
