<script setup lang="ts">
import { Quiz } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import PublicAppTemplate from '@/components/PublicAppTemplate.vue';
import { Link } from '@inertiajs/vue3';
import { create } from '@/routes/game';
import { ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';

interface Props {
    quizzes: Quiz[];
    filters?: {
        quiz_title: string;
    };
};

const props = defineProps<Props>();

const quiz_title = ref(props.filters?.quiz_title || '');

const filter = useDebounceFn(() => {
    router.get('/', {
        quiz_title: quiz_title.value,
    }, {
        preserveState: true,
        replace: true
    })
}, 500);

watch(quiz_title, () => {
    filter()
});

</script>

<template>
    <Head title="Select a Quiz" />    
    <div>
        <PublicAppTemplate>
            <div class="px-40 flex flex-1 justify-center py-5">
                <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
                    <div class="py-3">
                        <label class="flex flex-col min-w-40 h-12 w-full">
                            <div class="flex w-full flex-1 items-stretch rounded-xl h-full">
                            <div
                                class="text-[#49739c] flex border-none bg-[#e7edf4] items-center justify-center pl-4 rounded-l-xl border-r-0"
                                data-icon="MagnifyingGlass"
                                data-size="24px"
                                data-weight="regular"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                <path
                                    d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"
                                ></path>
                                </svg>
                            </div>
                            <input
                                v-model="quiz_title"
                                type="text"
                                placeholder="Search quiz categories"
                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0d141c] focus:outline-0 focus:ring-0 border-none bg-[#e7edf4] focus:border-none h-full placeholder:text-[#49739c] px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
                            />
                            </div>                            
                        </label>
                    </div>
                    <div class="flex flex-wrap justify-between gap-3 py-4"><p class="tracking-light text-[32px] font-bold leading-tight min-w-72">Select a Quiz</p></div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                        <div v-for="quiz in quizzes" :key="quiz.id" class="flex flex-col gap-3 pb-3">
                            <Link :href="create(quiz.id)">
                                <div
                                    class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-xl category-item-icon"
                                    :style="{ 'background-image': 'url(' + 'storage/'+quiz.image + ')' }"                            
                                >
                                </div>
                                <div>
                                    <p class="text-base font-medium leading-normal">{{ quiz.title }}</p>   
                                    <p class="text-[#49739c] text-sm font-normal leading-normal">{{ quiz.description }}</p>   
                                </div>                          
                            </Link>
                        </div>                    
                    </div>
                </div>
            </div>
        </PublicAppTemplate>
    </div>
</template>

<style scoped>    
    .category-item-icon:hover {
        transform: scale(1.02);
    }    
</style>