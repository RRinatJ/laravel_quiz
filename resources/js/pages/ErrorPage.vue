<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicAppTemplate from '@/components/PublicAppTemplate.vue';
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

const props = defineProps({ 
    status: { type: Number, required: true } 
})

const title = computed(() => {
  return {
    503: '503: Service Unavailable',
    500: '500: Server Error',
    404: '404: Page Not Found',
    403: '403: Forbidden',
  }[props.status]
})

const description = computed(() => {
  return {
    503: 'Sorry, we are doing some maintenance. Please check back soon.',
    500: 'Whoops, something went wrong on our servers.',
    404: 'Sorry, the page you are looking for could not be found.',
    403: 'Sorry, you are forbidden from accessing this page.',
  }[props.status]
})

</script>

<template>
    <Head :title="title" />    
    <PublicAppTemplate>
        <div class="layout-container flex h-full grow flex-col">            
            <div class="px-40 flex flex-1 justify-center py-5">
                <div class="layout-content-container flex flex-col w-[512px] max-w-[512px] py-5 max-w-[960px] flex-1">
                    <h2 class="tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">{{ title }}</h2>
                    <p class="text-base font-normal leading-normal pb-3 pt-1 px-4 text-center">
                        {{ description }}
                    </p>
                    <div class="flex px-4 py-3 justify-center">
                        <Link
                            :href="home()"
                            class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 text-sm font-bold leading-normal tracking-[0.015em]"
                        >
                            <span class="truncate">Go to Home</span>
                        </Link>
                    </div>                    
                </div>
            </div>
        </div>
    </PublicAppTemplate>
</template>