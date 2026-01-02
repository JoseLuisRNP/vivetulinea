<script setup lang="ts">
  import NavBar from '@/Components/Layout/NavBar.vue';
  import ziggyRoute from 'ziggy-js';
  import { Link, usePage } from '@inertiajs/vue3';
  import Capital from '@/Components/Capital.vue';
  import Pautas from '@/Components/Pautas.vue';

  import { computed, ref, onMounted } from 'vue';
  import { Head } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import { useToast } from 'vue-toastification';
  import WeightToast from '@/Components/WeightToast.vue';
  import SvgIcon from '@/Components/SvgIcon.vue';

  const props = defineProps<{
    shouldShowToast: boolean;
  }>();

  const toast = useToast();

  const role = usePage().props.auth.user.role;

  if(props.shouldShowToast) {
    toast(WeightToast, {
      toastClassName: ['!bg-primary', '!text-white'],
      closeButtonClassName: '!text-white',
      timeout: false
    });
  }


</script>
<template>
  <Head title="Menu" />

  <div class="h-screen">
    <NavBar />
    <div class="w-full h-5/6 flex flex-col justify-between items-center">
      <div class="flex w-full h-full flex-col content-between justify-between">
        <Capital />

        <div class="flex w-full justify-between my-6">
          <div class="grid h-20 flex-grow card rounded-box place-items-center basis-1/2">
            <Link
              :href="ziggyRoute('dashboard')"
              class="text-primary flex flex-col justify-center items-center"
            >
              <SvgIcon name="diary" class="w-12 h-12" />
              <span class="text-xl">Diario</span>
            </Link>
          </div>
          <div class="divider divider-horizontal" />
          <div class="grid h-20 flex-grow card rounded-box place-items-center basis-1/2">
            <Link
              :href="ziggyRoute('calculator')"
              class="text-primary flex flex-col justify-center items-center"
            >
              <SvgIcon name="calculator" class="w-12 h-12" />
              <span class="text-xl">Calculadora</span>
            </Link>
          </div>
          <div class="divider divider-horizontal"/>
          <div class="grid h-20 flex-grow card rounded-box place-items-center basis-1/2">
            <Link
              :href="ziggyRoute('weights.show')"
              class="text-primary flex flex-col justify-center items-center"
            >
              <SvgIcon name="weight-scale" class="w-12 h-12" />
              <span class="text-xl">Peso</span>
            </Link>
          </div>
        </div>
        <div class="mt-4">
          <Pautas />
        </div>
      </div>
    </div>
  </div>
</template>
