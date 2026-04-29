<script setup lang="ts">
import LogOldNew from '@/components/log/LogOldNew.vue';
import LogTableAnswers from '@/components/log/LogTableAnswers.vue';
import LogTableQuizzes from '@/components/log/LogTableQuizzes.vue';
import { Button } from '@/components/ui/button';
import { type Log } from '@/types';
import { ref } from 'vue';
import LogTableTags from './LogTableTags.vue';

const show = ref(false);

defineProps<{
    items: Log[];
}>();
</script>

<template>
    <div
        class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm"
    >
        <div class="flex items-center gap-4">
            <h2 class="px-4 pt-3 pb-3 text-2xl font-semibold">Log History</h2>
            <div class="px-4 pt-3 pb-3">
                <Button
                    @click="show = !show"
                    class="rounded bg-blue-600 text-white transition hover:bg-blue-700"
                >
                    {{ show ? 'Hide' : 'Show' }}
                </Button>
            </div>
        </div>
        <table v-if="show" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left tracking-wider">User</th>
                    <th class="px-6 py-3 text-left tracking-wider">
                        Description
                    </th>
                    <th class="px-6 py-3 text-left tracking-wider">Changes</th>
                    <th class="px-6 py-3 text-left tracking-wider">
                        Created At
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-if="items.length === 0">
                    <td
                        colspan="5"
                        class="px-6 py-8 text-center text-sm text-gray-400"
                    >
                        No records to display
                    </td>
                </tr>
                <tr
                    v-for="item in items"
                    :key="item.id"
                    class="transition-colors hover:bg-gray-50"
                >
                    <td
                        class="px-6 py-4 text-sm whitespace-nowrap text-gray-700"
                    >
                        <span
                            class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800"
                        >
                            {{ item.user.name }}
                        </span>
                    </td>
                    <td
                        class="px-6 py-4 text-sm whitespace-nowrap text-gray-700 capitalize"
                    >
                        {{ item.description }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div
                            v-if="item.changes?.attributes"
                            class="flex flex-col gap-1"
                        >
                            <LogOldNew :log="item.changes" />
                        </div>
                        <div
                            v-else-if="item.properties?.quizzes"
                            class="flex flex-col gap-1"
                        >
                            <LogTableQuizzes
                                :quizzes="item.properties.quizzes"
                            />
                        </div>
                        <div
                            v-else-if="item.properties?.answers"
                            class="flex flex-col gap-1"
                        >
                            <LogTableAnswers
                                :answers="item.properties.answers"
                            />
                        </div>
                        <div
                            v-else-if="item.properties?.tags"
                            class="flex flex-col gap-1"
                        >
                            <LogTableTags :tags="item.properties.tags" />
                        </div>
                        <span v-else class="text-gray-400 italic"
                            >No tracked changes</span
                        >
                    </td>
                    <td
                        class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                    >
                        {{ item.created_at }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
