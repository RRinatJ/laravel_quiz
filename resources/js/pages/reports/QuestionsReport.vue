<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, type Ref } from 'vue';
import type { Question } from '@/types';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import type { DateRange } from 'reka-ui';
import { getLocalTimeZone, parseDate, today } from '@internationalized/date';
import OnOffIcon from '@/components/OnOffIcon.vue';
import DateRangePicker from '@/components/DateRangePicker.vue';
import questions_report from '@/routes/reports';

interface ValidationErrors {
    [field: string]: string;
}

interface Props {
    data: Array<{
        question_id: number;
        question: Question;
        play_count: number;
        corrected_count: number;
    }>;
    filters: {
        end: string | null;
        start: string | null;
    } | [];
}
const props = defineProps<Props>();

let start, end;
if(!Array.isArray(props.filters) && props.filters.start && props.filters.end){
    start = parseDate(props.filters.start);
    end = parseDate(props.filters.end);    
}else{
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
        title: 'Questions Report',
        href: questions_report.questions_report().url,
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
    router.get(questions_report.questions_report(), params.value, {
        preserveScroll: true,
        preserveState : "errors",
        onError: (errors) => {
            errorsForm.value = errors;
        },
    })    
};

const getWinPrcnt = (winCount: number, playCount: number): string => {
    if(playCount === 0) return "0";
    return ((winCount * 100) / playCount).toFixed(2);
};

if(Array.isArray(props.filters)){
    getStat();
}
</script>

<template>
    <Head title="Report Popular Quizzes" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Popular Quizzes</h1>
            </div>
            <div class="shadow rounded-lg p-4 mb-6">
                <div class="w-full max-w-[700px] flex custom-justify-center">
                    <DateRangePicker 
                        :date-range="dateRange"
                        @update:dateRange="dateRangeChange"
                    />
                </div>
                <div v-if="errorsForm" >
                    <span 
                        v-for="(fieldError, field) in errorsForm" 
                        :key="field"
                    >                                
                        <InputError class="mt-2" :message="fieldError" />
                    </span>
                </div>
                <div class="flex mt-4">
                    <Button @click="getStat" :disabled="processing">Get Stats</Button>
                </div>
                <table class="mt-4 min-w-full shadow rounded-lg" v-if="data.length > 0">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left border-b">Question ID</th>
                            <th class="py-2 px-4 text-left border-b">Question</th>
                            <th class="py-2 px-4 text-left border-b">Play count</th>
                            <th class="py-2 px-4 text-left border-b">Correctly answered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="question in data" :key="question.question_id" >
                            <td class="py-2 px-4 border-b">{{ question.question_id }}</td>
                            <td class="py-2 px-4 border-b">
                                <div v-if="question.question">
                                    <div v-if="question.question.question">{{ question.question.question }}</div>
                                    <div v-if="question.question.image">
                                        <img                                     
                                            class="w-32 h-auto rounded"
                                            :src="'/storage/'+question.question.image" 
                                            srcset=""
                                        >
                                    </div>
                                    <div v-if="question.question.audio">Audio: <OnOffIcon :check-value="true" :size="'sm'" /></div>
                                </div>
                                <div v-else>
                                    Question not found
                                </div>
                            </td>
                            <td class="py-2 px-4 border-b">{{ question.play_count }}</td>
                            <td class="py-2 px-4 border-b">{{ getWinPrcnt(question.corrected_count, question.play_count) }}%</td>
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