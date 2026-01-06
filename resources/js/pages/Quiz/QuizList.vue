<script setup lang="ts">
import OnOffIcon from '@/components/OnOffIcon.vue';
import Pagination from '@/components/pagination/Pagination.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit } from '@/routes/quiz';
import { type BreadcrumbItem, type Quiz, PaginatedResponse } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

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

const deleteQuiz = (id: number) => {
    if (confirm('Are you sure you want to remove this quiz?')) {
        form.delete(destroy(id).url, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Quiz List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Quiz list</h1>
            </div>
            <div v-if="error" class="mb-6">
                <div
                    class="rounded-lg bg-red-100 p-4 text-sm text-red-700 dark:bg-red-200 dark:text-red-800"
                    role="alert"
                >
                    <span class="font-medium">
                        {{ error }}
                    </span>
                </div>
            </div>
            <div v-if="message" class="mb-6">
                <div
                    class="rounded-lg bg-green-100 p-4 text-sm text-green-700 dark:bg-green-200 dark:text-green-800"
                    role="alert"
                >
                    <span class="font-medium">
                        {{ message }}
                    </span>
                </div>
            </div>
            <div class="mb-6 rounded-lg p-4 shadow">
                <Button size="lg" class="mr-2 h-9 w-9">
                    <Link :href="create()"> Create </Link>
                </Button>
                <br /><br />
                <table class="min-w-full rounded-lg shadow">
                    <thead>
                        <tr>
                            <th class="border-b px-4 py-2 text-left">ID</th>
                            <th class="border-b px-4 py-2 text-left">Title</th>
                            <th class="border-b px-4 py-2 text-left">
                                Is work
                            </th>
                            <th class="w-32 border-b px-4 py-2 text-left">
                                Image
                            </th>
                            <th class="border-b px-4 py-2 text-left">
                                Time count
                            </th>
                            <th class="border-b px-4 py-2 text-left">Hints</th>
                            <th class="border-b px-4 py-2 text-left">
                                Edit/Delete
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="quiz in quizzes.data" :key="quiz.id">
                            <td class="border-b px-4 py-2">{{ quiz.id }}</td>
                            <td class="border-b px-4 py-2">{{ quiz.title }}</td>
                            <td class="border-b px-4 py-2">
                                <OnOffIcon
                                    v-if="quiz.is_work !== undefined"
                                    :check-value="quiz.is_work"
                                />
                            </td>
                            <td class="border-b px-4 py-2">
                                <img
                                    v-if="quiz.image"
                                    class="h-auto w-32 rounded"
                                    :src="'/storage/' + quiz.image"
                                    srcset=""
                                />
                            </td>
                            <td class="border-b px-4 py-2">
                                {{ quiz.timer_count }}
                            </td>
                            <td class="border-b px-4 py-2">
                                Fifty-fity hint
                                <OnOffIcon
                                    v-if="quiz.fifty_fifty_hint !== undefined"
                                    :check-value="quiz.fifty_fifty_hint"
                                /><br />
                                Can skip hint
                                <OnOffIcon
                                    v-if="quiz.can_skip !== undefined"
                                    :check-value="quiz.can_skip"
                                />
                            </td>
                            <td class="border-b px-4 py-2">
                                <Button size="lg" class="mr-2 h-9 w-9">
                                    <Link :href="edit(quiz.id)"> Edit </Link>
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="lg"
                                    class="mr-2 h-9 w-9"
                                    @click="deleteQuiz(quiz.id)"
                                >
                                    Delete
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-4" v-if="quizzes !== undefined">
                    <Pagination :data="quizzes" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
