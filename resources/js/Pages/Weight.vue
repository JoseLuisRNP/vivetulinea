<script setup lang="ts">
  import { Head, useForm } from '@inertiajs/vue3';
  import NavBar from '@/Components/Layout/NavBar.vue';
  import ziggyRoute from 'ziggy-js';
  import { computed, ref } from 'vue';

  const props = defineProps<{
    weight?: {
      id: number;
      value: number;
      date: string;
    } | null;
  }>();

  const showDatePicker = ref(false);

  const normalizeWeight = (raw: number | string): number | null => {
    const parsed = parseFloat(String(raw).replace(',', '.'));
    return Number.isFinite(parsed) ? parsed : null;
  };

  const form = useForm({
    weight: (props.weight?.value ?? '') as number | string,
    date: (props.weight ? new Date(props.weight.date) : new Date()) as Date,
  });

  form.transform((data) => ({
    weight: normalizeWeight(data.weight),
    date: data.date,
  }));

  const formattedDate = computed(() => {
    return form.date.toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    });
  });

  const handleSubmit = () => {
    if (normalizeWeight(form.weight) === null) return;

    if (props.weight) {
      form.patch(ziggyRoute('weight.update', { weight: props.weight.id }));
    } else {
      form.post(ziggyRoute('weight.store'));
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
        <div class="fieldset max-w-xs w-full">
          <label class="label text-lg">Peso en kg</label>
          <input
            v-model="form.weight"
            type="number"
            min="20"
            max="300"
            step="0.1"
            inputmode="decimal"
            placeholder="Peso en kg"
            class="input focus:border-primary"
            :class="{ 'border-error': form.errors.weight }"
            @focus="$event.target.select()"
          />
          <span v-if="form.errors.weight" class="text-error text-sm mt-1">{{
            form.errors.weight
          }}</span>
        </div>
        <div class="fieldset max-w-xs relative w-full">
          <label class="label text-lg">Fecha</label>
          <button
            type="button"
            class="input focus:border-primary text-left"
            @click="showDatePicker = !showDatePicker"
          >
            {{ formattedDate }}
          </button>

          <div v-show="showDatePicker" class="absolute top-full left-0 z-10 mt-1">
            <VDatePicker
              v-model="form.date"
              mode="date"
              locale="es"
              color="vivetulinea"
              @update:model-value="showDatePicker = false"
            />
          </div>
          <span v-if="form.errors.date" class="text-error text-sm mt-1">{{ form.errors.date }}</span>
        </div>

        <button
          :disabled="!form.date || !form.weight || form.processing"
          :class="!form.date || !form.weight || form.processing ? 'btn-disabled' : ''"
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
