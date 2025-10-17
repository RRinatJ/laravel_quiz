<script setup lang="ts">
import PublicAppFooter from '@/components/PublicAppFooter.vue';
import PublicAppHeader from '@/components/PublicAppHeader.vue';
</script>
<template>    
    <div class="relative flex size-full min-h-screen flex-col group/design-root overflow-x-hidden" style='font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;'>
        <div class="layout-container flex h-full grow flex-col min-h-screen">
            <PublicAppHeader />
            <slot></slot>
            <PublicAppFooter />
        </div>
    </div>    
</template>