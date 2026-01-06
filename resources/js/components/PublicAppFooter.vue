<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
    <footer class="mt-auto flex justify-center">
        <div class="flex max-w-[960px] flex-1 flex-col">
            <footer
                class="@container flex flex-col gap-6 px-5 py-10 text-center"
            >
                <div
                    class="flex flex-wrap items-center justify-center gap-6 @[480px]:flex-row @[480px]:justify-around"
                ></div>
                <p class="text-base leading-normal font-normal">
                    @2025 {{ page.props.name }}. All rights reserved.
                </p>
            </footer>
        </div>
    </footer>
</template>
