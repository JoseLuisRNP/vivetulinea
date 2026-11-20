<script setup lang="ts">
  import { computed, ref } from 'vue';
  import { Head, useForm } from '@inertiajs/vue3';
  import NavBar from '@/Components/Layout/NavBar.vue';
  import ziggyRoute from 'ziggy-js';
  import { times } from '@/data';
  import { roundedPoints } from '@/helpers';

  const props = defineProps<{
    backTo: string;
    dayActive: string;
    errors: any;
  }>();

  const calories = ref(0);
  const fats = ref(0);
  const sugars = ref(0);
  const proteins = ref(0);

  const form = useForm({
    name: '',
    color: 'yellow',
    time_of_day: 'Desayuno',
    quantity: 0,
    points: 0 as number | string,
  });

  const points = computed(() => {
    if (!calories.value || !form.quantity) return '-';

    const result =
      (calories.value * 0.0305 +
        fats.value * 0.275 +
        sugars.value * 0.12 -
        proteins.value * 0.098) *
      (form.quantity / 100);
    return roundedPoints(result);
  });

  const registerPoints = () => {
    if (!calories.value || !form.quantity) return;

    form.points = points.value;
    form.transform((data) => ({
      ...data,
      consumed_at: props.dayActive || new Date().toISOString(),
    }));

    form.post(ziggyRoute('points.store'));
  };
</script>
<template>
  <div>
    <Head title="Calculadora" />
    <NavBar />
    <div class="px-6 py-4 sm:py-32 lg:px-8">
      <div class="mx-auto max-w-2xl text-center">
        <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
          Calculadora
        </h2>
        <p class="text-base font-semibold leading-7 text-primary">
          Introducir información nutricional para 100g/ml
        </p>
      </div>
    </div>
    <div class="mx-8 my-2 md:flex md:justify-between">
      <div class="fieldset max-w-xs">
        <label class="label">Calorías</label>
        <input
          v-model.number="calories"
          type="number"
          placeholder="0"
          class="input focus:border-primary"
          @focus="$event.target.select()"
        />
      </div>
      <div class="fieldset max-w-xs">
        <label class="label">Grasas saturadas</label>
        <input
          v-model.number="fats"
          type="number"
          class="input focus:border-primary"
          @focus="$event.target.select()"
        />
      </div>
      <div class="fieldset max-w-xs">
        <label class="label">Azúcares</label>
        <input
          v-model.number="sugars"
          type="number"
          class="input focus:border-primary"
          @focus="$event.target.select()"
        />
      </div>
      <div class="fieldset max-w-xs">
        <label class="label">Proteínas</label>
        <input
          v-model.number="proteins"
          type="number"
          class="input focus:border-primary"
          @focus="$event.target.select()"
        />
      </div>
      <div class="fieldset max-w-xs">
        <label class="label">Cantidad consumida</label>
        <input
          v-model.number="form.quantity"
          type="number"
          class="input focus:border-primary"
          @focus="$event.target.select()"
        />
      </div>
    </div>
    <div class="flex flex-col justify-center my-8">
      <div class="avatar-placeholder">
        <div class="bg-primary text-primary-content rounded-full size-16 m-auto">
          <span class="text-xl">{{ points }}</span>
        </div>
      </div>
      <div class="mx-8 my-2 md:flex md:justify-between">
        <div class="fieldset max-w-xs">
          <label class="label">Nombre</label>
          <input
            v-model="form.name"
            type="text"
            placeholder="Alimento"
            class="input focus:border-primary"
            :class="form.errors.name ? 'border-error' : ''"
            @focus="$event.target.select()"
          />
          <span v-if="form.errors.name" class="text-xs text-error">{{ form.errors.name }}</span>
        </div>
        <div class="fieldset max-w-xs">
          <label class="label">Color</label>
          <select v-model="form.color" class="select">
            <option value="green">🟢 Hidratos de carbono</option>
            <option value="blue">🔵 Proteínas</option>
            <option value="red">🔴 Grasas</option>
            <option value="yellow">🟡 Sin identificar</option>
          </select>
        </div>
        <div class="fieldset max-w-xs">
          <label class="label">Momento</label>
          <select v-model="form.time_of_day" class="select">
            <option v-for="time in times" :key="time" :value="time">
              {{ time }}
            </option>
          </select>
        </div>
        <button
          class="btn btn-primary mt-4 w-2/4 m-auto w-full"
          :disabled="!points || !form.quantity || form.processing"
          @click="registerPoints"
        >
          Añadir puntos al diario
        </button>
      </div>
    </div>
  </div>
</template>
