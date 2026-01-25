<script setup lang="ts">
import { Button } from '@/components/ui/button';
const emit = defineEmits(['searchImages']);

interface Props {
    results: Array<{
        id: number;
        name: string;
        original_name: string;
        profile_path: string | null;
        known_for: Array<{
            id: number;
            title: string;
            original_title: string;
            release_date: string;
        }>;
    }>;
    tmdbImageBaseUrl?: string;
}
defineProps<Props>();

const emitEvent = (person_id: number) => {
    emit('searchImages', person_id);
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
                                Name
                            </th>
                            <th
                                scope="col"
                                class="p-5 text-left text-sm leading-6 font-semibold text-gray-900 capitalize"
                            >
                                Known for
                            </th>
                            <th
                                scope="col"
                                class="p-5 text-left text-sm leading-6 font-semibold text-gray-900 capitalize"
                            >
                                Image
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
                            v-for="person in results"
                            :key="person.id"
                            class="bg-white transition-all duration-500 hover:bg-gray-50"
                        >
                            <td
                                class="p-5 text-sm leading-6 font-medium text-gray-900"
                            >
                                {{ person.name }} ({{ person.original_name }})
                            </td>
                            <td
                                class="p-5 text-sm leading-6 font-medium text-gray-900"
                            >
                                <ul>
                                    <li
                                        v-for="movie in person.known_for"
                                        :key="movie.id"
                                    >
                                        {{ movie.title }} ({{
                                            movie.original_title
                                        }}) - {{ movie.release_date }}
                                    </li>
                                </ul>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex w-24 items-center gap-3">
                                    <img
                                        v-if="person.profile_path"
                                        :src="
                                            tmdbImageBaseUrl +
                                            person.profile_path
                                        "
                                        srcset=""
                                    />
                                </div>
                            </td>
                            <td class="flex items-center gap-0.5 p-5">
                                <Button
                                    label="Get Images"
                                    @click="emitEvent(person.id)"
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
