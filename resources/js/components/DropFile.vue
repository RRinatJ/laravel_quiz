<script setup lang="ts">
import { CloudUpload } from 'lucide-vue-next';
import { computed, ref } from 'vue';
const emit = defineEmits(['input']);

interface Props {
    id?: string;
    multiple?: boolean;
}
withDefaults(defineProps<Props>(), {
    id: 'drag-and-drop-input',
    multiple: false,
});

const files = ref<File[]>([]);
const isHovered = ref(false);

const uploadInfo = computed(() => {
    return files.value.length === 1
        ? files.value[0].name
        : `${files.value.length} files selected`;
});

const handleUpload = (e: Event) => {
    const target = e.target as HTMLInputElement;
    files.value = Array.from(target.files || []);
    emit('input', e);
};

const handleMouseEnter = () => {
    isHovered.value = true;
};
const handleMouseLeave = () => {
    isHovered.value = false;
};
</script>

<template>
    <label
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        :for="id"
        class="relative block h-64 overflow-hidden rounded"
    >
        <input
            :id="id"
            type="file"
            class="absolute top-0 right-0 bottom-0 left-0 block w-full cursor-pointer rounded-full opacity-0"
            :multiple="multiple"
            @change="handleUpload"
        />
        <span
            class="group pointer-events-none absolute top-0 right-0 bottom-0 left-0 block w-full"
        >
            <div
                class="relative flex cursor-pointer flex-col items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-12 transition-all dark:border-slate-700 dark:bg-slate-800/30"
                :class="{
                    'bg-slate-100 dark:bg-slate-800/50': isHovered,
                }"
            >
                <div
                    class="mb-4 flex size-14 items-center justify-center rounded-full bg-[#13a4ec]/10 text-[#13a4ec] transition-transform"
                    :class="{
                        'scale-110': isHovered,
                    }"
                >
                    <span class="text-3xl"><CloudUpload /></span>
                </div>
                <p class="text-sm font-bold text-slate-900 dark:text-white">
                    Click to upload or drag and drop
                </p>
                <p class="mt-1 text-xs text-slate-500">
                    PNG, JPG or JPEG (max.size 2 MB)
                </p>
                <p class="mt-1 text-xs text-slate-500">{{ uploadInfo }}</p>
            </div>
        </span>
    </label>
</template>
