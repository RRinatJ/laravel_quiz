<script setup lang="ts">
import Pagination from '@/components/pagination/Pagination.vue';
import ShowError from '@/components/ShowError.vue';
import ShowMessage from '@/components/ShowMessage.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, index } from '@/routes/tag';
import {
    type BreadcrumbItem,
    type PaginatedResourceResponse,
    type Tag,
} from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    tags: PaginatedResourceResponse<Tag>;
    message?: string;
    error?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tag List',
        href: index().url,
    },
];

const form = useForm({
    id: null,
});

const deleteTag = (id: number) => {
    if (confirm('Are you sure you want to remove this tag?')) {
        form.submit(destroy(id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Tag List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Tag list</h1>
            </div>
            <ShowError class="mb-6" :error="error" />
            <ShowMessage class="mb-6" :message="message" />
            <div class="mb-6 rounded-lg p-4 shadow">
                <Button size="lg" class="mb-4">
                    <Link :href="create()"> Create </Link>
                </Button>
                <table class="min-w-full rounded-lg shadow">
                    <thead>
                        <tr>
                            <th class="border-b px-4 py-2 text-left">ID</th>
                            <th class="border-b px-4 py-2 text-left">Name</th>
                            <th class="border-b px-4 py-2 text-left">
                                Created at
                            </th>
                            <th class="border-b px-4 py-2 text-left">
                                Edit/Delete
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="tag in tags.data" :key="tag.id">
                            <td class="border-b px-4 py-2">{{ tag.id }}</td>
                            <td class="border-b px-4 py-2">
                                {{ tag.name }}
                            </td>
                            <td class="border-b px-4 py-2">
                                {{ tag.created_at }}
                            </td>
                            <td class="border-b px-4 py-2">
                                <Button size="lg" class="mr-2 h-9 w-9">
                                    <Link :href="edit(tag.id)"> Edit </Link>
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="lg"
                                    class="mr-2 h-9 w-9"
                                    @click="deleteTag(tag.id)"
                                >
                                    Delete
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-4">
                    <Pagination :data="tags" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
