<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { search as tagSearch } from '@/routes/tag';
import { Tag } from '@/types';
import { useDebounceFn } from '@vueuse/core';
import axios, { AxiosError } from 'axios';
import { Search, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import Overlay from './Overlay.vue';

const emit = defineEmits(['addToTags', 'filterTags']);
interface Props {
    tags: Tag[];
}
defineProps<Props>();

interface ValidationErrors {
    [field: string]: string[];
}
const tagName = ref('');
const processing = ref(false);
const searchTags = ref<Tag[]>([]);
const searchError = ref<ValidationErrors | []>([]);

const search = useDebounceFn(() => {
    searchError.value = [];
    processing.value = true;
    if (tagName.value.length < 3) {
        searchTags.value = [];
        return;
    }
    axios
        .get(tagSearch().url, {
            params: {
                tag_name: tagName.value,
            },
        })
        .then((response) => {
            if (response.data) {
                searchTags.value = response.data;
            }
            processing.value = false;
        })
        .catch((error) => {
            if (axios.isAxiosError(error)) {
                const serverError = error as AxiosError<{
                    errors: ValidationErrors;
                }>;
                if (serverError.response?.data?.errors) {
                    searchError.value = serverError.response.data.errors;
                }
            }
            processing.value = false;
        });
}, 500);

watch(tagName, () => {
    search();
});
</script>

<template>
    <div class="space-y-8 lg:col-span-4">
        <div
            class="rounded-2xl border-2 border-slate-200 bg-white p-6 shadow-sm"
        >
            <h4
                class="mb-4 text-sm font-bold tracking-wider text-slate-800 uppercase"
            >
                Assign to Tags
            </h4>

            <div class="relative mb-4">
                <span
                    class="material-symbols-outlined absolute top-1/2 left-3 -translate-y-1/2 text-lg text-slate-400"
                    ><Search
                /></span>
                <Input
                    class="w-full rounded-xl border-slate-200 pr-4 pl-10 text-sm placeholder:text-slate-400 focus:border-primary focus:ring-primary"
                    v-model="tagName"
                    autocomplete="name"
                    placeholder="Search tags..."
                />
            </div>
            <InputError
                class="mt-2"
                v-if="'tag_name' in searchError"
                :message="searchError.tag_name[0]"
            />
            <Overlay :isBlur="processing">
                <div
                    class="custom-scrollbar max-h-[300px] space-y-3 overflow-y-auto pr-2"
                >
                    <label
                        v-for="search_tag in searchTags"
                        :key="search_tag.id"
                        class="flex cursor-pointer items-center gap-3 rounded-lg p-3 transition-colors hover:bg-slate-50"
                        @click="emit('addToTags', search_tag)"
                    >
                        <div>
                            <p class="text-sm font-bold text-slate-800">
                                {{ search_tag.name }}
                            </p>
                        </div>
                    </label>
                </div>
            </Overlay>
            <div
                v-if="tags.length"
                class="mt-4 flex min-h-[40px] flex-wrap gap-2"
            >
                <div
                    v-for="tag in tags"
                    :key="tag.id"
                    class="flex items-center gap-1.5 rounded-full bg-[#6366f1]/10 px-3 py-1.5 text-xs font-bold text-[#6366f1]"
                >
                    <span>
                        <p class="text-sm font-bold text-slate-800">
                            {{ tag.name }}
                        </p>
                    </span>
                    <button
                        @click="emit('filterTags', tag)"
                        class="flex items-center justify-center rounded-full transition-colors hover:bg-[#6366f1]/20"
                    >
                        <span class="material-symbols-outlined text-[14px]"
                            ><X :size="18"
                        /></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
