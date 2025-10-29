<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { type Quiz } from '@/types';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { store, update } from '@/routes/quiz';
import { Switch } from '@/components/ui/switch';

interface Props {
    quiz?: object;
    message?: string;
}

const props = defineProps<Props>();
const quiz = props.quiz as Quiz;

const form = useForm({
    id: quiz?.id || null,
    title: quiz?.title || "",
    is_work: quiz?.is_work || false,
    timer_count: quiz?.timer_count || 1,
    fifty_fifty_hint: quiz?.fifty_fifty_hint || false,
    can_skip: quiz?.can_skip || false,
    uploaded_image: null as File | null,
});

const isEditMode = computed(() => !!props.quiz);
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Quiz List',
        href: '/quiz',
    },
];
if (isEditMode.value) {
    breadcrumbs.push(
        {
            title: quiz.title,
            href: '/quiz/' + quiz.id + '/edit',
        }
    )
} else {
    breadcrumbs.push(
        {
            title: 'Create',
            href: '/create',
        }   
    )
}

const submitText = computed(() => {
    if (isEditMode.value) {
        return form.processing ? 'Updating...' : 'Update';
    }else{
        return form.processing ? 'Creating...' : 'Create';
    }

});

const submit = () => {    
    if (isEditMode.value) {        
        form.post(update(quiz.id).url, {
            preserveScroll: true,
            preserveState : "errors",
            onSuccess: () => form.reset()
        });
    } else {
        form.post(store().url, {
            preserveScroll: true,
            preserveState : "errors",
            onSuccess: () => form.reset()
        });    
    }
};

const setUploadedImage = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (!files || !files[0]){
        return false;
    }     
    form.uploaded_image = files[0];    
}
</script>

<template>
    <Head :title="isEditMode ? 'Edit Quiz' : 'Create Quiz'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 v-if="isEditMode" class="text-2xl font-bold">Update Quiz</h1>
                <h1 v-else class="text-2xl font-bold">Create Quiz</h1>
            </div>
            <div class="shadow rounded-lg p-4 mb-6">                
                <div>
                    <div v-if="props.message" class="mb-4">
                        <div
                            class="p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800"
                            role="alert"
                        >
                            <span class="font-medium">
                                {{props.message}}
                            </span>
                        </div>
                    </div>
                    <div class="mb-4 w-sm">
                        <Label for="title">Title</Label>
                        <Input 
                            id="title" 
                            class="mt-1 block w-full" 
                            v-model="form.title" 
                            required 
                            autocomplete="title" 
                            placeholder="Title" 
                        />
                        <InputError class="mt-2" :message="form.errors.title" />                        
                    </div>
                    <div class="mb-4 w-sm">  
                        <Label for="image">Image</Label>
                        <img 
                            v-if="quiz && quiz.image" 
                            class="w-96 mt-1" 
                            :src="'/storage/'+quiz.image" 
                            srcset=""
                        >
                        <Input  
                            class="mt-1 block" 
                            id="image" 
                            type="file" 
                            @input="setUploadedImage" 
                        />
                        <InputError class="mt-2" :message="form.errors.uploaded_image" />
                    </div>
                    <div class="mb-4 w-sm">
                        <Label for="timer_count" >Timer Count</Label>
                        <Input 
                            id="timer_count" 
                            class="mt-1 block w-full" 
                            v-model="form.timer_count" 
                            required 
                            autocomplete="timer_count" 
                            placeholder="Timer Count" 
                        />
                        <InputError class="mt-2" :message="form.errors.timer_count" />                     
                    </div>
                    <div class="mb-4">
                        <Label for="is_work">Is Work</Label><br>
                        <Switch id="is_work" v-model="form.is_work" />
                        <InputError class="mt-2" :message="form.errors.is_work" />
                    </div>
                    <div class="mb-4">
                        <Label for="fifty_fifty_hint">Fifty-Fifty Hint</Label><br>
                        <Switch id="fifty_fifty_hint" v-model="form.fifty_fifty_hint" />
                        <InputError class="mt-2" :message="form.errors.fifty_fifty_hint" />
                    </div>
                    <div class="mb-4">
                        <Label for="can_skip">Can Skip Hint</Label><br>
                        <Switch id="can_skip" v-model="form.can_skip" />
                        <InputError class="mt-2" :message="form.errors.can_skip" />
                    </div>
                    
                    <Button @click="submit" :disabled="form.processing" size="lg" >
                        {{ submitText }}
                    </Button>
                </div>
            </div>
        </div>        
    </AppLayout>
</template>