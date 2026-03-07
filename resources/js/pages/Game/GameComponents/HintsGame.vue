<script setup lang="ts">
const emit = defineEmits(['fiftyFiftyHint', 'skipQuestion']);

interface Props {
    fifty_fifty_hint: boolean;
    can_skip: boolean;
}
defineProps<Props>();
</script>

<template>
    <div class="mt-6 flex justify-center gap-4">
        <button
            v-if="fifty_fifty_hint"
            class="option info"
            @click="emit('fiftyFiftyHint')"
        >
            50/50
        </button>
        <button
            v-if="can_skip"
            class="option info"
            @click="emit('skipQuestion')"
        >
            Skip
        </button>
    </div>
</template>
