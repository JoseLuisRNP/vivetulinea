<template>
  <svg aria-hidden="true" :class="className">
    <use :href="symbolId" />
  </svg>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(defineProps<{
  name: string;
  prefix?: string;
  className?: string;
}>(), {
  prefix: 'icon',
  className: 'w-6 h-6'
});

const symbolId = computed(() => `#${props.prefix}-${props.name}`);
</script>

