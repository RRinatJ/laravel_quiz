<script setup lang="ts">
import SimplePagination from './SimplePagination.vue';
import ResourcePagination from './ResourcePagination.vue';
import { PaginateData, PaginatedResourceResponse } from '@/types';

interface Props {
    data: PaginateData;
}
const props = defineProps<Props>();

const hasMeta = (data: PaginateData): data is PaginatedResourceResponse => {
  return 'meta' in props.data
}
</script>

<template>
    <ResourcePagination v-if="hasMeta(data)" :data-pagination="data" />
    <SimplePagination v-else :data-pagination="data" />
</template>