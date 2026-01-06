<script setup lang="ts">
import PublicAppTemplate from '@/components/PublicAppTemplate.vue';
import { home } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    status: { type: Number, required: true },
});

const title = computed(() => {
    return {
        503: '503: Service Unavailable',
        500: '500: Server Error',
        404: '404: Page Not Found',
        403: '403: Forbidden',
    }[props.status];
});

const description = computed(() => {
    return {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
    }[props.status];
});
</script>

<template>
    <Head :title="title" />
    <PublicAppTemplate>
        <div class="layout-container flex h-full grow flex-col">
            <div class="flex flex-1 justify-center px-40 py-5">
                <div
                    class="layout-content-container flex w-[512px] max-w-[512px] max-w-[960px] flex-1 flex-col py-5"
                >
                    <h2
                        class="tracking-light px-4 pt-5 pb-3 text-center text-[28px] leading-tight font-bold"
                    >
                        {{ title }}
                    </h2>
                    <p
                        class="px-4 pt-1 pb-3 text-center text-base leading-normal font-normal"
                    >
                        {{ description }}
                    </p>
                    <div class="flex justify-center px-4 py-3">
                        <Link
                            :href="home()"
                            class="flex h-10 max-w-[480px] min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg px-4 text-sm leading-normal font-bold tracking-[0.015em]"
                        >
                            <span class="truncate">Go to Home</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicAppTemplate>
</template>
