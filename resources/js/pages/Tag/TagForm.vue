<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import ShowMessage from '@/components/ShowMessage.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, index, store, update } from '@/routes/tag';
import { type BreadcrumbItem, type Tag } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    tag?: { data: object };
    message?: string;
}

const props = defineProps<Props>();
const tag = props.tag?.data as Tag;

const isEditMode = computed(() => !!props.tag);
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tag List',
        href: index().url,
    },
];
if (isEditMode.value) {
    breadcrumbs.push({
        title: tag.name,
        href: '/tag/' + tag.id + '/edit',
    });
} else {
    breadcrumbs.push({
        title: 'Create',
        href: create().url,
    });
}

const submitText = computed(() => {
    if (isEditMode.value) {
        return form.processing ? 'Updating...' : 'Update';
    } else {
        return form.processing ? 'Creating...' : 'Create';
    }
});

type TagForm = {
    id: number | null;
    name: string;
};

const form = useForm<TagForm>({
    id: tag?.id || null,
    name: tag?.name || '',
});

const submit = () => {
    if (isEditMode.value) {
        form.submit(update(tag.id), {
            preserveScroll: true,
            preserveState: 'errors',
            onSuccess: () => form.reset(),
        });
    } else {
        form.submit(store(), {
            preserveScroll: true,
            preserveState: 'errors',
            onSuccess: () => form.reset(),
        });
    }
};
</script>

<template>
    <Head :title="isEditMode ? 'Update Tag' : 'Create Tag'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 v-if="isEditMode" class="text-2xl font-bold">Update Tag</h1>
                <h1 v-else class="text-2xl font-bold">Create Tag</h1>
            </div>
            <div class="mb-6 rounded-lg p-4 shadow">
                <div>
                    <ShowMessage class="mb-6" :message="props.message" />
                    <div class="mb-4">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            class="mt-1 block w-full max-w-sm"
                            required
                            autocomplete="name"
                            placeholder="Tag name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <Button
                        @click="submit"
                        :disabled="form.processing"
                        size="lg"
                    >
                        {{ submitText }}
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
