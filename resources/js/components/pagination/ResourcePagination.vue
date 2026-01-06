<script setup lang="ts">
import { Button } from '@/components/ui/button';

import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { PaginatedResourceResponse } from '@/types';

interface Props {
    dataPagination: PaginatedResourceResponse;
}

const props = defineProps<Props>();

const queryString = (value: number) => {
    return (
        props.dataPagination.meta.links.find(
            (link) => link.label === value.toString(),
        )?.url || ''
    );
};
</script>

<template>
    <Pagination
        v-slot="{ page }"
        :items-per-page="dataPagination.meta.per_page"
        :total="dataPagination.meta.total"
        :sibling-count="1"
        show-edges
        :default-page="dataPagination.meta.current_page"
    >
        <PaginationContent v-slot="{ items }" class="flex items-center gap-1">
            <a
                :href="
                    dataPagination.meta.current_page !== 1
                        ? dataPagination.links.first
                        : undefined
                "
                ><PaginationFirst
            /></a>
            <a
                :href="
                    dataPagination.links.prev
                        ? dataPagination.links.prev
                        : undefined
                "
                ><PaginationPrevious
            /></a>
            <template v-for="(item, index) in items">
                <PaginationItem
                    v-if="item.type === 'page'"
                    :key="item.value"
                    :value="item.value"
                    as-child
                >
                    <a :href="queryString(item.value)">
                        <Button
                            class="h-10 w-10 p-0"
                            :variant="
                                item.value === page ? 'default' : 'outline'
                            "
                        >
                            {{ item.value }}
                        </Button>
                    </a>
                </PaginationItem>
                <PaginationEllipsis v-else :key="item.type" :index="index" />
            </template>
            <a
                :href="
                    dataPagination.links.next
                        ? dataPagination.links.next
                        : undefined
                "
                ><PaginationNext
            /></a>
            <a
                :href="
                    dataPagination.meta.last_page !==
                    dataPagination.meta.current_page
                        ? dataPagination.links.last
                        : undefined
                "
                ><PaginationLast
            /></a>
        </PaginationContent>
    </Pagination>
</template>
