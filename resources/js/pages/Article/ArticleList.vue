<script setup lang="ts">
import Pagination from '@/components/pagination/Pagination.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, slug } from '@/routes/article';
import {
    type Article,
    type BreadcrumbItem,
    type PaginatedResponse,
} from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ExternalLink } from 'lucide-vue-next';

interface Props {
    articles: PaginatedResponse<Article>;
    message?: string;
    error?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Article List',
        href: '/article',
    },
];

const form = useForm({
    id: null,
});

const deleteArticle = (id: number) => {
    if (confirm('Are you sure you want to remove this article?')) {
        form.submit(destroy(id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Article List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Article list</h1>
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
                            <th class="w-32 border-b px-4 py-2 text-left">
                                Image
                            </th>
                            <th class="border-b px-4 py-2 text-left">
                                Created at
                            </th>
                            <th class="border-b px-4 py-2 text-left">Link</th>
                            <th class="border-b px-4 py-2 text-left">
                                Edit/Delete
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="article in articles.data" :key="article.id">
                            <td class="border-b px-4 py-2">{{ article.id }}</td>
                            <td class="border-b px-4 py-2">
                                {{ article.title }}
                            </td>
                            <td class="border-b px-4 py-2">
                                <img
                                    v-if="article.image"
                                    class="h-auto w-32 rounded"
                                    :src="'/storage/' + article.image"
                                    srcset=""
                                />
                            </td>
                            <td class="border-b px-4 py-2">
                                {{ article.created_at }}
                            </td>
                            <td class="border-b px-4 py-2">
                                <a
                                    :href="slug(article.slug).url"
                                    target="_blank"
                                    class="text-sm leading-normal font-medium"
                                >
                                    <component :is="ExternalLink" />
                                </a>
                            </td>
                            <td class="border-b px-4 py-2">
                                <Button size="lg" class="mr-2 h-9 w-9">
                                    <Link :href="edit(article.id)"> Edit </Link>
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="lg"
                                    class="mr-2 h-9 w-9"
                                    @click="deleteArticle(article.id)"
                                >
                                    Delete
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-4">
                    <Pagination :data="articles" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
