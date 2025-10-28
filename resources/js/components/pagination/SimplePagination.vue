<script setup lang="ts">
import {
  Button,
} from '@/components/ui/button'

import {
  Pagination,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationContent,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination'
import { PaginatedResponse } from '@/types';

interface Props {
    dataPagination: PaginatedResponse;
}

const props = defineProps<Props>();

const queryString = (value:number) => {  
  return props.dataPagination.links.find((link) => link.label === value.toString())?.url || '';
};

</script>

<template>    
  <Pagination v-slot="{ page }" :items-per-page="dataPagination.per_page" :total="dataPagination.total" :sibling-count="1" show-edges :default-page="dataPagination.current_page">
    <PaginationContent v-slot="{ items }" class="flex items-center gap-1">
      <a :href="dataPagination.current_page !== 1 ? dataPagination.first_page_url : undefined"><PaginationFirst /></a>
      <a :href="dataPagination.prev_page_url ? dataPagination.prev_page_url : undefined"><PaginationPrevious /></a>
      <template v-for="(item, index) in items">
        <PaginationItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
            <a :href="queryString(item.value)">
                <Button class="w-10 h-10 p-0" :variant="item.value === page ? 'default' : 'outline'">
                    {{ item.value }}
                </Button>
            </a>
        </PaginationItem>
        <PaginationEllipsis v-else :key="item.type" :index="index" />
      </template>
      <a :href="dataPagination.next_page_url ? dataPagination.next_page_url : undefined"><PaginationNext /></a>
      <a :href="dataPagination.last_page !== dataPagination.current_page ? dataPagination.last_page_url : undefined"><PaginationLast /></a>
    </PaginationContent>
  </Pagination>
</template>