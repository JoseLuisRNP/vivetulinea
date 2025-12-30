<script setup lang="ts">
  import NavBar from '@/Components/Layout/NavBar.vue';
  import { ref } from 'vue';
  import { watchDebounced } from '@vueuse/core';
  import { Head, router, Link } from '@inertiajs/vue3';

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
    special_no_count: boolean;
    oil_no_count: boolean;
  }

  const props = defineProps<{
    foods: Paginate<Food>;
  }>();

  const search = ref('');

  watchDebounced(
    search,
    () => {
      router.reload({ preserveState: true, data: { q: search.value } });
    },
    { debounce: 300 }
  );
</script>
<template>
  <Head title="Listado día de no contar" />
  <NavBar />

  <div class="flex flex-col content-center items-center h-full">
    <div class="px-6 py-4 sm:py-32 lg:px-8">
      <div class="mx-auto max-w-2xl text-center">
        <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
          Listado día de no contar
        </h2>
        <p class="text-base font-semibold leading-7 text-primary">
          Recuerda los alimentos marcados en rosa máximo 3 al día, verde máximo 2 al día
        </p>
      </div>
    </div>

    <div class="fieldset max-w-xs mt-4">
      <input
        v-model="search"
        type="text"
        placeholder="Buscar alimento"
        class="input"
      />
    </div>

    <div class="mt-4 w-full p-4">
      <ul>
        <li
          v-for="food in foods.data"
          :key="food.id"
          class="flex items-center border-b p-2 w-full"
          :class="{
            'bg-pink-100': food.special_no_count,
            'bg-green-100': food.oil_no_count,
          }"
        >
          <div
            class="w-2 h-2 rounded-full mr-2"
            :class="{
              'bg-blue-500': food.color === 'blue',
              'bg-green-500': food.color === 'green',
              'bg-yellow-500': food.color === 'yellow',
              'bg-red-500': food.color === 'red',
              'bg-black': food.color === 'black',
            }"
          />
          <div class="flex justify-between w-full">
            <div>{{ food.name }}</div>
            <div v-show="food.special_no_count || food.oil_no_count">
              {{ food.quantity }}{{ food.unit }}
            </div>
          </div>
        </li>
      </ul>
    </div>
    <div
      v-show="foods.prev_page_url || foods.next_page_url"
      class="join justify-self-end sticky bottom-0"
    >
      <Link
        v-if="foods.prev_page_url"
        :href="foods.prev_page_url"
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
        v-if="foods.next_page_url"
        :href="foods.next_page_url"
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
