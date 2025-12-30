<script setup lang="ts">
  import NavBar from '@/Components/Layout/NavBar.vue';
  import ColorFilter from '@/Components/ColorFilter.vue';
  import FoodListItem from '@/Components/FoodListItem.vue';
  import { ref } from 'vue';
  import { watchDebounced } from '@vueuse/core';
  import { Head, router, Link, usePage } from '@inertiajs/vue3';
  import ziggyRoute from 'ziggy-js';

  interface Paginate<T> {
    data: T[];
    links: {
      active: boolean;
      label: string;
      url: string | null;
    };
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
  }

  interface Food {
    id: number;
    name: string;
    color: string;
    points: number;
    quantity: number;
    unit?: string;
    is_favorite?: boolean;
  }

  const props = defineProps<{
    foods: Paginate<Food>;
  }>();

  const page = usePage();
  const urlParams = new URLSearchParams(window.location.search);
  const search = ref(urlParams.get('q') || '');
  const selectedColor = ref<string | null>(urlParams.get('color') || null);

  const handleColorChange = (color: string | null) => {
    selectedColor.value = color;
    router.get(
      page.url,
      {
        q: search.value,
        color: color,
      },
      {
        preserveState: true,
        preserveScroll: true,
      }
    );
  };

  watchDebounced(
    search,
    () => {
      router.get(
        page.url,
        {
          q: search.value,
          color: selectedColor.value,
        },
        {
          preserveState: true,
          preserveScroll: true,
        }
      );
    },
    { debounce: 300 }
  );

  const handleFoodClick = (foodId: number) => {
    router.visit(ziggyRoute('points.show', { food: foodId }));
  };

  const handleToggleFavorite = (foodId: number) => {
    router.post(
      ziggyRoute('favorites.toggle', { food: foodId }),
      {},
      {
        preserveState: true,
        preserveScroll: true,
      }
    );
  };
</script>

<template>
  <Head title="Listado de alimentos" />
  <NavBar />

  <div class="flex flex-col content-center items-center h-full">
    <div class="px-6 py-4 sm:py-32 lg:px-8">
      <div class="mx-auto max-w-2xl text-center">
        <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
          Listado de alimentos
        </h2>
        <p class="text-base font-semibold leading-7 text-primary">
          Selecciona un alimento para registrar puntos
        </p>
      </div>
    </div>
    <div class="flex gap-4 items-end justify-center mt-4">
      <div class="fieldset max-w-xs">
        <input
          v-model="search"
          type="text"
          placeholder="Buscar alimento"
          class="input"
        />
      </div>

      <ColorFilter v-model="selectedColor" @update:model-value="handleColorChange" />
    </div>

    <div class="mt-4 w-full p-4">
      <ul>
        <FoodListItem
          v-for="food in props.foods.data"
          :key="food.id"
          :food="food"
          :clickable="true"
          @click="handleFoodClick"
          @toggle-favorite="handleToggleFavorite"
        />
      </ul>
    </div>

    <div
      v-if="props.foods.prev_page_url || props.foods.next_page_url"
      class="join justify-self-end sticky bottom-0"
    >
      <Link
        v-if="props.foods.prev_page_url"
        :href="props.foods.prev_page_url"
        class="join-item btn bg-primary text-primary-content"
      >
        Anterior
      </Link>
      <button
        v-else
        disabled
        class="join-item btn bg-gray-100 text-neutral"
      >
        Anterior
      </button>
      <Link
        v-if="props.foods.next_page_url"
        :href="props.foods.next_page_url"
        class="join-item btn bg-primary text-primary-content"
      >
        Siguiente
      </Link>
      <button
        v-else
        disabled
        class="join-item btn bg-gray-100 text-neutral"
      >
        Siguiente
      </button>
    </div>
  </div>
</template>

