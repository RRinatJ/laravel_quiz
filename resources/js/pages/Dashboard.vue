<script setup lang="ts">
import OnOffIcon from '@/components/OnOffIcon.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import {
    type BreadcrumbItem,
    type Game,
    type Question,
    type Quiz,
} from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

interface Props {
    latestQuestions: Question[];
    latestQuizzes: Quiz[];
    latestGames: Game[];
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
                    v-if="latestGames.length > 0"
                    class="relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <h2
                        class="px-4 pt-5 pb-3 text-[22px] leading-tight font-bold tracking-[-0.015em]"
                    >
                        Latest Games
                    </h2>
                    <table class="min-w-full rounded-lg shadow">
                        <thead>
                            <tr>
                                <th class="border-b px-4 py-2 text-left">
                                    Quiz
                                </th>
                                <th class="border-b px-4 py-2 text-left">
                                    Answered / Total
                                </th>
                                <th class="border-b px-4 py-2 text-left">
                                    Played
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="game in latestGames" :key="game.id">
                                <td class="border-b px-4 py-2">
                                    {{ game.quiz.title }}
                                </td>
                                <td class="border-b px-4 py-2">
                                    {{ game.correct_count }} /
                                    {{ game.question_row.length }}
                                </td>
                                <td class="border-b px-4 py-2">
                                    {{ game.created_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div
                    v-if="latestQuestions.length > 0"
                    class="relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <h2
                        class="px-4 pt-5 pb-3 text-[22px] leading-tight font-bold tracking-[-0.015em]"
                    >
                        Latest Questions
                    </h2>
                    <table class="min-w-full rounded-lg shadow">
                        <thead>
                            <tr>
                                <th class="border-b px-4 py-2 text-left">
                                    Question
                                </th>
                                <th class="w-16 border-b px-4 py-2 text-left">
                                    Image
                                </th>
                                <th class="border-b px-4 py-2 text-left">
                                    Quiz
                                </th>
                                <th class="border-b px-4 py-2 text-left">
                                    Added
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="question in latestQuestions"
                                :key="question.id"
                            >
                                <td class="border-b px-4 py-2">
                                    {{ question.question }}
                                </td>
                                <td class="border-b px-4 py-2">
                                    <img
                                        v-if="question.image"
                                        class="h-auto w-16 rounded"
                                        :src="question.image"
                                        srcset=""
                                    />
                                </td>
                                <td class="border-b px-4 py-2">
                                    <ul>
                                        <li
                                            v-for="question_quiz in question.quizzes"
                                            :key="question_quiz.id"
                                        >
                                            {{ question_quiz.title }}
                                        </li>
                                    </ul>
                                </td>
                                <td class="border-b px-4 py-2">
                                    {{ question.created_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div
                    v-if="latestQuizzes.length > 0"
                    class="relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <h2
                        class="px-4 pt-5 pb-3 text-[22px] leading-tight font-bold tracking-[-0.015em]"
                    >
                        Latest Quizzes
                    </h2>
                    <table class="min-w-full rounded-lg shadow">
                        <thead>
                            <tr>
                                <th class="border-b px-4 py-2 text-left">
                                    Title
                                </th>
                                <th class="w-16 border-b px-4 py-2 text-left">
                                    Image
                                </th>
                                <th class="border-b px-4 py-2 text-left">
                                    Is work
                                </th>
                                <th class="border-b px-4 py-2 text-left">
                                    Added
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="quiz in latestQuizzes" :key="quiz.id">
                                <td class="border-b px-4 py-2">
                                    {{ quiz.title }}
                                </td>
                                <td class="border-b px-4 py-2">
                                    <img
                                        v-if="quiz.image"
                                        class="h-auto w-16 rounded"
                                        :src="quiz.image"
                                        srcset=""
                                    />
                                </td>
                                <td class="border-b px-4 py-2">
                                    <OnOffIcon
                                        v-if="quiz.is_work !== undefined"
                                        :check-value="quiz.is_work"
                                    />
                                </td>
                                <td class="border-b px-4 py-2">
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
