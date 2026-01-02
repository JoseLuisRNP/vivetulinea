<script setup lang="ts">
  import NavBar from '@/Components/Layout/NavBar.vue';
  import { Head, router, Link } from '@inertiajs/vue3';
  import { computed, ref, watch } from 'vue';
  import { onClickOutside, watchDebounced } from '@vueuse/core';
  import ziggyRoute from 'ziggy-js';
  import { roundedPoints } from '@/helpers';
  import SvgIcon from '@/Components/SvgIcon.vue';

  interface Food {
    id: number;
    name: string;
    color: string;
    points: number;
    quantity: number;
    unit: string;
    recipeQuantity?: number;
  }

  const props = defineProps<{
    resultSearch: any;
    recipe: any;
  }>();

  const search = ref('');

  watchDebounced(
    search,
    () => {
      router.reload({ preserveState: true, only: ['resultSearch'], data: { q: search.value } });
    },
    { debounce: 500 }
  );

  const name = ref(props.recipe?.name || '');
  const ration = ref(props.recipe?.quantity || 1);
  const foods = ref(
    props.recipe?.foods?.map((food) => {
      food.food.recipeQuantity = food.quantity;
      return food.food as Food;
    }) || []
  );
  const calculatedPointsPerFood = computed(() =>
    foods.value.map((food) => {
      const result = (food.recipeQuantity * food.points) / food.quantity;
      return roundedPoints(result);
    })
  );

  const totalRecipePoints = computed(() => {
    return calculatedPointsPerFood.value.reduce((acc, points) => {
      return acc + points;
    }, 0);
  });

  const totalRecipePointsByColor = computed(() => {
    return foods.value.reduce(
      (acc, food, currentIndex) => {
        acc[food.color] += calculatedPointsPerFood.value[currentIndex];
        return acc;
      },
      {
        green: 0,
        blue: 0,
        red: 0,
        yellow: 0,
        black: 0,
      }
    );
  });

  const addToRecipe = (food: Food) => {
    if (foods.value.some((f) => f.id === food.id)) return;

    food.recipeQuantity = food.quantity;
    foods.value.push(food);
    search.value = '';
  };

  const deleteFood = (food: Food) => {
    foods.value = foods.value.filter((f) => f.id !== food.id);
  };
  watch(search, () => {
    openResults.value = true;
  });
  const openResults = ref(false);
  const resultsWrapper = ref();
  onClickOutside(resultsWrapper, () => {
    if (!openResults.value) return;

    search.value = '';
    openResults.value = false;
  });

  const createRecipe = () => {
    const recipe = {
      id: props.recipe?.id,
      name: name.value,
      ration: ration.value,
      points: totalRecipePoints.value,
      proteins: totalRecipePointsByColor.value.blue,
      sugars: totalRecipePointsByColor.value.green,
      fats: totalRecipePointsByColor.value.red,
      empty_points: totalRecipePointsByColor.value.yellow,
      foods: foods.value.map((food, index) => {
        return {
          food_id: food.id,
          quantity: food.recipeQuantity,
          unit: food.unit,
          // points: calculatedPointsPerFood.value[index]
        };
      }),
    };

    router.post(ziggyRoute('recipes.create'), recipe);
  };
</script>

