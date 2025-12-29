<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { type Article, type PaginatedResponse } from '@/types';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import Pagination from '@/components/pagination/Pagination.vue';
import { ExternalLink } from 'lucide-vue-next';
import { create, edit, destroy, slug } from '@/routes/article';

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

const deleteArticle = (id:number) => {
    if(confirm("Are you sure you want to remove this article?")){
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
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Article list</h1>
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
                            <th class="py-2 px-4 text-left border-b w-32">Image</th>
                            <th class="py-2 px-4 text-left border-b">Created at</th>
                            <th class="py-2 px-4 text-left border-b">Link</th>
                            <th class="py-2 px-4 text-left border-b">Edit/Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="article in articles.data" :key="article.id" >
                        <td class="py-2 px-4 border-b">{{ article.id }}</td>
                        <td class="py-2 px-4 border-b">{{ article.title }}</td>                                                
                        <td class="py-2 px-4 border-b">
                            <img 
                                v-if="article.image" 
                                class="w-32 h-auto rounded"
                                :src="'/storage/'+article.image" 
                                srcset=""
                            >
                        </td>
                        <td class="py-2 px-4 border-b">{{ article.created_at }}</td>
                        <td class="py-2 px-4 border-b">
                            <a :href="slug(article.slug).url" target="_blank" class="text-sm font-medium leading-normal">
                                <component :is="ExternalLink" />
                            </a>
                        </td>
                        <td class="py-2 px-4 border-b">                            
                            <Button size="lg" class="mr-2 h-9 w-9">
                                <Link :href="edit(article.id)" >
                                Edit
                                </Link>
                            </Button>
                            <Button variant="destructive" size="lg" class="mr-2 h-9 w-9"  @click="deleteArticle(article.id)">
                                Delete
                            </Button>
                        </td>
                        </tr>
                    </tbody>
                </table>                
                <div class="mt-4">
                    <Pagination :data="articles"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>