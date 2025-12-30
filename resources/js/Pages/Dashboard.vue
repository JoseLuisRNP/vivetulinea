<script setup lang="ts">
  import { Head, router, usePage } from '@inertiajs/vue3';
  import NavBar from '@/Components/Layout/NavBar.vue';
  import { Link } from '@inertiajs/vue3';
  import ziggyRoute from 'ziggy-js';
  import { computed, ref, watch } from 'vue';
  import { times } from '@/data';
  import { watchDebounced, onClickOutside } from '@vueuse/core';
  import { useToast } from 'vue-toastification';
  import { roundedPoints } from '../helpers';
  import SvgIcon from '@/Components/SvgIcon.vue';

  interface Meal {
    id: number;
    name: string;
    points: number;
    quantity: number;
    consumed_at: string;
    oil_no_count: boolean;
    special_no_count: boolean;
  }

  interface Meals {
    [key: string]: Meal[];
  }

  interface Guideline {
    id: number;
    water: string;
    fruit: number;
    vegetable: number;
  }

  const props = defineProps<{
    meals: Meals;
    remainingPoints: number;
    weekPointsConsumedThisDay: number;
    weekRemainingPoints: number;
    pointsByColor: Record<string, number>;
    resultSearch: any[];
    noCountDay: boolean;
    guideline: Guideline;
  }>();

  const oneDay = 24 * 60 * 60 * 1000;
  const dayActive = ref(new Date());

  const nextDay = () => {
    dayActive.value = new Date(dayActive.value.getTime() + oneDay);
  };

  const prevDay = () => {
    dayActive.value = new Date(dayActive.value.getTime() - oneDay);
  };

  watch(dayActive, () => {
    // Get points of this day
    router.reload({ data: { dayActive: dayActive.value.toISOString() } });
  });

  const getTotalPoints = (array: Meal[]) =>
    array ? array.reduce((acc, meal) => acc + meal.points, 0) : 0;

  const oilNoCountQuantity = computed(() => {
    if (!props.noCountDay) return 0;

    return Math.min(
      2,
      Object.values(props.meals).reduce((acc, meal) => {
        const mealsOil = meal.filter((m) => m.oil_no_count);
        return acc + mealsOil.reduce((acc, meal) => acc + meal.quantity, 0);
      }, 0)
    );
  });

  const specialNoCountQuantity = computed(() => {
    if (!props.noCountDay) return 0;

    return Math.min(
      3,
      Object.values(props.meals).reduce((acc, meal) => {
        const mealsSpecial = meal.filter((m) => m.special_no_count);
        return acc + mealsSpecial.reduce((acc, meal) => acc + meal.quantity, 0);
      }, 0)
    );
  });

  const totalPointsPerMeal = computed(() => {
    const result = {} as Record<string, number>;

    times.forEach((time) => {
      result[time] = getTotalPoints(props.meals[time]);
    });

    return result;
  });

  const search = ref('');

  watchDebounced(
    search,
    () => {
      router.reload({ preserveState: true, only: ['resultSearch'], data: { q: search.value } });
    },
    { debounce: 500 }
  );

  const toast = useToast();

  const startNoCountDay = () => {
    if (!Array.isArray(props.meals) || (Array.isArray(props.meals) && props.meals.length)) {
      toast.error('No puedes iniciar el día de no contar si ya has registrado alimentos');
      return;
    }
    router.post(ziggyRoute('points.no-count'), {
      date: dayActive.value.toISOString(),
    });
  };

  const cancelNoCountDay = () => {
    if (!Array.isArray(props.meals) || (Array.isArray(props.meals) && props.meals.length)) {
      toast.error('No puedes cancelar el día de no contar si ya has registrado alimentos');
      return;
    }
    router.post(ziggyRoute('points.cancel-no-count'), {
      date: dayActive.value.toISOString(),
    });
  };
  const openResults = ref(false);

  watch(search, () => {
    openResults.value = true;
  });
  const resultsWrapper = ref();

  onClickOutside(resultsWrapper, () => {
    if (!openResults.value) return;
    openResults.value = false;
  });

  const page = usePage();

  const user = computed(() => page.props.auth.user);

  const updateGuideline = (property) => {
    props.guideline[property] += property === 'sport' ? 30 : 1;
    router.post(ziggyRoute('guidelines.store', { id: props.guideline.id }), { ...props.guideline });
  };

  const deleteMeal = (meal) => {
    router.delete(ziggyRoute('points.destroy', { id: meal.id }));
  };
