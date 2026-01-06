<script setup lang="ts">
import PublicAppFooter from '@/components/PublicAppFooter.vue';
import PublicAppHeader from '@/components/PublicAppHeader.vue';
</script>
<template>
    <div
        class="group/design-root relative flex size-full min-h-screen flex-col overflow-x-hidden"
        style="font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif"
    >
        <div class="layout-container flex h-full min-h-screen grow flex-col">
            <PublicAppHeader />
            <slot></slot>
            <PublicAppFooter />
        </div>
    </div>
</template>
