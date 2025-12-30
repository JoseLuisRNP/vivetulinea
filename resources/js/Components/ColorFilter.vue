<script setup lang="ts">
  import { ref, watch, computed } from 'vue';

  const props = defineProps<{
    modelValue?: string | null;
  }>();

  const emit = defineEmits<{
    'update:modelValue': [value: string | null];
  }>();

  const selectedColor = ref<string | null>(props.modelValue || null);
  const detailsRef = ref<HTMLDetailsElement | null>(null);

  const colors = [
    { value: 'blue', label: 'Azul', class: 'bg-blue-500' },
    { value: 'green', label: 'Verde', class: 'bg-green-500' },
    { value: 'yellow', label: 'Amarillo', class: 'bg-yellow-500' },
    { value: 'red', label: 'Rojo', class: 'bg-red-500' },
    { value: 'black', label: 'Negro', class: 'bg-black' },
  ];

  const selectedLabel = computed(() => {
    if (!selectedColor.value) {
      return 'Todos';
    }
    const color = colors.find((c) => c.value === selectedColor.value);
    return color ? color.label : 'Todos';
  });

  const selectedColorClass = computed(() => {
    if (!selectedColor.value) {
      return '';
    }
    return colors.find((c) => c.value === selectedColor.value)?.class || '';
  });

  const selectColor = (color: string | null) => {
    selectedColor.value = color;
    emit('update:modelValue', color);
    if (detailsRef.value) {
      detailsRef.value.removeAttribute('open');
    }
  };

  watch(
    () => props.modelValue,
    (newValue) => {
      selectedColor.value = newValue || null;
    }
  );
</script>

<template>
  <div class="fieldset max-w-xs w-[120px]">
    <label class="label">Filtrar por color</label>
    <details ref="detailsRef" class="dropdown w-full">
      <summary class="select select-bordered w-full cursor-pointer">
        <div class="flex items-center gap-2">
          <div
            v-if="selectedColor"
            :class="['w-3 h-3 rounded-full', selectedColorClass]"
          ></div>
          {{ selectedLabel }}
        </div>
      </summary>
      <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-full p-2 shadow">
        <li>
          <button
            type="button"
            class="flex items-center gap-2"
            :class="selectedColor === null ? 'active' : ''"
            @click="selectColor(null)"
          >
            Todos
          </button>
        </li>
        <li
          v-for="color in colors"
          :key="color.value"
        >
          <button
            type="button"
            class="flex items-center gap-2"
            :class="selectedColor === color.value ? 'active' : ''"
            @click="selectColor(color.value)"
          >
            <div :class="['w-3 h-3 rounded-full', color.class]"></div>
            {{ color.label }}
          </button>
        </li>
      </ul>
    </details>
  </div>
</template>

