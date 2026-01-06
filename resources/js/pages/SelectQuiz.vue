<script setup lang="ts">
import Pagination from '@/components/pagination/Pagination.vue';
import PublicAppTemplate from '@/components/PublicAppTemplate.vue';
import { create } from '@/routes/game';
import { PaginatedResponse, Quiz } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { ref, watch } from 'vue';

interface Props {
    quizzes: PaginatedResponse<Quiz>;
    filters?: {
        quiz_title: string;
    };
}

const props = defineProps<Props>();

const quiz_title = ref(props.filters?.quiz_title || '');

const filter = useDebounceFn(() => {
    router.get(
        '/',
        {
            quiz_title: quiz_title.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}, 500);

watch(quiz_title, () => {
    filter();
});
</script>

<template>
    <Head title="Select a Quiz" />
    <div>
        <PublicAppTemplate>
            <div class="flex flex-1 justify-center px-40 py-5">
                <div
                    class="layout-content-container flex max-w-[960px] flex-1 flex-col"
                >
                    <div class="mb-2 flex-grow">
                        <div class="py-3">
                            <label class="flex h-12 w-full min-w-40 flex-col">
                                <div
                                    class="flex h-full w-full flex-1 items-stretch rounded-xl"
                                >
                                    <div
                                        class="flex items-center justify-center rounded-l-xl border-r-0 border-none bg-[#e7edf4] pl-4 text-[#49739c]"
                                        data-icon="MagnifyingGlass"
                                        data-size="24px"
                                        data-weight="regular"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24px"
                                            height="24px"
                                            fill="currentColor"
                                            viewBox="0 0 256 256"
                                        >
                                            <path
                                                d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <input
                                        v-model="quiz_title"
                                        type="text"
                                        placeholder="Search quiz categories"
                                        class="form-input flex h-full w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl rounded-l-none border-l-0 border-none bg-[#e7edf4] px-4 pl-2 text-base leading-normal font-normal text-[#0d141c] placeholder:text-[#49739c] focus:border-none focus:ring-0 focus:outline-0"
                                    />
                                </div>
                            </label>
                        </div>
                        <div class="flex flex-wrap justify-between gap-3 py-4">
                            <p
                                class="tracking-light min-w-72 text-[32px] leading-tight font-bold"
                            >
                                Select a Quiz
                            </p>
                        </div>
                        <div
                            class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5"
                        >
                            <div
                                v-for="quiz in quizzes.data"
                                :key="quiz.id"
                                class="flex flex-col gap-3 pb-3"
                            >
                                <Link :href="create(quiz.id)">
                                    <div
                                        class="category-item-icon aspect-square w-full rounded-xl bg-cover bg-center bg-no-repeat"
                                        :style="{
                                            'background-image':
                                                'url(' +
                                                'storage/' +
                                                quiz.image +
                                                ')',
                                        }"
                                    ></div>
                                    <div>
                                        <p
                                            class="text-base leading-normal font-medium"
                                        >
                                            {{ quiz.title }}
                                        </p>
                                        <p
                                            class="text-sm leading-normal font-normal text-[#49739c]"
                                        >
                                            {{ quiz.description }}
                                        </p>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div
                        class="mt-4"
                        v-if="
                            quizzes !== undefined &&
                            quizzes.total > quizzes.per_page
                        "
                    >
                        <Pagination :data="quizzes" />
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
