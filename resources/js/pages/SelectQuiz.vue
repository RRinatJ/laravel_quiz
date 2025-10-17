<script setup lang="ts">
import { Quiz } from '@/types';
import { Head } from '@inertiajs/vue3';
import PublicAppTemplate from '@/components/PublicAppTemplate.vue';
import { Link } from '@inertiajs/vue3';
import { create } from '@/routes/game';

defineProps<{
    quizzes: Quiz[];
}>();

</script>

<template>
    <Head title="Select a Quiz" />    
    <div>
        <PublicAppTemplate>
            <div class="px-40 flex flex-1 justify-center py-5">
                <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
                    <div class="flex flex-wrap justify-between gap-3 p-4"><p class="tracking-light text-[32px] font-bold leading-tight min-w-72">Select a Quiz</p></div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                        <div v-for="quiz in quizzes" :key="quiz.id" class="flex flex-col gap-3 pb-3">
                            <Link :href="create(quiz.id)">
                                <div
                                    class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-xl category-item-icon"
                                    :style="{ 'background-image': 'url(' + 'storage/'+quiz.image + ')' }"                            
                                >
                                </div>
                                <p class="text-base font-medium leading-normal">{{ quiz.title }}</p>                                
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