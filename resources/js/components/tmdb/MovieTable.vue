<script setup lang="ts">
import { Button } from '@/components/ui/button';
const emit = defineEmits(['searchImages']);

interface Props {
    results: Array<{
        id: number;
        original_title: string;
        title: string;
        overview: string;
        poster_path: string | null;
        release_date: string;
    }>;
    tmdbImageBaseUrl?: string;
}
defineProps<Props>();
const emitEvent = (movie_id: number) => {
    emit('searchImages', movie_id);
};
</script>

<template>
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <div class="rounded-lg border border-gray-300">
                <table class="min-w-full table-auto divide-y divide-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th
                                scope="col"
                                class="p-5 text-left text-sm leading-6 font-semibold text-gray-900 capitalize"
                            >
                                Original title
                            </th>
                            <th
                                scope="col"
                                class="p-5 text-left text-sm leading-6 font-semibold text-gray-900 capitalize"
                            >
                                Overview
                            </th>
                            <th
                                scope="col"
                                class="p-5 text-left text-sm leading-6 font-semibold text-gray-900 capitalize"
                            >
                                Poster
                            </th>
                            <th
                                scope="col"
                                class="p-5 text-left text-sm leading-6 font-semibold text-gray-900 capitalize"
                            >
                                Release date
                            </th>
                            <th
                                scope="col"
                                class="p-5 text-left text-sm leading-6 font-semibold text-gray-900 capitalize"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        <tr
                            v-for="movie in results"
                            :key="movie.id"
                            class="bg-white transition-all duration-500 hover:bg-gray-50"
                        >
                            <td
                                class="p-5 text-sm leading-6 font-medium text-gray-900"
                            >
                                {{ movie.original_title }} ({{ movie.title }})
                            </td>
                            <td
                                class="p-5 text-sm leading-6 font-medium text-gray-900"
                            >
                                {{ movie.overview }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex w-24 items-center gap-3">
                                    <img
                                        v-if="movie.poster_path"
                                        :src="
                                            tmdbImageBaseUrl + movie.poster_path
                                        "
                                        srcset=""
                                    />
                                </div>
                            </td>
                            <td
                                class="p-5 text-sm leading-6 font-medium text-gray-900"
                            >
                                {{ movie.release_date }}
                            </td>
                            <td class="flex items-center gap-0.5 p-5">
                                <Button @click="emitEvent(movie.id)"
                                    >Get Images</Button
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
