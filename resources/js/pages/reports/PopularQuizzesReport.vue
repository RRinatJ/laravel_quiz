<script setup lang="ts">
import DateRangePicker from '@/components/DateRangePicker.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import popular_quizzes from '@/routes/reports';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { getLocalTimeZone, parseDate, today } from '@internationalized/date';
import type { DateRange } from 'reka-ui';
import { ref, type Ref } from 'vue';

interface ValidationErrors {
    [field: string]: string;
}

interface Props {
    data: Array<{
        quiz_id: number;
        quiz: {
            title: string;
        };
        play_count: number;
        win_count: number;
        unregistered_count: number;
        registered_count: number;
    }>;
    filters:
        | {
              end: string | null;
              start: string | null;
          }
        | [];
}
const props = defineProps<Props>();

let start, end;
if (!Array.isArray(props.filters) && props.filters.start && props.filters.end) {
    start = parseDate(props.filters.start);
    end = parseDate(props.filters.end);
} else {
    end = today(getLocalTimeZone());
    start = end.subtract({ days: 7 });
}

const errorsForm = ref<ValidationErrors>({});
const processing = ref(false);

const dateRange = ref({
    start,
    end,
}) as Ref<DateRange>;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Popular Quizzes Report',
        href: popular_quizzes.popular_quizzes().url,
    },
];

const params = ref({
    start: dateRange.value.start?.toString() || null,
    end: dateRange.value.end?.toString() || null,
});

const dateRangeChange = (newRange: DateRange) => {
    dateRange.value = newRange;
    params.value.start = newRange.start?.toString() || null;
    params.value.end = newRange.end?.toString() || null;
};

const getStat = () => {
    router.get(popular_quizzes.popular_quizzes(), params.value, {
        preserveScroll: true,
        preserveState: 'errors',
        onError: (errors) => {
            errorsForm.value = errors;
        },
    });
};

const getWinPrcnt = (winCount: number, playCount: number): string => {
    if (playCount === 0) return '0';
    return ((winCount * 100) / playCount).toFixed(2);
};

if (Array.isArray(props.filters)) {
    getStat();
}
</script>

<template>
    <Head title="Report Popular Quizzes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Popular Quizzes</h1>
            </div>
            <div class="mb-6 rounded-lg p-4 shadow">
                <div class="custom-justify-center flex w-full max-w-[700px]">
                    <DateRangePicker
                        :date-range="dateRange"
                        @update:dateRange="dateRangeChange"
                    />
                </div>
                <div v-if="errorsForm">
                    <span
                        v-for="(fieldError, field) in errorsForm"
                        :key="field"
                    >
                        <InputError class="mt-2" :message="fieldError" />
                    </span>
                </div>
                <div class="mt-4 flex">
                    <Button @click="getStat" :disabled="processing"
                        >Get Stats</Button
                    >
                </div>
                <table
                    class="mt-4 min-w-full rounded-lg shadow"
                    v-if="data.length > 0"
                >
                    <thead>
                        <tr>
                            <th class="border-b px-4 py-2 text-left">
                                Quiz ID
                            </th>
                            <th class="border-b px-4 py-2 text-left">
                                Quiz title
                            </th>
                            <th class="border-b px-4 py-2 text-left">
                                Play count
                            </th>
                            <th class="border-b px-4 py-2 text-left">
                                Percentage of wins
                            </th>
                            <th class="border-b px-4 py-2 text-left">
                                Played unregistered
                            </th>
                            <th class="border-b px-4 py-2 text-left">
                                Played registered
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="quiz in data" :key="quiz.quiz_id">
                            <td class="border-b px-4 py-2">
                                {{ quiz.quiz_id }}
                            </td>
                            <td class="border-b px-4 py-2">
                                {{ quiz.quiz.title }}
                            </td>
                            <td class="border-b px-4 py-2">
                                {{ quiz.play_count }}
                            </td>
                            <td class="border-b px-4 py-2">
                                {{
                                    getWinPrcnt(
                                        quiz.win_count,
                                        quiz.play_count,
                                    )
                                }}%
                            </td>
                            <td class="border-b px-4 py-2">
                                {{ quiz.unregistered_count }}
                            </td>
                            <td class="border-b px-4 py-2">
                                {{ quiz.registered_count }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="mt-4">
                    No data available for the selected date range.
                </div>
            </div>
        </div>
    </AppLayout>
</template>
