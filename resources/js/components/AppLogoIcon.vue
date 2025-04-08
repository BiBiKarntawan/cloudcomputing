<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <div class="flex justify-center mb-6">
        <img src="/logo.png" alt="App Logo" style=" max-width : 250px" />
    </div>
</template>