</script>

<template>
  <div>
    <Head title="Dashboard" />

    <NavBar>
      <Link
        :href="ziggyRoute('recipes.new')"
        class="btn btn-ghost flex items-end text-primary -mr-2"
      >
        <SvgIcon name="recipe-book" class="w-6 h-6" />
      </Link>
      <Link
        :href="
          ziggyRoute('calculator', {
            dayActive: dayActive.toISOString(),
          })
        "
        class="btn btn-ghost flex items-end text-primary -mr-2"
      >
        <SvgIcon name="calculator" class="w-6 h-6" />
      </Link>
    </NavBar>

    <div
      class="bg-primary flex flex-col items-center w-full p-4 text-primary-content"
      :class="{
        'bg-primary': !noCountDay,
        'bg-secondary': noCountDay,
      }"
    >
      <div class="relative m-2 rounded-md shadow-sm w-full">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
          <SvgIcon name="search" class="w-5 h-5" />
        </div>
        <input
          id="text"
          v-model="search"
          type="text"
          name="text"
          class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-primary placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
          placeholder="Buscar alimento"
        />
        <div
          v-show="openResults"
          ref="resultsWrapper"
          class="absolute w-full bg-primary-content text-neutral max-h-48 overflow-auto"
        >
          <Link
            v-for="result in resultSearch"
            :key="result.id"
            :href="
              ziggyRoute('points.show', {
                food: result.id,
                dayActive,
                noCountDay,
                special: specialNoCountQuantity,
                oil: oilNoCountQuantity,
              })
            "
            class="hover:bg-primary hover:text-primary-content flex items-center p-1"
            :class="{
              'bg-pink-100': noCountDay && result.special_no_count,
              'bg-green-100': noCountDay && result.oil_no_count,
            }"
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
            <div :class="noCountDay && result.no_count ? 'font-bold' : ''">
              {{ result.name }}
            </div>
            <div class="ml-4 text-xs text-gray-400">
              {{ result.points }} puntos / {{ result.quantity }} {{ result.unit.toLowerCase() }}
            </div>
          </Link>
        </div>
      </div>

      <div class="flex justify-between w-full py-4" :class="{ 'items-center': noCountDay }">
        <template v-if="!noCountDay">
          <div class="flex flex-col items-center w-1/3">
            <div class="text-4xl">
              {{ remainingPoints }}
            </div>
            <div class="text-xs">diarios</div>
            <div class="text-xs">restantes</div>
          </div>
        </template>

        <button
          class="border border-primary-content rounded-md flex flex-col items-center justify-center p-2 w-1/3"
          :class="noCountDay ? 'bg-secondary-focus' : 'bg-primary-focus'"
          @click="noCountDay ? cancelNoCountDay() : startNoCountDay()"
        >
          <SvgIcon name="no-count-day" class="h-10 w-10 fill-current" />
          <span class="text-xs">{{
            noCountDay ? 'Cancelar día de no contar' : 'Iniciar día de no contar'
          }}</span>
        </button>
        <div class="flex flex-col items-center w-1/3">
          <div class="relative text-4xl">
            <div
