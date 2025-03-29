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
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-12 h-12"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                />
              </svg>
              <span class="text-xl">Diario</span>
            </Link>
          </div>
          <div class="divider divider-horizontal" />
          <div class="grid h-20 flex-grow card rounded-box place-items-center basis-1/2">
            <Link
              :href="ziggyRoute('calculator')"
              class="text-primary flex flex-col justify-center items-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-12 h-12"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z"
                />
              </svg>
              <span class="text-xl">Calculadora</span>
            </Link>
          </div>
          <div class="divider divider-horizontal" v-if="['admin', 'dietician'].includes(role)"/>
          <div class="grid h-20 flex-grow card rounded-box place-items-center basis-1/2" v-if="['admin', 'dietician'].includes(role)">
            <Link
              :href="ziggyRoute('weights.show')"
              class="text-primary flex flex-col justify-center items-center"
            >
              <svg
                class="w-12 h-12"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                version="1.1"
                viewBox="0 0 512 512"
                fill="none"
                stroke="currentColor"
              >
                <g>
                  <line
                    style="
                      stroke-width: 20;
                      stroke-linecap: round;
                      stroke-linejoin: round;
                      stroke-miterlimit: 10;
                    "
                    x1="256"
                    y1="59.627"
                    x2="256"
                    y2="92.691"
                  />
                  <line
                    style="
                      stroke-width: 20;
                      stroke-linecap: round;
                      stroke-linejoin: round;
                      stroke-miterlimit: 10;
                    "
                    x1="167.829"
                    y1="83.253"
                    x2="184.361"
                    y2="111.887"
                  />
                  <line
                    style="
                      stroke-width: 20;
                      stroke-linecap: round;
                      stroke-linejoin: round;
                      stroke-miterlimit: 10;
                    "
                    x1="103.284"
                    y1="147.798"
                    x2="131.918"
                    y2="164.33"
                  />
                  <line
                    style="
                      stroke-width: 20;
                      stroke-linecap: round;
                      stroke-linejoin: round;
                      stroke-miterlimit: 10;
                    "
                    x1="408.716"
                    y1="147.798"
                    x2="380.082"
                    y2="164.33"
                  />
                  <line
                    style="
                      stroke-width: 20;
                      stroke-linecap: round;
                      stroke-linejoin: round;
                      stroke-miterlimit: 10;
                    "
                    x1="344.171"
                    y1="83.253"
                    x2="327.639"
                    y2="111.887"
                  />
                  <path
                    style="
                      stroke-width: 20;
                      stroke-linecap: round;
                      stroke-linejoin: round;
                      stroke-miterlimit: 10;
                    "
                    d="M400.08,502H111.92c-30.345,0-55.953-22.569-59.767-52.673L10.811,123.029C3.212,63.046,49.961,10,110.423,10h291.153c60.463,0,107.211,53.046,99.612,113.029l-41.341,326.299C456.034,479.431,430.425,502,400.08,502z"
                  />
                  <line
                    style="
                      stroke-width: 20;
                      stroke-linecap: round;
                      stroke-linejoin: round;
                      stroke-miterlimit: 10;
                    "
                    x1="256"
                    y1="180.575"
                    x2="291.275"
                    y2="140.229"
                  />
                  <path
                    style="
                      stroke-width: 20;
                      stroke-linecap: round;
                      stroke-linejoin: round;
                      stroke-miterlimit: 10;
                    "
                    d="M80.266,220.857c2.129-24.982,9.649-49.913,23.024-73.047c48.688-84.353,156.526-113.24,240.879-64.552c51.831,29.922,82.736,82.184,87.526,137.599H80.266z"
                  />
                  <path
                    style="
                      stroke-width: 20;
                      stroke-linecap: round;
                      stroke-linejoin: round;
                      stroke-miterlimit: 10;
                    "
                    d="M312.025,446.727L312.025,446.727c21.426,10.092,46.87,4.793,62.596-13.037l0,0c24.917-28.25,41.081-63.265,46.489-100.705v0c3.413-23.63-8.923-46.724-30.349-56.816h0c-32.501-15.309-70.421,5.358-75.598,41.203v0c-2.383,16.497-9.505,31.926-20.484,44.374l0,0C270.822,388.791,279.524,431.418,312.025,446.727z"
                  />
                  <path
                    style="
                      stroke-width: 20;
                      stroke-linecap: round;
                      stroke-linejoin: round;
                      stroke-miterlimit: 10;
                    "
                    d="M199.975,446.727L199.975,446.727c-21.426,10.092-46.87,4.793-62.596-13.037l0,0c-24.917-28.25-41.081-63.265-46.489-100.705v0c-3.413-23.63,8.923-46.724,30.349-56.816h0c32.501-15.309,70.421,5.358,75.598,41.203v0c2.383,16.497,9.505,31.926,20.485,44.374l0,0C241.178,388.791,232.476,431.418,199.975,446.727z"
                  />
                  <path
                    style="
                      stroke-width: 20;
                      stroke-linecap: round;
                      stroke-linejoin: round;
                      stroke-miterlimit: 10;
                    "
                    d="M256,180.694L256,180.694c-19.409,0-35.143,15.734-35.143,35.143v5.02h70.286v-5.02C291.143,196.428,275.409,180.694,256,180.694z"
                  />
                </g>
              </svg>
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
