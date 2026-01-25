<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Overlay from '@/components/Overlay.vue';
import TmdbMovieTable from '@/components/tmdb/MovieTable.vue';
import TmdbPersonTable from '@/components/tmdb/PersonTable.vue';
import TmdbTvTable from '@/components/tmdb/TvTable.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { images, search } from '@/routes/tmdb';
import axios, { AxiosError } from 'axios';
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next';
import { computed, getCurrentInstance, nextTick, ref } from 'vue';

const emit = defineEmits(['setImageFromTmdb']);
const tmdbImageBaseUrl = 'https://image.tmdb.org/t/p/';
interface ValidationErrors {
    [field: string]: string[];
}
const tmdbBackdropSizes = ['w300', 'w780', 'w1280', 'original'];
const galleryRefEl = ref<HTMLElement | null>(null);
const instance = getCurrentInstance();
const uuid = instance?.uid;
const searchError = ref<ValidationErrors | []>([]);
const tmdbSearchQuery = ref('');
const tmdbSearchLoading = ref(false);
const tmdbImageLoading = ref(false);
const tmdbSearchTypes = [
    { type: 'movie', name: 'Movie' },
    { type: 'tv', name: 'TV Show' },
    { type: 'person', name: 'Person' },
];
const selectedTmdbType = ref(tmdbSearchTypes[0].type);
const tmdbSearchResult = ref<{ [key: string]: any }>({});
const tmdbImages = ref<{ [key: string]: any[] }>({});
const carouselValueKey = computed(() =>
    selectedTmdbType.value == 'person' ? 'profiles' : 'backdrops',
);
const itemsPerPage = ref(5);
const currentPage = ref(1);
const currentTmdbImagesKey = ref('');
const totalPages = computed(() =>
    Math.ceil(
        tmdbImages.value[currentTmdbImagesKey.value].length /
            itemsPerPage.value,
    ),
);
const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    return tmdbImages.value[currentTmdbImagesKey.value].slice(
        start,
        start + itemsPerPage.value,
    );
});
const scrollToGallery = async () => {
    // Wait for the DOM to update if necessary
    await nextTick();

    galleryRefEl.value?.scrollIntoView({
        behavior: 'smooth', // Optional: adds a smooth scrolling animation
        block: 'start', // Optional: aligns the top of the element to the top of the view
    });
};
const tmdbSearch = () => {
    searchError.value = [];
    tmdbSearchLoading.value = true;
    axios
        .get(search().url, {
            params: {
                query: tmdbSearchQuery.value,
                type: selectedTmdbType.value,
            },
        })
        .then((response) => {
            tmdbSearchResult.value = response.data;
            tmdbSearchLoading.value = false;
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
            tmdbSearchLoading.value = false;
        });
};
const tmdbImagesSearch = (tmdb_id: number) => {
    if (tmdbImages.value[selectedTmdbType.value + '_' + tmdb_id]) {
        currentTmdbImagesKey.value = selectedTmdbType.value + '_' + tmdb_id;
        scrollToGallery();
        return;
    }
    tmdbImageLoading.value = true;
    axios
        .get(images().url, {
            params: { tmdb_id: tmdb_id, type: selectedTmdbType.value },
        })
        .then((response) => {
            if (response.data) {
                currentTmdbImagesKey.value =
                    selectedTmdbType.value + '_' + tmdb_id;
                tmdbImages.value[currentTmdbImagesKey.value] =
                    response.data[carouselValueKey.value];
                tmdbImageLoading.value = false;
                scrollToGallery();
            }
        });
};
const setImageFromTmdb = (file_path: string, size: string) => {
    emit('setImageFromTmdb', tmdbImageBaseUrl + size + file_path);
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Get images from TMDB</CardTitle>
        </CardHeader>
        <CardContent>
            <div class="mb-4">
                <Label :for="`r1-${uuid}`">TMDB Search Type</Label>
                <Select v-model="selectedTmdbType">
                    <SelectTrigger :id="`r1-${uuid}`">
                        <SelectValue placeholder="Select type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="tmdb_type in tmdbSearchTypes"
                            :key="tmdb_type.type"
                            :value="tmdb_type.type"
                        >
                            {{ tmdb_type.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="mb-2 space-y-6">
                <div class="grid gap-2">
                    <Label :for="`tmdb_search_query-${uuid}`"
                        >Search Movie (TMDB)</Label
                    >
                    <Input
                        :id="`tmdb_search_query-${uuid}`"
                        type="text"
                        v-model="tmdbSearchQuery"
                        class="mt-1 block w-full"
                        name="tmdb_search_query"
                        placeholder="Search query..."
                    />
                    <InputError
                        v-if="'error' in searchError"
                        :message="searchError.error[0]"
                    />
                </div>
                <div class="flex items-center gap-4">
                    <Button
                        :disabled="
                            tmdbSearchLoading || tmdbSearchQuery.length < 3
                        "
                        @click="tmdbSearch()"
                    >
                        Search
                    </Button>
                </div>
            </div>
            <Overlay :is-blur="tmdbImageLoading">
                <TmdbMovieTable
                    v-if="
                        tmdbSearchResult.hasOwnProperty('results') &&
                        selectedTmdbType === 'movie'
                    "
                    :tmdb-image-base-url="tmdbImageBaseUrl + 'w300'"
                    :results="tmdbSearchResult.results"
                    @search-images="tmdbImagesSearch"
                />
                <TmdbTvTable
                    v-if="
                        tmdbSearchResult.hasOwnProperty('results') &&
                        selectedTmdbType == 'tv'
                    "
                    :tmdb-image-base-url="tmdbImageBaseUrl + 'w300'"
                    :results="tmdbSearchResult.results"
                    @search-images="tmdbImagesSearch"
                />
                <TmdbPersonTable
                    v-if="
                        tmdbSearchResult.hasOwnProperty('results') &&
                        selectedTmdbType === 'person'
                    "
                    :tmdb-image-base-url="tmdbImageBaseUrl + 'w300'"
                    :results="tmdbSearchResult.results"
                    @search-images="tmdbImagesSearch"
                />
            </Overlay>
            <div v-if="Object.keys(tmdbImages).length" class="mt-4">
                <div ref="galleryRefEl" class="masonry-grid">
                    <div
                        v-for="(image, key) in paginatedItems"
                        :key="key"
                        class="masonry-item group flex flex-col overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm transition-all hover:shadow-xl dark:border-slate-700 dark:bg-slate-800"
                    >
                        <div
                            class="relative overflow-hidden bg-slate-200 dark:bg-slate-700"
                        >
                            <img
                                v-if="image.file_path"
                                :src="
                                    tmdbImageBaseUrl + 'w300' + image.file_path
                                "
                                srcset=""
                                class="h-auto w-full rounded-lg object-cover"
                            />
                        </div>
                        <div class="p-3">
                            <div class="flex gap-2">
                                <button
                                    v-for="tmdb_img_size in tmdbBackdropSizes"
                                    :key="tmdb_img_size"
                                    @click="
                                        setImageFromTmdb(
                                            image.file_path,
                                            tmdb_img_size,
                                        )
                                    "
                                    class="h-8 flex-1 rounded-lg bg-primary text-xs font-bold text-white transition-colors hover:bg-primary/90"
                                >
                                    {{ tmdb_img_size }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    v-if="totalPages > 1"
                    class="mt-4 flex flex-row items-center gap-1"
                >
                    <button
                        @click="currentPage--"
                        :disabled="currentPage === 1"
                        class="flex items-center gap-1 px-2.5 hover:bg-accent hover:text-accent-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 sm:pr-2.5 dark:hover:bg-accent/50 dark:aria-invalid:ring-destructive/40"
                    >
                        <ChevronLeftIcon />
                        <span class="hidden sm:block">Previous</span>
                    </button>

                    <span> Page {{ currentPage }} of {{ totalPages }} </span>

                    <button
                        @click="currentPage++"
                        :disabled="currentPage === totalPages"
                        class="flex items-center gap-1 px-2.5 hover:bg-accent hover:text-accent-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 sm:pr-2.5 dark:hover:bg-accent/50 dark:aria-invalid:ring-destructive/40"
                    >
                        <span class="hidden sm:block">Next</span>
                        <ChevronRightIcon />
                    </button>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<style scoped>
.material-symbols-outlined {
    font-variation-settings:
        'FILL' 0,
        'wght' 400,
        'GRAD' 0,
        'opsz' 24;
}
.masonry-grid {
    column-count: 2;
    column-gap: 1.5rem;
}
@media (min-width: 768px) {
    .masonry-grid {
        column-count: 3;
    }
}
@media (min-width: 1024px) {
    .masonry-grid {
        column-count: 4;
    }
}
@media (min-width: 1280px) {
    .masonry-grid {
        column-count: 5;
    }
}
.masonry-item {
    break-inside: avoid;
    margin-bottom: 1.5rem;
}
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