v-if="weekPointsConsumedThisDay"
class="text-xs absolute -top-2 -right-4 rounded-full w-5 h-5 flex items-center justify-center"
            :class="!noCountDay ? 'bg-secondary-focus' : 'bg-primary-focus'">
{{ weekPointsConsumedThisDay }}
            </div>
            {{ weekRemainingPoints }}
          </div>
          <div class="text-xs">extras</div>
          <div class="text-xs">semanales</div>
        </div>
      </div>
      <div class="m-4 flex justify-evenly w-full text-xs" :class="noCountDay ? 'opacity-0' : ''">
        <div
          class="flex items-center px-1 rounded-full"
          :class="
            user.sugars <= pointsByColor['green'] ? 'bg-primary-content text-neutral' : 'bg-primary'
          "
        >
          <div class="w-2 h-2 bg-green-500 rounded-full mr-2" />
          <div>{{ roundedPoints(pointsByColor['green'] || 0) }} puntos</div>
        </div>
        <div
          class="flex items-center px-1 rounded-full"
          :class="
            user.proteins <= pointsByColor['blue']
              ? 'bg-primary-content text-neutral'
              : 'bg-primary'
          "
        >
          <div class="w-2 h-2 bg-blue-500 rounded-full mr-2" />
          <div>{{ roundedPoints(pointsByColor['blue'] || 0) }} puntos</div>
        </div>
        <div
          class="flex items-center px-1 rounded-full"
          :class="
            user.fats <= pointsByColor['red'] ? 'bg-primary-content text-neutral' : 'bg-primary'
          "
        >
          <div class="w-2 h-2 bg-red-500 rounded-full mr-2" />
          <div>{{ roundedPoints(pointsByColor['red'] || 0) }} puntos</div>
        </div>
        <div class="flex items-center">
          <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2" />
          <div>{{ roundedPoints(pointsByColor['yellow'] || 0) }} puntos</div>
        </div>
      </div>
      <div @click.stop>
        <span class="p-2" @click="prevDay">&lt;</span>
        <span class="p-2"
          >{{ dayActive.getDate() }}/{{ dayActive.getMonth() + 1 }}/{{
            dayActive.getFullYear()
          }}</span
        >
        <span class="p-2" @click="nextDay">&gt;</span>
      </div>
      <div class="m-4 flex justify-evenly w-full text-xl">
        <div
          class="flex rounded-lg"
          :class="
            guideline.water >= 8
              ? 'bg-primary-content text-neutral'
              : noCountDay
                ? 'bg-secondary'
                : 'bg-primary'
          "
          @click="updateGuideline('water')"
        >
          <span class="p-2 flex flex-col justify-center items-center text-base">
            <SvgIcon name="water" class="fill-current h-6 w-6" />
            <span>{{ guideline.water }}</span>
          </span>
        </div>
        <div
          class="flex rounded-lg"
          :class="
            guideline.fruit >= 2
              ? 'bg-primary-content text-neutral'
              : noCountDay
                ? 'bg-secondary'
                : 'bg-primary'
          "
          @click="updateGuideline('fruit')"
        >
          <span class="p-2 flex flex-col justify-center items-center text-base">
            <SvgIcon name="fruit" class="h-6 w-6" />
            <span>{{ guideline.fruit }}</span>
          </span>
        </div>
        <div
          class="flex rounded-lg"
          :class="
            guideline.vegetable >= 3
              ? 'bg-primary-content text-neutral'
              : noCountDay
                ? 'bg-secondary'
                : 'bg-primary'
          "
          @click="updateGuideline('vegetable')"
        >
          <span class="p-2 flex flex-col justify-center items-center text-base">
            <SvgIcon name="vegetable" class="h-6 w-6" />
            <span>{{ guideline.vegetable }}</span>
          </span>
        </div>
        <div
          class="flex rounded-lg"
          :class="
            guideline.sport >= 30
              ? 'bg-primary-content text-neutral'
              : noCountDay
                ? 'bg-secondary'
                : 'bg-primary'
          "
          @click="updateGuideline('sport')"
        >
          <span class="p-2 flex flex-col justify-center items-center text-base">
            <SvgIcon name="sport" class="h-6 w-6" />
            <span>{{ guideline.sport }}</span>
          </span>
        </div>
      </div>
      <Link v-if="noCountDay" :href="route('food.no-count')" class="border p-2 rounded-lg">
        Listado de alimentos
      </Link>
    </div>
    <div>
      <div v-for="time in times" :key="time" class="collapse collapse-arrow bg-base-200">
        <input type="checkbox" checked class="peer w-full" />
        <div
          class="collapse-title bg-primary-content text-primary peer-checked:bg-primary-content peer-checked:text-primary flex justify-between items-center"
        >
          {{ time }}
          <div class="flex">
            <Link
              :href="ziggyRoute('recipes.index', { time, dayActive })"
              class="text-primary-content rounded-full h-3 w-3 flex items-center justify-center p-3 z-10 mr-2"
            >
              <div class="flex justify-between items-center">
                <svg
                  class="w-6 h-6"
                  viewBox="0 0 64 64"
                  xmlns="http://www.w3.org/2000/svg"
                  version="1.1"
                  xmlns:xlink="http://www.w3.org/1999/xlink"
                  xmlns:svgjs="http://svgjs.dev/svgjs"
                >
                  <g transform="matrix(1,0,0,1,0,0)">
                    <g id="_x35_0_Cup" />
                    <g id="_x34_9_Fork" />
                    <g id="_x34_8_Spoon" />
                    <g id="_x34_7_Steak_Hammer" />
                    <g id="_x34_6_Pot_Holder" />
                    <g id="_x34_5_Wok" />
                    <g id="_x34_4_Whisk" />
                    <g id="_x34_3_Pitcher" />
                    <g id="_x34_2_Plater" />
                    <g id="_x34_1_Tongs" />
                    <g id="_x34_0_Pressure_Cooker" />
                    <g id="_x33_9_Glass" />
                    <g id="_x33_8_Funnel" />
                    <g id="_x33_7_Measuring_Cup" />
                    <g id="_x33_6_Stand_Mixer" />
                    <g id="_x33_5_Water_Heater" />
                    <g id="_x33_4_Drainer" />
                    <g id="_x33_3_Colender" />
                    <g id="_x33_2_Bowl" />
                    <g id="_x33_1_Thermo" />
                    <g id="_x33_0_coffee_grinder" />
                    <g id="_x32_9_cup" />
                    <g id="_x32_8_Knife" />
                    <g id="_x32_7_cheese_grater" />
                    <g id="_x32_6_grill" />
                    <g id="_x32_5_chopping_board" />
                    <g id="_x32_4_Cleaver" />
                    <g id="_x32_3_Apron" />
                    <g id="_x32_2_Electric_cooker" />
                    <g id="_x32_1_juicer" />
                    <g id="_x32_0_Toaster" />
                    <g id="_x31_9_kettle" />
                    <g id="_x31_8_Rolling_pin" />
                    <g id="_x31_7_spatula" />
                    <g id="_x31_6_chef_hat" />
                    <g id="_x31_5_food_scale" />
                    <g id="_x31_4_jug" />
                    <g id="_x31_3_coffe_maker" />
                    <g id="_x31_2_refrigerator" />
                    <g id="_x31_1_food_mixer" />
                    <g id="_x31_0_Slotted_Spatula" />
                    <g id="_x30_9_Corkscrew" />
                    <g id="_x30_8_blender" />
                    <g id="_x30_7_gas_stove" />
                    <g id="_x30_6_recipe_book">
                      <g>
                        <path
                          d="m56 18c-1.104 0-2 .896-2 2v38c0 1.103-.897 2-2 2h-38c-1.103 0-2-.897-2-2v-4c1.104 0 2-.896 2-2s-.896-2-2-2h-1.995c-.002 0-.003 0-.005 0s-.003 0-.005 0h-1.995c-1.104 0-2 .896-2 2s.896 2 2 2v4c0 3.309 2.691 6 6 6h38c3.309 0 6-2.691 6-6v-38c0-1.104-.896-2-2-2z"
                          fill="#c11387ff"
                          data-original-color="#000000ff"
                          stroke="none"
                        />
                        <path
                          d="m52 0h-38c-3.309 0-6 2.691-6 6v4c-1.104 0-2 .896-2 2s.896 2 2 2h4c1.104 0 2-.896 2-2s-.896-2-2-2v-4c0-1.103.897-2 2-2h38c1.103 0 2 .897 2 2v2c0 1.104.896 2 2 2s2-.896 2-2v-2c0-3.309-2.691-6-6-6z"
                          fill="#c11387ff"
                          data-original-color="#000000ff"
                          stroke="none"
                        />
                        <path
                          d="m8 22h4c1.104 0 2-.896 2-2s-.896-2-2-2h-4c-1.104 0-2 .896-2 2s.896 2 2 2z"
                          fill="#c11387ff"
                          data-original-color="#000000ff"
                          stroke="none"
                        />
                        <path
                          d="m8 30h4c1.104 0 2-.896 2-2s-.896-2-2-2h-4c-1.104 0-2 .896-2 2s.896 2 2 2z"
                          fill="#c11387ff"
                          data-original-color="#000000ff"
                          stroke="none"
                        />
                        <path
                          d="m8 38h4c1.104 0 2-.896 2-2s-.896-2-2-2h-4c-1.104 0-2 .896-2 2s.896 2 2 2z"
                          fill="#c11387ff"
                          data-original-color="#000000ff"
                          stroke="none"
                        />
                        <path
                          d="m8 46h4c1.104 0 2-.896 2-2s-.896-2-2-2h-4c-1.104 0-2 .896-2 2s.896 2 2 2z"
                          fill="#c11387ff"
                          data-original-color="#000000ff"
                          stroke="none"
                        />
                        <path
                          d="m24 52c0 1.104.896 2 2 2h16c1.104 0 2-.896 2-2v-10.343c2.328-.825 4-3.05 4-5.657 0-3.393-2.865-6.137-6.252-5.995-.889-3.449-4.025-6.005-7.748-6.005s-6.859 2.556-7.748 6.005c-3.402-.138-6.252 2.602-6.252 5.995 0 2.607 1.672 4.832 4 5.657zm4-2v-2h12v2zm-2-16c.344 0 .683.098 1.009.291.646.383 1.454.372 2.09-.031.635-.402.991-1.127.921-1.875-.012-.128-.02-.256-.02-.385 0-2.206 1.794-4 4-4s4 1.794 4 4c0 .126-.008.25-.02.374-.074.75.279 1.478.915 1.883.635.404 1.444.419 2.094.035.327-.194.667-.292 1.011-.292 1.103 0 2 .897 2 2s-.897 2-2 2c-1.104 0-2 .896-2 2v4h-12v-4c0-1.104-.896-2-2-2-1.103 0-2-.897-2-2s.897-2 2-2z"
                          fill="#c11387ff"
                          data-original-color="#000000ff"
                          stroke="none"
                        />
                        <path
                          d="m50 18v-8c0-1.104-.896-2-2-2h-28c-1.104 0-2 .896-2 2v8c0 1.104.896 2 2 2h28c1.104 0 2-.896 2-2zm-4-2h-24v-4h24z"
                          fill="#c11387ff"
                          data-original-color="#000000ff"
                          stroke="none"
                        />
                        <path
                          d="m54.58 12.59c-.37.37-.58.89-.58 1.41 0 .53.21 1.04.59 1.41.37.38.88.59 1.41.59s1.04-.21 1.41-.59c.38-.37.59-.88.59-1.41 0-.52-.21-1.04-.59-1.41-.74-.75-2.08-.75-2.83 0z"
                          fill="#c11387ff"
                          data-original-color="#000000ff"
                          stroke="none"
                        />
                      </g>
                    </g>
                    <g id="_x30_5_oven" />
                    <g id="_x30_4_pot" />
                    <g id="_x30_3_pepper_mill" />
                    <g id="_x30_2_Peeler" />
                    <g id="_x30_1_frying_pan" />
                  </g>
                </svg>
              </div>
            </Link>
            <Link
              :href="ziggyRoute('points.show', { time, dayActive, noCountDay })"
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
            <li v-for="meal in meals[time]" :key="meal.id" class="border-b py-2">
              <div class="flex items-center">
                <div
                  class="w-2 h-2 rounded-full mr-2"
                  :class="{
                    'bg-blue-500': meal.color === 'blue',
                    'bg-green-500': meal.color === 'green',
                    'bg-yellow-500': meal.color === 'yellow' && !meal.recipe_id,
                    'bg-red-500': meal.color === 'red',
                    'bg-black': meal.color === 'black',
                    'bg-gradient-to-r from-blue-500 via-70% via-green-500 to-red-500':
                      meal.color === 'yellow' && meal.recipe_id,
                  }"
                />
                <div class="flex justify-between w-full items-center">
                  <div>
                    <div class="text-sm">
                      {{ meal.name }}
                    </div>
                    <div class="flex text-xs">
                      <div v-if="meal.quantity" class="text-xs mr-4">
                        {{ meal.quantity }}
                        <span v-if="meal.recipe_id">{{
                          meal.quantity === 1 ? 'ración' : 'raciones'
                        }}</span>
                      </div>
                      <div v-if="meal.recipe_id" class="flex items-center">
                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2" />
                        {{
                          roundedPoints((meal.recipe.sugars * meal.points) / meal.recipe.points)
                        }}
                        <span class="w-2 h-2 rounded-full bg-blue-500 mr-2 ml-2" />
                        {{
                          roundedPoints((meal.recipe.proteins * meal.points) / meal.recipe.points)
                        }}
                        <span class="w-2 h-2 rounded-full bg-red-500 mr-2 ml-2" />
                        {{ roundedPoints((meal.recipe.fats * meal.points) / meal.recipe.points) }}
                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2 ml-2" />
                        {{
                          roundedPoints(
                            (meal.recipe.empty_points * meal.points) / meal.recipe.points
                          )
                        }}
                      </div>
                    </div>
                  </div>
                  <div class="flex">
                    <div class="text-sm mr-2">{{ meal.points }} puntos</div>
                    <button class="btn btn-xs btn-error btn-outline" @click="deleteMeal(meal)">
                      Borrar
                    </button>
                  </div>
                </div>
              </div>
            </li>
          </ul>
          <div class="text-neutral text-right py-2">
            Total: <span class="font-bold">{{ totalPointsPerMeal[time] }} puntos</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
