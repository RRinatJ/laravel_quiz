<script setup lang="ts">
import { CircleCheck, CircleX } from 'lucide-vue-next';

type Size = 'sm' | 'default';

interface Props {
    checkValue: string | boolean | number | null;
    size?: Size;
}

const { checkValue, size = 'default' } = defineProps<Props>();
const getTypeClass = () => {
    switch (size) {
        case 'sm':
            return 'h-5 w-5';
    }
    return '';
};
</script>

<template>
    <component
        :is="checkValue ? CircleCheck : CircleX"
        :class="[
            checkValue ? 'text-green-500' : 'text-red-500',
            getTypeClass(),
            'ml-2 inline',
        ]"
    />
</template>
