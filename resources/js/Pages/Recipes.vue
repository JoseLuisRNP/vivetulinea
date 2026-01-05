<script setup lang="ts">
  import NavBar from '@/Components/Layout/NavBar.vue';
  import { ref } from 'vue';
  import { watchDebounced } from '@vueuse/core';
  import { Head, router, Link, usePage } from '@inertiajs/vue3';
  import ziggyRoute from 'ziggy-js';
  import { roundedPoints } from '../helpers';

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
  interface RecipeFood {
    id: number;
    quantity: number;
    unit: string;
    food: {
      id: number;
      name: string;
      color: string;
      points: number;
      quantity: number;
    };
  }

  interface Recipe {
    id: number;
    name: string;
    points: number;
    quantity: number;
    unit: string;
    foods: RecipeFood[];
  }

  const props = defineProps<{
    foods: Paginate<Recipe>;
  }>();

  const page = usePage();
  const search = ref('');
  const urlParams = new URLSearchParams(window.location.search);
  const dayActiveParam = urlParams.get('dayActive') || new Date().toISOString();
  const dayActive = ref(new Date(dayActiveParam));
  const time = ref(urlParams.get('time') || 'Desayuno');

  const getPointsUrl = (recipeId: number) => {
    const dayActiveDate = dayActive.value instanceof Date && !isNaN(dayActive.value.getTime())
      ? dayActive.value
      : new Date();
    return ziggyRoute('points.show', {
      food: recipeId,
      time: time.value || 'Desayuno',
      dayActive: dayActiveDate.toISOString(),
      recipe: true,
    });
  };

  watchDebounced(
    search,
    () => {
      router.get(page.url, { q: search.value }, { preserveState: true });
    },
    { debounce: 300 }
  );

  const deleteRecipe = (recipe: Recipe) => {
    router.delete(ziggyRoute('recipes.destroy', { id: recipe.id }));
  };

  const editRecipe = (recipe: Recipe) => {
    router.get(ziggyRoute('recipes.new', { id: recipe.id }));
  };
</script>
<template>
  <Head title="Tus recetas - ViveTuLinea" />
  <NavBar />

  <div class="flex flex-col content-center items-center h-full">
    <div class="px-6 py-4 sm:py-32 lg:px-8">
      <div class="mx-auto max-w-2xl text-center">
        <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
          Tus recetas
        </h2>
        <p class="text-base mt-2 font-semibold leading-7 text-primary">Busca tus recetas</p>
      </div>
    </div>

    <div class="flex gap-4 items-center justify-center mt-4 w-full px-4">
      <div class="fieldset max-w-xs flex-1">
        <input
          v-model="search"
          type="text"
          placeholder="Buscar receta"
          class="input"
        />
      </div>
      <Link :href="ziggyRoute('recipes.new')" class="btn btn-primary">
        + Crear receta
      </Link>
    </div>

    <div class="mt-4 w-full p-4">
      <ul>
        <li v-for="food in foods.data" :key="food.id" class="collapse collapse-arrow bg-base-200">
          <input type="checkbox" class="peer w-full h-full" />
          <div
            class="collapse-title bg-primary-content text-primary peer-checked:bg-primary-content peer-checked:text-primary flex justify-between items-center"
          >
            <div class="flex justify-between w-full items-center">
              <div>
                <div class="text-sm">
                  {{ food.name }}
                </div>
                <div class="text-xs text-neutral">
                  {{ roundedPoints(food.points / food.quantity) }} puntos por {{ food.unit }}
                </div>
              </div>
            </div>
            <div class="flex">
              <Link
                :href="getPointsUrl(food.id)"
                class="text-primary-content bg-primary rounded-full h-3 w-3 flex items-center justify-center p-3 z-10"
              >
                +
              </Link>
            </div>
          </div>
          <div
            class="collapse-content bg-primary-content text-primary peer-checked:bg-primary-content peer-checked:text-primary"
          >
            <ul class="ml-4 text-neutral">
              <li v-for="f in food.foods" :key="f.id" class="border-b py-2">
                <div class="flex items-center mt-2">
                  <div
                    class="w-2 h-2 rounded-full mr-2"
                    :class="{
                      'bg-blue-500': f.food.color === 'blue',
                      'bg-green-500': f.food.color === 'green',
                      'bg-yellow-500': f.food.color === 'yellow',
                      'bg-red-500': f.food.color === 'red',
                      'bg-black': f.food.color === 'black',
                    }"
                  />
                  <div class="flex justify-between w-full items-center">
                    <div>
                      <div class="text-sm">
                        {{ f.food.name }}
                      </div>
                      <div class="text-xs">
                        {{ f.quantity }} {{ f.unit }} -
                        {{ roundedPoints((f.quantity * f.food.points) / f.food.quantity) }} puntos
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
            <!--                        <div class="text-neutral text-right py-2">Total: <span class="font-bold">{{totalPointsPerMeal[time]}} puntos</span> </div>-->
            <button class="text-neutral text-right py-2 mr-2" @click="editRecipe(food)">
              <span class="btn btn-xs btn-secondary btn-outline">Editar receta</span>
            </button>
            <button class="text-neutral text-right py-2" @click="deleteRecipe(food)">
              <span class="btn btn-xs btn-error btn-outline">Borrar receta</span>
            </button>
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
