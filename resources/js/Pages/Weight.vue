<script setup lang="ts">
  import { Head, router } from '@inertiajs/vue3';
  import NavBar from '@/Components/Layout/NavBar.vue';
  import ziggyRoute from 'ziggy-js';
  import { computed, onMounted, ref } from 'vue';

  const props = defineProps<{
    weight?: {
      id: number;
      value: number;
      date: string;
    } | null;
  }>();

  const weight = ref(props.weight?.value || 0);
  const date = ref(props.weight ? new Date(props.weight.date) : new Date());
  const showDatePicker = ref(false);

  // Format date helper
  const formattedDate = computed(() => {
    return date.value.toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    });
  });

  const handleSubmit = () => {
    const data = {
      weight: weight.value,
      date: date.value,
    };

    if (props.weight) {
      router.patch(ziggyRoute('weight.update', { weight: props.weight.id }), data);
    } else {
      router.post(ziggyRoute('weight.store'), data);
    }
  };
</script>
<template>
  <div>
    <Head :title="props.weight ? 'Editar peso' : 'Registrar peso'" />
    <NavBar />
    <div class="px-6 py-4 sm:py-32 lg:px-8">
      <div class="mx-auto max-w-2xl text-center">
        <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Peso</h2>
        <p class="text-base font-semibold leading-7 text-primary">
          {{ props.weight ? 'Edita tu peso' : 'Registra tu nuevo peso' }}
        </p>
      </div>
    </div>
    <div class="flex flex-col justify-center my-8 text-xl">
      <div class="w-full justify-center items-center flex flex-col">
        <div class="form-control w-full max-w-xs">
          <label class="label">
            <span class="label-text text-lg">Peso en kg</span>
          </label>
          <input
            v-model="weight"
            type="number"
            placeholder="Peso en kg"
            class="input input-bordered w-full max-w-xs focus:border-primary"
            @focus="$event.target.select()"
          />
        </div>
        <div class="form-control w-full max-w-xs relative">
          <label class="label">
            <span class="label-text text-lg">Fecha</span>
          </label>
          <button
            type="button"
            class="input input-bordered w-full max-w-xs focus:border-primary text-left"
            @click="showDatePicker = !showDatePicker"
          >
            {{ formattedDate }}
          </button>

          <div v-show="showDatePicker" class="absolute top-full left-0 z-10 mt-1">
            <VDatePicker
              v-model="date"
              mode="date"
              locale="es"
              color="vivetulinea"
              @update:model-value="showDatePicker = false"
            />
          </div>
        </div>

        <button
          :disabled="!date || !weight"
          :class="!date || !weight ? 'btn-disabled' : ''"
          class="btn btn-primary mt-8 w-2/4 m-auto"
          @click="handleSubmit"
        >
          {{ props.weight ? 'Actualizar peso' : 'Registrar peso' }}
        </button>
      </div>
    </div>
  </div>
</template>
<style>
  .vc-vivetulinea {
    --vc-accent-50: #c11387;
    --vc-accent-100: #c11387;
    --vc-accent-200: #c11387;
    --vc-accent-300: #c11387;
    --vc-accent-400: #c11387;
    --vc-accent-500: #c11387;
    --vc-accent-600: #c11387;
    --vc-accent-700: #c11387;
    --vc-accent-800: #c11387;
    --vc-accent-900: #c11387;
  }
</style>