<template>
  <Head title="Tus recetas - ViveTuLinea" />
  <NavBar />
  <div class="px-6 py-4 sm:py-32 lg:px-8">
    <div class="mx-auto max-w-2xl text-center">
      <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Crear receta</h2>
      <p class="text-base font-semibold leading-7 text-primary">Añade los alimentos de tu receta</p>
    </div>
    <div class="mx-8 my-2 md:flex md:justify-between">
      <div class="fieldset max-w-xs">
        <label class="label">Nombre</label>
        <input
          v-model="name"
          type="text"
          placeholder="Tu receta"
          class="input focus:border-primary"
          @focus="$event.target.select()"
        />
      </div>
      <div class="fieldset max-w-xs">
        <label class="label">Número de raciones</label>
        <input
          v-model.number="ration"
          type="number"
          placeholder="1"
          class="input focus:border-primary"
          @focus="$event.target.select()"
        />
      </div>
      <div class="fieldset max-w-xs">
        <label class="label">Alimentos</label>
        <input
          v-model="search"
          placeholder="Buscar alimento"
          class="input focus:border-primary"
          @focus="$event.target.select()"
        />
        <div class="relative">
          <div
            v-show="resultSearch.length"
            ref="resultsWrapper"
            class="absolute dropdown dropdown-open w-full bg-primary-content text-neutral max-h-48 h-48 overflow-auto"
            :class="resultSearch.length ? 'h-48' : ''"
          >
            <div class="dropdown-content">
              <div
                v-for="result in resultSearch"
                :key="result.id"
                class="hover:bg-primary hover:text-primary-content flex items-center p-1"
                @click="addToRecipe(result)"
              >
                <div
                  class="w-2 h-2 rounded-full mr-2"
                  :class="{
                    'bg-blue-500': result.color === 'blue',
                    'bg-green-500': result.color === 'green',
                    'bg-yellow-500': result.color === 'yellow',
                    'bg-red-500': result.color === 'red',
                    'bg-black': result.color === 'black',
                  }"
                />
                <div>{{ result.name }}</div>
                <div class="ml-4 text-xs text-gray-400">
                  {{ result.points }} puntos / {{ result.quantity }} {{ result.unit.toLowerCase() }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <ul class="ml-4 text-neutral">
          <li v-for="(food, i) in foods" :key="food.id" class="border-b py-2">
            <div class="flex items-center">
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
              <div class="flex justify-between w-full items-center">
                <div class="max-w-[2rem]">
                  <div class="text-sm">
                    {{ food.name }}
                  </div>
                </div>
                <div class="flex items-center">
                  <div class="mr-4">
                    <input
                      v-model.number="food.recipeQuantity"
                      type="number"
                      placeholder="0"
                      class="input input-xs w-12 focus:border-primary"
                    />
                    <span class="text-xs ml-2">Cantidad</span>
                  </div>
                  <div class="text-sm mr-2">{{ calculatedPointsPerFood[i] }} pt</div>
                  <button class="btn btn-xs btn-error btn-outline" @click="deleteFood(food)">
                    <SvgIcon name="trash" class="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>
          </li>
        </ul>
        <div class="w-full flex justify-evenly mt-4">
          <span
            v-for="(quantity, color) in totalRecipePointsByColor"
            class="flex items-center"
            :class="color === 'black' ? 'hidden' : ''"
          >
            <span
              class="w-2 h-2 rounded-full mr-2"
              :class="{
                'bg-blue-500': color === 'blue',
                'bg-green-500': color === 'green',
                'bg-yellow-500': color === 'yellow',
                'bg-red-500': color === 'red',
                'bg-black': color === 'black',
              }"
            />
            {{ quantity }}
          </span>
        </div>
        <div class="text-neutral text-right py-2">
          Total: <span class="font-bold">{{ totalRecipePoints }} puntos</span>
        </div>
        <div class="text-neutral text-right py-2">
          Por ración:
          <span class="font-bold">{{ roundedPoints(totalRecipePoints / ration) }} puntos</span>
        </div>
      </div>
    </div>
    <div class="flex justify-center">
      <button
        class="btn btn-primary mt-4 w-2/4 m-auto"
        :disabled="!(name && ration && foods.length)"
        @click="createRecipe"
      >
        {{ props.recipe ? 'Actualizar' : 'Crear' }} receta
      </button>
    </div>
    <div class="flex justify-center">
      <Link
        :href="ziggyRoute(props.recipe ? 'recipes.index' : 'dashboard')"
        class="btn btn-primary btn-outline mt-4 w-2/4 m-auto"
      >
        Volver
      </Link>
    </div>
  </div>
</template>

<style scoped></style>
