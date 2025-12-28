<script setup lang="ts">
  import { VisXYContainer, VisLine, VisTooltip, VisScatter, VisAxis } from '@unovis/vue';
  import { Line, Scatter, StackedBar } from '@unovis/ts';
  import { computed, ref } from 'vue';
  import NavBar from '@/Components/Layout/NavBar.vue';
  import { Head, Link, router } from '@inertiajs/vue3';
  import SvgIcon from '@/Components/SvgIcon.vue';

  const props = defineProps<{
    weights: {
      date: string;
      value: number;
      index: number;
      id: number;
    }[];
    targetWeight: number;
    selectedRange: string;
  }>();

  const x = (d) => d.index;
  const y = (d) => d.value;

  const targetX = (d) => d.index;
  const targetY = () => props.targetWeight;

  const triggers = {
    [Scatter.selectors.point]: (d) => `Fecha: ${d.date}<br/>Peso: ${d.value} kg`,
  };

  const currentWeight = computed(() => props.weights[props.weights.length - 1]);

  const yDomain = computed(() => {
    if (props.weights.length === 0) return [40, 120];
    const minWeight = Math.min(...props.weights.map(w => w.value), props.targetWeight);
    const maxWeight = Math.max(...props.weights.map(w => w.value), props.targetWeight);

    if(!minWeight || !maxWeight) return [50, 110];
    return [minWeight - 2, maxWeight + 2];
  });

  const showDeleteModal = ref(false);
  const weightToDelete = ref<number | null>(null);

  const openDeleteModal = (id: number) => {
    weightToDelete.value = id;
    showDeleteModal.value = true;
  };

  const closeDeleteModal = () => {
    showDeleteModal.value = false;
    weightToDelete.value = null;
  };

  const confirmDelete = () => {
    if (weightToDelete.value) {
      router.delete(route('weight.destroy', { weight: weightToDelete.value }));
      closeDeleteModal();
    }
  };

  const handleRangeChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    router.get(route('weights.show'), { range: target.value }, {
      preserveState: true,
      preserveScroll: true,
    });
  };
</script>

<template>
  <div>
    <Head title="Tu progreso" />
    <NavBar />
    <div class="flex items-center justify-between">
        <div v-if="currentWeight" class="mx-auto max-w-2xl text-center">
      <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
        {{ currentWeight.value }} <span class="text-sm">kg</span>
      </h2>
      <p class="text-sm font-semibold leading-7 text-primary">
        {{ currentWeight.date }}
      </p>
    </div>
    <div v-else class="mx-auto max-w-2xl text-center">
      <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Sin datos</h2>
      <p class="text-sm font-semibold leading-7 text-primary">Registra tu peso</p>
    </div>

    <div v-if="targetWeight" class="mx-auto max-w-2xl text-center">
      <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
        {{ targetWeight }} <span class="text-sm">kg</span>
      </h2>
      <p class="text-sm font-semibold leading-7 text-secondary">
        Peso objetivo
      </p>
    </div>

    <div class="flex justify-end px-4 mb-4 self-end">
      <select 
        class="select" 
        :value="selectedRange"
        @change="handleRangeChange"
      >
        <option value="1month">Último mes</option>
        <option value="3months">Últimos 3 meses</option>
        <option value="6months">Últimos 6 meses</option>
        <option value="1year">Último año</option>
      </select>
    </div>
    </div>
   
    
    <VisXYContainer :data="weights" :prevent-empty-domain="true" :y-domain="yDomain">
      <VisAxis type="y" />
      <VisLine color="#c11387" :line-width="3" :x="x" :y="y" />
      <VisLine v-if="targetWeight" color="#9ec151" :line-width="1" :x="targetX" :y="targetY" />
      <VisTooltip :triggers="triggers" />
      <VisScatter color="#c11387" :x="x" :y="y" />
    </VisXYContainer>

    <div class="mt-8">
      <div class="overflow-x-auto max-h-[320px] overflow-y-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Peso</th>
              <th>
                <div class="flex justify-end" v-if="weights.length > 0">
                    <Link :href="route('weight.show')" class="btn btn-primary btn-sm">
                    Añadir peso
                    </Link>
                </div>
               </th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="weights.length === 0">
              <td colspan="3" class="text-center py-8">
                <div class="flex flex-col items-center justify-center">
                  <SvgIcon name="warning" class="w-12 h-12 text-gray-400 mb-4" />
                  <p class="text-gray-500 text-lg">No hay registros de peso</p>
                  <Link :href="route('weight.show')" class="btn btn-primary btn-sm mt-4">
                    Registrar mi primer peso
                  </Link>
                </div>
              </td>
            </tr>
            <tr v-else v-for="weight in weights" :key="weight.id">
              <td>{{ weight.date }}</td>
              <td>{{ weight.value }} kg</td>
              <td>
                <div class="flex gap-2 justify-end">
                  <Link
                    :href="route('weight.show', { weight: weight.id })"
                    class="btn btn-sm btn-primary"
                  >
                    <SvgIcon name="edit-pencil" class="w-4 h-4" />
                  </Link>
                  <button
                    @click="openDeleteModal(weight.id)"
                    class="btn btn-sm btn-error"
                  >
                    <SvgIcon name="trash" class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de confirmación -->
    <dialog :class="{'modal': true, 'modal-open': showDeleteModal}">
      <div class="modal-box">
        <h3 class="font-bold text-lg">Confirmar eliminación</h3>
        <p class="py-4">¿Estás seguro de que quieres eliminar este registro de peso? Esta acción no se puede deshacer.</p>
        <div class="modal-action">
          <button class="btn" @click="closeDeleteModal">Cancelar</button>
          <button class="btn btn-error" @click="confirmDelete">Eliminar</button>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop">
        <button @click="closeDeleteModal">Cerrar</button>
      </form>
    </dialog>
  </div>
</template>