<script setup lang="ts">
import { home } from '@/routes';
import { create } from '@/routes/game';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, RotateCcw } from 'lucide-vue-next';

interface Props {
    quiz_id: number;
}
defineProps<Props>();
</script>

<template>
    <div class="mt-10 flex w-full max-w-[800px] flex-col gap-4 sm:flex-row">
        <Link
            :href="create(quiz_id)"
            class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#137fec] py-4 text-lg font-bold text-white shadow-lg shadow-[#137fec]/20 transition-all hover:bg-[#137fec]/90 active:translate-y-0.5"
        >
            <span class="material-symbols-outlined"><RotateCcw /></span>
            Play Again
        </Link>
        <Link
            :href="home()"
            class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-[#dbe0e6] bg-white py-4 text-lg font-bold text-[#111418] transition-all hover:border-[#137fec] hover:text-[#137fec]"
        >
            <span class="material-symbols-outlined"><LayoutGrid /></span>
            Back to Quizzes
        </Link>
    </div>
</template>
