<script setup lang="ts">
import OnOffIcon from '@/components/OnOffIcon.vue';
import Pagination from '@/components/pagination/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit } from '@/routes/question';
import {
    PaginatedResourceResponse,
    Quiz,
    type BreadcrumbItem,
    type Question,
} from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Props {
    questions: PaginatedResourceResponse<Question>;
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
    filter();
});

const filter = () => {
    router.get(
        '/question',
        {
            quiz_id: quiz_id.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Question List',
        href: '/question',
    },
];

const form = useForm({
    id: null,
});

const deleteQuestion = (id: number) => {
    if (confirm('Are you sure you want to remove this question?')) {
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
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Question list</h1>
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
                <div class="flex max-w-7xl items-center py-4">
                    <Select v-model="quiz_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Choose a Quiz" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem :value="null">
                                    Choose a Quiz
                                </SelectItem>
                                <SelectItem
                                    v-for="quiz_option in quizzes"
                                    :value="quiz_option.id"
                                    :key="quiz_option.id"
                                >
                                    {{ quiz_option.title }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <table class="min-w-full rounded-lg shadow">
                    <thead>
                        <tr>
                            <th class="border-b px-4 py-2 text-left">ID</th>
                            <th class="border-b px-4 py-2 text-left">
                                Question
                            </th>
                            <th class="w-32 border-b px-4 py-2 text-left">
                                Image
                            </th>
                            <th class="border-b px-4 py-2 text-left">Audio</th>
                            <th class="border-b px-4 py-2 text-left">
                                Quizzes
                            </th>
                            <th class="border-b px-4 py-2 text-left">
                                Answers
                            </th>
                            <th class="border-b px-4 py-2 text-left">
                                Edit/Delete
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="question in questions.data"
                            :key="question.id"
                        >
                            <td class="border-b px-4 py-2">
                                {{ question.id }}
                            </td>
                            <td class="border-b px-4 py-2">
                                {{ question.question }}
                            </td>
                            <td class="border-b px-4 py-2">
                                <img
                                    v-if="question.image"
                                    class="h-auto w-32 rounded"
                                    :src="question.image"
                                    srcset=""
                                />
                            </td>
                            <td class="border-b px-4 py-2">
                                <OnOffIcon
                                    :check-value="question.audio"
                                    :size="'sm'"
                                />
                            </td>
                            <td class="border-b px-4 py-2">
                                <ul>
                                    <li
                                        v-for="question_quiz in question.quizzes"
                                        :key="question_quiz.id"
                                    >
                                        {{ question_quiz.title }}
                                        <OnOffIcon
                                            v-if="
                                                question_quiz.is_work !==
                                                undefined
                                            "
                                            :check-value="question_quiz.is_work"
                                            :size="'sm'"
                                        />
                                    </li>
                                </ul>
                            </td>
                            <td class="border-b px-4 py-2">
                                <div
                                    class="grid auto-cols-max grid-flow-col gap-4"
                                >
                                    <div
                                        v-for="answer in question.answers"
                                        :key="answer.id"
                                    >
                                        <img
                                            v-if="answer.image"
                                            class="h-auto w-16 rounded"
                                            :src="answer.image"
                                            srcset=""
                                        />
                                        <Badge
                                            v-if="answer.text"
                                            :class="'w-min whitespace-normal'"
                                            :variant="
                                                answer.is_correct
                                                    ? 'destructive'
                                                    : 'default'
                                            "
                                            class="mr-1"
                                        >
                                            {{ answer.text }}
                                        </Badge>
                                    </div>
                                </div>
                            </td>
                            <td class="border-b px-4 py-2">
                                <Button size="lg" class="mr-2 mb-2 h-9 w-9">
                                    <Link :href="edit(question.id)">
                                        Edit
                                    </Link>
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="lg"
                                    class="mr-2 mb-2 h-9 w-9"
                                    @click="deleteQuestion(question.id)"
                                >
                                    Delete
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-4" v-if="questions !== undefined">
                    <Pagination :data="questions" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
