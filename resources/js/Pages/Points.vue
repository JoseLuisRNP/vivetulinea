<script setup lang="ts">
  import { Head, router } from '@inertiajs/vue3';
  import NavBar from '@/Components/Layout/NavBar.vue';
  import ziggyRoute from 'ziggy-js';
  import { computed, ref } from 'vue';
  import { times } from '@/data';
  import { roundedPoints } from '@/helpers';

  const props = defineProps<{
    food: any;
  }>();

  const urlParams = new URLSearchParams(window.location.search);
  const dayActiveParam = urlParams.get('dayActive') || new Date().toISOString();
  const dayActive = ref(new Date(dayActiveParam));

  const name = ref(props.food?.name || '');
  const color = ref(props.food?.color || 'yellow');
  const timeOfDay = ref(urlParams.get('time') || 'Desayuno');
  const points = ref(0);
  const noCountDay = ref(!!parseInt(urlParams.get('noCountDay')));
  const oilCount = ref(parseInt(urlParams.get('oil')));
  const specialCount = ref(parseInt(urlParams.get('special')));
  const quantity = ref(
    noCountDay.value && props.food && (props.food.special_no_count || props.food.oil_no_count)
      ? 1
      : 0
  );
  const isRecipe = ref(urlParams.get('recipe'));

  const calculatedPoints = computed(() => {
    const isNoCountDay = noCountDay.value;
    const isNoCountFood = props.food.no_count;
    const isNoCountFoodSpecial = props.food.special_no_count || props.food.oil_no_count;
    let realQuantity = quantity.value;
    if (!props.food) return 0;
    if (isNoCountDay && isNoCountFoodSpecial) {
      const freeLeft = props.food.oil_no_count ? 2 - oilCount.value : 3 - specialCount.value;
      realQuantity = Math.max(0, quantity.value - freeLeft);
    }

    if (isNoCountDay && isNoCountFood) {
      realQuantity = 0;
    }

    const result = (realQuantity * props.food.points) / props.food.quantity;
    return roundedPoints(result);
  });

  const registerPoints = () => {
    const data = {
      quantity: quantity.value,
      points: props.food ? calculatedPoints.value : points.value,
      name: name.value,
      color: color.value,
      time_of_day: timeOfDay.value,
      consumed_at: dayActive.value.toISOString(),
      special_no_count: noCountDay.value && props.food && props.food.special_no_count,
      oil_no_count: noCountDay.value && props.food && props.food.oil_no_count,
      recipe_id: isRecipe.value ? props.food.id : null,
    };

    router.post(ziggyRoute('points.store'), data);
  };
</script>
<template>
  <div>
    <Head title="Añadir puntos" />
    <NavBar />
    <div class="px-6 py-4 sm:py-32 lg:px-8">
      <div class="mx-auto max-w-2xl text-center">
        <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Puntos</h2>
        <p class="text-base font-semibold leading-7 text-primary">
          {{ food ? 'Registra puntos del alimento' : 'Añade tus puntos manualmente' }}
        </p>
      </div>
    </div>
    <div class="flex flex-col justify-center my-8 text-xl">
      <div class="mx-8 my-2 md:flex md:justify-between">
        <div class="fieldset max-w-xs">
          <label class="label text-lg">Nombre</label>
          <input
            v-model="name"
            type="text"
            placeholder="Alimento"
            class="input focus:border-primary"
            :disabled="food"
            style="color: #3b424e"
            @focus="$event.target.select()"
          />
        </div>
        <div v-if="!food" class="fieldset max-w-xs">
          <label class="label text-lg">Puntos</label>
          <input
            v-model.number="points"
            type="number"
            placeholder="Alimento"
            class="input focus:border-primary"
            @focus="$event.target.select()"
          />
        </div>
        <div class="fieldset max-w-xs">
          <label class="label text-lg"
            >Cantidad <span v-if="food && food.unit"> en {{ food.unit }}</span></label
          >
          <input
            v-model.number="quantity"
            type="number"
            placeholder="0"
            class="input focus:border-primary"
            @focus="$event.target.select()"
          />
        </div>
        <div v-if="!food" class="fieldset max-w-xs">
          <label class="label text-lg">Color</label>
          <select v-model="color" class="select">
            <option value="green">🟢 Hidratos de carbono</option>
            <option value="blue">🔵 Proteínas</option>
            <option value="red">🔴 Grasas</option>
            <option value="yellow">🟡 Sin identificar</option>
          </select>
        </div>
        <div class="fieldset max-w-xs">
          <label class="label text-lg">Momento</label>
          <select v-model="timeOfDay" class="select">
            <option v-for="time in times" :key="time" :value="time">
              {{ time }}
            </option>
          </select>
        </div>
        <div v-if="food" class="avatar-placeholder mt-6">
          <div class="bg-primary text-primary-content rounded-full size-16 m-auto">
            <span class="text-xl">{{ calculatedPoints }}</span>
          </div>
        </div>
        <button class="btn btn-primary mt-4 w-2/4 m-auto w-full" @click="registerPoints">
          Añadir puntos al diario
        </button>
      </div>
    </div>
  </div>
</template>
