<script setup lang="ts">
import { useSlots } from 'vue';
import SvgIcon from '@/Components/SvgIcon.vue';
import { useUser } from '@/composables/useUser';

  interface Food {
    id: number;
    name: string;
    color: string;
    points: number;
    quantity: number;
    unit?: string;
    special_no_count?: boolean;
    oil_no_count?: boolean;
    is_favorite?: boolean;
  }

  const props = defineProps<{
    food: Food;
    showNoCountHighlight?: boolean;
    clickable?: boolean;
  }>();

  const emit = defineEmits<{
    click: [foodId: number];
    toggleFavorite: [foodId: number];
  }>();

  const handleClick = () => {
    if (props.clickable) {
      emit('click', props.food.id);
    }
  };

  const handleStarClick = (e: Event) => {
    e.stopPropagation();
    emit('toggleFavorite', props.food.id);
  };

  const colorClasses = {
    blue: 'bg-blue-500',
    green: 'bg-green-500',
    yellow: 'bg-yellow-500',
    red: 'bg-red-500',
    black: 'bg-black',
  };

  const slots = useSlots();

  const { isAdminOrDietician } = useUser();
</script>

<template>
  <li
    class="flex items-center border-b p-2 w-full"
    :class="{
      'bg-pink-100': showNoCountHighlight && food.special_no_count,
      'bg-green-100': showNoCountHighlight && food.oil_no_count,
      'cursor-pointer hover:bg-base-200': clickable,
    }"
    @click="handleClick"
  >
    <div
      class="w-2 h-2 rounded-full mr-2"
      :class="colorClasses[food.color as keyof typeof colorClasses] || 'bg-gray-500'"
    />
      <div class="flex justify-between w-full min-w-0 gap-2">
      <div class="flex items-center gap-2 min-w-0">
        <button
          v-if="food.is_favorite !== undefined && isAdminOrDietician"
          @click="handleStarClick"
          class="shrink-0 flex items-center justify-center w-5 h-5 hover:opacity-70 transition-opacity text-yellow-500"
          type="button"
        >
          <SvgIcon :name="food.is_favorite ? 'star-filled' : 'star'" class="w-5 h-5" />
        </button>
        <div class="line-clamp-2 min-w-0 wrap-break-word">{{ food.name }}</div>
      </div>
      <div class="flex items-center gap-2 shrink-0 whitespace-nowrap">
        <slot name="extra"></slot>
        <template v-if="!slots.extra">
            <div v-if="food.points && food.quantity">
          {{ food.points }} pts / {{ food.quantity }}{{ food.unit || '' }}
        </div>
        <div v-else-if="food.quantity">
          {{ food.quantity }}{{ food.unit || '' }}
        </div>
        </template>
       
      </div>
    </div>
  </li>
</template>

