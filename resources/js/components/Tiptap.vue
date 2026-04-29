<script setup lang="ts">
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import {
    Bold,
    ChevronsLeftRight,
    Code,
    Heading1,
    Heading2,
    Heading3,
    Italic,
    List,
    ListOrdered,
    Minus,
    Pilcrow,
    Quote,
    Redo2,
    Strikethrough,
    Undo2,
} from 'lucide-vue-next';
import { onBeforeUnmount, watch } from 'vue';

const emit = defineEmits(['update:modelValue']);

interface Props {
    modelValue?: string;
    activeButtons?: string[];
}

const { activeButtons = ['bold', 'italic'], modelValue = '' } =
    defineProps<Props>();

const editor = useEditor({
    content: modelValue || '',
    editorProps: {
        attributes: {
            class: 'prose prose-neutral max-w-none focus:outline-none min-h-[250px] p-5 border border-gray-300 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700',
        },
    },
    extensions: [StarterKit],
    onUpdate: () => {
        emit('update:modelValue', editor.value?.getHTML() || '');
    },
});

watch(
    () => modelValue,
    (val: string) => {
        editor.value?.commands.setContent(val);
    },
);

onBeforeUnmount(() => {
    editor.value?.destroy();
});
</script>

<template>
    <div v-if="editor">
        <span v-for="actionName in activeButtons" :key="actionName">
            <button
                v-if="actionName === 'bold'"
                :disabled="!editor.can().chain().focus().toggleBold().run()"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :class="{ 'ring-2 ring-indigo-900': editor.isActive('bold') }"
                @click="editor.chain().focus().toggleBold().run()"
            >
                <Bold class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'italic'"
                :disabled="!editor.can().chain().focus().toggleItalic().run()"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :class="{ 'ring-2 ring-indigo-900': editor.isActive('italic') }"
                @click="editor.chain().focus().toggleItalic().run()"
            >
                <Italic class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'strike'"
                :disabled="!editor.can().chain().focus().toggleStrike().run()"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :class="{ 'ring-2 ring-indigo-900': editor.isActive('strike') }"
                @click="editor.chain().focus().toggleStrike().run()"
            >
                <Strikethrough
                    class="h-4 w-4 flex-shrink-0"
                    aria-hidden="true"
                />
            </button>
            <button
                v-if="actionName === 'code'"
                :disabled="!editor.can().chain().focus().toggleCode().run()"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :class="{ 'ring-2 ring-indigo-900': editor.isActive('code') }"
                @click="editor.chain().focus().toggleCode().run()"
            >
                <Code class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'paragraph'"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :class="{
                    'ring-2 ring-indigo-900': editor.isActive('paragraph'),
                }"
                @click="editor.chain().focus().setParagraph().run()"
            >
                <Pilcrow class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'h1'"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :class="{
                    'ring-2 ring-indigo-900': editor.isActive('heading', {
                        level: 1,
                    }),
                }"
                @click="
                    editor.chain().focus().toggleHeading({ level: 1 }).run()
                "
            >
                <Heading1 class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'h2'"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :class="{
                    'ring-2 ring-indigo-900': editor.isActive('heading', {
                        level: 2,
                    }),
                }"
                @click="
                    editor.chain().focus().toggleHeading({ level: 2 }).run()
                "
            >
                <Heading2 class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'h3'"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :class="{
                    'ring-2 ring-indigo-900': editor.isActive('heading', {
                        level: 3,
                    }),
                }"
                @click="
                    editor.chain().focus().toggleHeading({ level: 3 }).run()
                "
            >
                <Heading3 class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'bulletList'"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :class="{
                    'ring-2 ring-indigo-900': editor.isActive('bulletList'),
                }"
                @click="editor.chain().focus().toggleBulletList().run()"
            >
                <List class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'orderedList'"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :class="{
                    'ring-2 ring-indigo-900': editor.isActive('orderedList'),
                }"
                @click="editor.chain().focus().toggleOrderedList().run()"
            >
                <ListOrdered class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'blockquote'"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :class="{
                    'ring-2 ring-indigo-900': editor.isActive('blockquote'),
                }"
                @click="editor.chain().focus().toggleBlockquote().run()"
            >
                <Quote class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'horizontalRule'"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                @click="editor.chain().focus().setHorizontalRule().run()"
            >
                <ChevronsLeftRight
                    class="h-4 w-4 flex-shrink-0"
                    aria-hidden="true"
                />
            </button>
            <button
                v-if="actionName === 'hardBreak'"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                @click="editor.chain().focus().setHardBreak().run()"
            >
                <Minus class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'undo'"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :disabled="!editor.can().chain().focus().undo().run()"
                @click="editor.chain().focus().undo().run()"
            >
                <Undo2 class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
            <button
                v-if="actionName === 'redo'"
                class="m-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                :disabled="!editor.can().chain().focus().redo().run()"
                @click="editor.chain().focus().redo().run()"
            >
                <Redo2 class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
            </button>
        </span>
        <editor-content :editor="editor" />
    </div>
</template>
