<script setup lang="ts">
import PublicAppTemplate from '@/components/PublicAppTemplate.vue';
import { Article } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    article: Article;
}>();

console.log(props.article);
</script>

<template>
    <Head :title="article.title" />
    <div>
        <PublicAppTemplate>
            <article
                class="format format-sm sm:format-base lg:format-lg format-blue dark:format-invert mx-auto mt-4 w-full max-w-2xl"
            >
                <header class="not-format mb-4 lg:mb-6">
                    <h1
                        class="mb-4 text-3xl leading-tight font-extrabold text-gray-900 lg:mb-6 lg:text-4xl dark:text-white"
                    >
                        {{ article.title }}
                    </h1>
                    <p
                        class="text-xs font-light text-gray-500 dark:text-gray-400"
                    >
                        {{ article.created_at }}
                    </p>
                </header>
                <figure v-if="article.image" class="mb-4 lg:mb-6">
                    <img :src="'/storage/' + article.image" alt="" />
                </figure>
                <div class="article-content">{{ article.content }}</div>
            </article>
        </PublicAppTemplate>
    </div>
</template>

<style scoped>
.article-content {
    white-space: pre-wrap;
}
</style>
