<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { getInitials } from '@/composables/useInitials';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { home, dashboard, login } from '@/routes';

const currentPath = window.location.pathname;
const page = usePage();
const auth = computed(() => page.props.auth);
</script>

<template>
    <header class="flex items-center justify-between whitespace-nowrap border-b border-solid px-10 py-3">
        <Link :href="home()" class="text-sm font-medium leading-normal">
            <div class="flex items-center gap-4">
                <div class="size-4">                            
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                            d="M36.7273 44C33.9891 44 31.6043 39.8386 30.3636 33.69C29.123 39.8386 26.7382 44 24 44C21.2618 44 18.877 39.8386 17.6364 33.69C16.3957 39.8386 14.0109 44 11.2727 44C7.25611 44 4 35.0457 4 24C4 12.9543 7.25611 4 11.2727 4C14.0109 4 16.3957 8.16144 17.6364 14.31C18.877 8.16144 21.2618 4 24 4C26.7382 4 29.123 8.16144 30.3636 14.31C31.6043 8.16144 33.9891 4 36.7273 4C40.7439 4 44 12.9543 44 24C44 35.0457 40.7439 44 36.7273 44Z"
                            fill="currentColor"
                            ></path>
                        </svg>                            
                </div>
                <h2 class="text-lg font-bold leading-tight tracking-[-0.015em]">{{ page.props.name }}</h2>
            </div>
        </Link>
        <div class="flex flex-1 justify-end gap-8">
            <div class="flex items-center gap-9">  
                <ThemeToggle />              
                <Link v-if="currentPath !== home().url" :href="home()" class="text-sm font-medium leading-normal">Home</Link>
                <Link v-if="!auth.user" :href="login()" class="text-sm font-medium leading-normal">Login</Link>       
                <Link v-if="auth.user" :href="dashboard()" class="text-sm font-medium leading-normal">Dashboard</Link>                
            </div>            
            <Avatar v-if="auth.user" class="overflow-hidden rounded-full bg-center bg-no-repeat aspect-square bg-cover size-10">
                <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                <AvatarFallback class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                    {{ getInitials(auth.user?.name) }}
                </AvatarFallback>
            </Avatar>            
        </div>
    </header>
</template>