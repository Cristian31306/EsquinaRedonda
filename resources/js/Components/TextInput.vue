<script>
export default {
    inheritAttrs: false,
};
</script>

<script setup>
import { onMounted, ref, computed } from 'vue';

const props = defineProps({
    toggleable: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: 'text',
    },
});

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);
const showPassword = ref(false);

const inputType = computed(() => {
    if (props.type === 'password' && props.toggleable) {
        return showPassword.value ? 'text' : 'password';
    }
    return props.type;
});

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div class="relative w-full">
        <input
            :type="inputType"
            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full"
            v-bind="$attrs"
            :class="[
                'w-full transition-all',
                type === 'password' && toggleable ? 'pr-12' : '',
                $attrs.class
            ]"
            v-model="model"
            ref="input"
        />
        <button
            v-if="type === 'password' && toggleable"
            type="button"
            @click="showPassword = !showPassword"
            class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors"
        >
            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.67 8.5 7.652 4.5 12 4.5c4.348 0 8.33 4 9.964 7.178.07.132.07.286 0 .418C20.33 15.5 16.348 19.5 12 19.5c-4.348 0-8.33-4-9.964-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
        </button>
    </div>
</template>
