<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { type Article } from '@/types';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { index, store, update, slug } from '@/routes/article';
import { ExternalLink } from 'lucide-vue-next';

interface Props {
    article?: object;
    message?: string;
    positions?: string[];
}

const props = defineProps<Props>();
const article = props.article as Article;

const isEditMode = computed(() => !!props.article);
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Article List',
        href: index().url,
    },
];
if (isEditMode.value) {
    breadcrumbs.push(
        {
            title: article.title,
            href: '/article/' + article.id + '/edit',
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

type articleForm = {
    id: number | null;
    title: string;
    content: string;    
    image: string;
    uploaded_image: File | null;
};

const form = useForm<articleForm>({
    id: article?.id || null,
    title: article?.title || "",
    content: article?.content || "",
    image: article?.image || "",
    uploaded_image: null,
});

const submit = () => {    
    if (isEditMode.value) {     
        form.submit(update(article.id), {
            preserveScroll: true,
            preserveState : "errors",
            onSuccess: () => form.reset()
        });
    } else {
        form.submit(store(), {
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

const deleteImage = () => {
    form.image = '';
    form.uploaded_image = null;
    article.image = '';    
}
</script>
    
    <template>
        <Head title="Create Article" />
    
        <AppLayout :breadcrumbs="breadcrumbs">
            <div class="container mx-auto p-4">
                <div class="flex justify-between items-center mb-4">
                    <h1 v-if="isEditMode" class="text-2xl font-bold">Update Article</h1>
                    <h1 v-else class="text-2xl font-bold">Create Article</h1>
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
                        <div class="mb-4">                            
                            <Label for="title">Title</Label>
                            <div class="flex w-full max-w-sm items-center space-x-2">
                                <Input 
                                    id="title" 
                                    class="mt-1 block w-full" 
                                    v-model="form.title" 
                                    required 
                                    autocomplete="title" 
                                    placeholder="Title" 
                                />
                                <a :href="slug(article.slug).url" target="_blank" class="text-sm font-medium leading-normal">
                                    <component :is="ExternalLink" />
                                </a> 
                            </div>                                
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>
                        <div class="mb-4">  
                            <Label for="image">Image</Label>
                            <img 
                                v-if="article && article.image" 
                                class="w-96 mt-1" 
                                :src="'/storage/' + article.image" 
                                srcset=""
                            >
                            <div class="mt-2" v-if="article && article.image"> 
                                <Button variant="destructive" @click="deleteImage">Delete Image</Button>                  
                            </div>
                            <Input 
                                class="mt-1 block" 
                                id="image" 
                                type="file" 
                                @input="setUploadedImage" 
                            />
                            <InputError class="mt-2" :message="form.errors.uploaded_image" />
                        </div>
                        <div class="mb-4">
                            <Label for="content" >Content</Label>                        
                            <Textarea 
                                v-model="form.content" 
                                class="mt-1 block w-full" 
                                required 
                                placeholder="Type your message here." 
                            />
                            <InputError class="mt-2" :message="form.errors.content" />                     
                        </div>                       
                        
                        <Button @click="submit" :disabled="form.processing" size="lg" >
                            {{ submitText }}
                        </Button>                        
                    </div>
                </div>
            </div>        
        </AppLayout>
    </template>