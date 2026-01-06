<script setup lang="ts">
import ThemeToggle from '@/components/ThemeToggle.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { getInitials } from '@/composables/useInitials';
import { dashboard, home, login } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const currentPath = window.location.pathname;
const page = usePage();
const auth = computed(() => page.props.auth);
</script>

<template>
    <header
        class="flex items-center justify-between border-b border-solid px-10 py-3 whitespace-nowrap"
    >
        <Link :href="home()" class="text-sm leading-normal font-medium">
            <div class="flex items-center gap-4">
                <div class="size-4">
                    <svg
                        viewBox="0 0 48 48"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M36.7273 44C33.9891 44 31.6043 39.8386 30.3636 33.69C29.123 39.8386 26.7382 44 24 44C21.2618 44 18.877 39.8386 17.6364 33.69C16.3957 39.8386 14.0109 44 11.2727 44C7.25611 44 4 35.0457 4 24C4 12.9543 7.25611 4 11.2727 4C14.0109 4 16.3957 8.16144 17.6364 14.31C18.877 8.16144 21.2618 4 24 4C26.7382 4 29.123 8.16144 30.3636 14.31C31.6043 8.16144 33.9891 4 36.7273 4C40.7439 4 44 12.9543 44 24C44 35.0457 40.7439 44 36.7273 44Z"
                            fill="currentColor"
                        ></path>
                    </svg>
                </div>
                <h2 class="text-lg leading-tight font-bold tracking-[-0.015em]">
                    {{ page.props.name }}
                </h2>
            </div>
        </Link>
        <div class="flex flex-1 justify-end gap-8">
            <div class="flex items-center gap-9">
                <ThemeToggle />
                <Link
                    v-if="currentPath !== home().url"
                    :href="home()"
                    class="text-sm leading-normal font-medium"
                    >Home</Link
                >
                <Link
                    v-if="!auth.user"
                    :href="login()"
                    class="text-sm leading-normal font-medium"
                    >Login</Link
                >
                <Link
                    v-if="auth.user"
                    :href="dashboard()"
                    class="text-sm leading-normal font-medium"
                    >Dashboard</Link
                >
            </div>
            <Avatar
                v-if="auth.user"
                class="aspect-square size-10 overflow-hidden rounded-full bg-cover bg-center bg-no-repeat"
            >
                <AvatarImage
                    v-if="auth.user.avatar"
                    :src="auth.user.avatar"
                    :alt="auth.user.name"
                />
                <AvatarFallback
                    class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
                >
                    {{ getInitials(auth.user?.name) }}
                </AvatarFallback>
            </Avatar>
        </div>
    </header>
</template>
