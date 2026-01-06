<script setup lang="ts">
import {
    Calendar1Icon,
    ChevronLeftIcon,
    ChevronRightIcon,
} from 'lucide-vue-next';
import type { DateRange } from 'reka-ui';
import {
    DateRangePickerArrow,
    DateRangePickerCalendar,
    DateRangePickerCell,
    DateRangePickerCellTrigger,
    DateRangePickerContent,
    DateRangePickerField,
    DateRangePickerGrid,
    DateRangePickerGridBody,
    DateRangePickerGridHead,
    DateRangePickerGridRow,
    DateRangePickerHeadCell,
    DateRangePickerHeader,
    DateRangePickerHeading,
    DateRangePickerInput,
    DateRangePickerNext,
    DateRangePickerPrev,
    DateRangePickerRoot,
    DateRangePickerTrigger,
} from 'reka-ui';

interface Props {
    dateRange: DateRange;
}
defineProps<Props>();

defineEmits(['update:dateRange']);
</script>

<template>
    <div class="flex flex-col gap-2">
        <DateRangePickerRoot
            :modelValue="dateRange"
            @update:modelValue="$emit('update:dateRange', $event)"
        >
            <DateRangePickerField
                v-slot="{ segments }"
                class="text-green10 flex items-center rounded-lg border bg-white p-1 text-center shadow-sm select-none data-[invalid]:border-red-500"
            >
                <template v-for="item in segments.start" :key="item.part">
                    <DateRangePickerInput
                        v-if="item.part === 'literal'"
                        :part="item.part"
                        type="start"
                    >
                        {{ item.value }}
                    </DateRangePickerInput>
                    <DateRangePickerInput
                        v-else
                        :part="item.part"
                        class="data-[placeholder]:text-green9 rounded-md p-0.5 focus:shadow-[0_0_0_2px] focus:shadow-black focus:outline-none"
                        type="start"
                    >
                        {{ item.value }}
                    </DateRangePickerInput>
                </template>
                <span class="mx-2"> - </span>
                <template v-for="item in segments.end" :key="item.part">
                    <DateRangePickerInput
                        v-if="item.part === 'literal'"
                        :part="item.part"
                        type="end"
                    >
                        {{ item.value }}
                    </DateRangePickerInput>
                    <DateRangePickerInput
                        v-else
                        :part="item.part"
                        class="data-[placeholder]:text-green9 rounded-md p-0.5 focus:shadow-[0_0_0_2px] focus:shadow-black focus:outline-none"
                        type="end"
                    >
                        {{ item.value }}
                    </DateRangePickerInput>
                </template>

                <DateRangePickerTrigger
                    class="ml-4 rounded p-1 focus:shadow-[0_0_0_2px] focus:shadow-black focus:outline-none"
                >
                    <Calendar1Icon class="text-green9 h-5 w-5" />
                </DateRangePickerTrigger>
            </DateRangePickerField>

            <DateRangePickerContent
                :side-offset="4"
                class="data-[state=open]:data-[side=top]:animate-slideDownAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade rounded-xl border bg-white shadow-sm will-change-[transform,opacity]"
            >
                <DateRangePickerArrow class="fill-white stroke-gray-300" />
                <DateRangePickerCalendar
                    v-slot="{ weekDays, grid }"
                    class="p-4"
                >
                    <DateRangePickerHeader
                        class="flex items-center justify-between"
                    >
                        <DateRangePickerPrev
                            class="inline-flex h-7 w-7 cursor-pointer items-center justify-center rounded-md bg-transparent text-black hover:bg-stone-100 focus:shadow-[0_0_0_2px] focus:shadow-black active:scale-98 active:transition-all"
                        >
                            <ChevronLeftIcon />
                        </DateRangePickerPrev>

                        <DateRangePickerHeading
                            class="text-sm font-medium text-black"
                        />
                        <DateRangePickerNext
                            class="inline-flex h-7 w-7 cursor-pointer items-center justify-center rounded-md bg-transparent text-black hover:bg-stone-100 focus:shadow-[0_0_0_2px] focus:shadow-black active:scale-98 active:transition-all"
                        >
                            <ChevronRightIcon />
                        </DateRangePickerNext>
                    </DateRangePickerHeader>
                    <div
                        class="flex flex-col space-y-4 pt-4 sm:flex-row sm:space-y-0 sm:space-x-4"
                    >
                        <DateRangePickerGrid
                            v-for="month in grid"
                            :key="month.value.toString()"
                            class="w-full border-collapse space-y-1 select-none"
                        >
                            <DateRangePickerGridHead>
                                <DateRangePickerGridRow
                                    class="mb-1 flex w-full justify-between"
                                >
                                    <DateRangePickerHeadCell
                                        v-for="day in weekDays"
                                        :key="day"
                                        class="w-8 rounded-md text-xs !font-normal text-black"
                                    >
                                        {{ day }}
                                    </DateRangePickerHeadCell>
                                </DateRangePickerGridRow>
                            </DateRangePickerGridHead>
                            <DateRangePickerGridBody>
                                <DateRangePickerGridRow
                                    v-for="(weekDates, index) in month.rows"
                                    :key="`weekDate-${index}`"
                                    class="flex w-full"
                                >
                                    <DateRangePickerCell
                                        v-for="weekDate in weekDates"
                                        :key="weekDate.toString()"
                                        :date="weekDate"
                                    >
                                        <DateRangePickerCellTrigger
                                            :day="weekDate"
                                            :month="month.value"
                                            class="data-[selected]:!bg-green10 hover:bg-green5 data-[highlighted]:bg-green5 data-[today]:before:bg-green9 relative flex h-8 w-8 items-center justify-center rounded-full text-sm font-normal whitespace-nowrap text-black outline-none before:absolute before:top-[5px] before:hidden before:h-1 before:w-1 before:rounded-full before:bg-white focus:shadow-[0_0_0_2px] focus:shadow-black data-[outside-view]:text-black/30 data-[selected]:text-white data-[today]:before:block data-[unavailable]:pointer-events-none data-[unavailable]:text-black/30 data-[unavailable]:line-through"
                                        />
                                    </DateRangePickerCell>
                                </DateRangePickerGridRow>
                            </DateRangePickerGridBody>
                        </DateRangePickerGrid>
                    </div>
                </DateRangePickerCalendar>
            </DateRangePickerContent>
        </DateRangePickerRoot>
    </div>
</template>

<style scoped>
.data-\[selected\]\:\!bg-green10[data-selected] {
    --tw-bg-opacity: 1 !important;
    background-color: rgb(43 154 102 / var(--tw-bg-opacity, 1)) !important;
}
</style>
