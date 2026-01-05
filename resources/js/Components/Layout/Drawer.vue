<script setup>
  defineProps({
    drawerId: {
      type: String,
      default: 'nav-drawer',
    },
    position: {
      type: String,
      default: 'end',
      validator: (value) => ['start', 'end'].includes(value),
    },
  });
</script>

<template>
  <div :class="['drawer', `drawer-${position}`]">
    <input :id="drawerId" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
      <slot name="content" />
    </div>
    <div class="drawer-side">
      <label :for="drawerId" aria-label="close sidebar" class="drawer-overlay"></label>
      <slot name="sidebar" />
    </div>
  </div>
</template>

