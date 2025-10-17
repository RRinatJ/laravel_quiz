<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
    <footer class="flex justify-center mt-auto">
        <div class="flex max-w-[960px] flex-1 flex-col">
            <footer class="flex flex-col gap-6 px-5 py-10 text-center @container">
                <div class="flex flex-wrap items-center justify-center gap-6 @[480px]:flex-row @[480px]:justify-around">                    
                </div>
                <p class="text-base font-normal leading-normal">@2025 {{ page.props.name }}. All rights reserved.</p>
            </footer>
        </div>
    </footer>
</template>