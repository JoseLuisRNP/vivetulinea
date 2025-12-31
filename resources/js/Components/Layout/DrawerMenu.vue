<script setup>
  import { Link } from '@inertiajs/vue3';
  import ziggyRoute from 'ziggy-js';
  import SvgIcon from '@/Components/SvgIcon.vue';
  import Logo from '@/assets/logo.png';

  const menuItems = [
    {
      route: 'menu',
      icon: 'home',
      label: 'Menú',
    },
    {
      route: 'dashboard',
      icon: 'diary',
      label: 'Diario',
    },
    {
      route: 'calculator',
      icon: 'calculator',
      label: 'Calculadora',
    },
    {
      route: 'weights.show',
      icon: 'weight-scale',
      label: 'Peso',
    },
    {
      route: 'recipes.index',
      icon: 'recipe-book',
      label: 'Recetas',
    },
    {
      route: 'my-foods.index',
      icon: 'recipe-book',
      label: 'Mis alimentos',
    },
    {
      route: 'foods.index',
      icon: 'search',
      label: 'Listado de alimentos',
    },
    {
      route: 'food.no-count',
      icon: 'no-count-day',
      label: 'Alimentos día de no contar',
    },
  ];

  const isActiveRoute = (routeName) => {
    return route().current(routeName);
  };
</script>

<template>
  <div class="bg-base-200 min-h-full w-80 flex flex-col">
    <div class="p-4 border-b border-base-300">
      <div class="flex items-center justify-between">
        <Link :href="ziggyRoute('menu')" class="flex items-center gap-2">
          <img :src="Logo" class="h-8 w-auto" alt="ViveTuLinea Logo" />
        </Link>
        <label for="nav-drawer" class="btn btn-ghost btn-square h-8 w-8 min-h-8 drawer-button flex items-center justify-center p-0 mt-3">
          <SvgIcon name="close-x" class="w-5 h-5" />
        </label>
      </div>
    </div>
    <ul class="menu p-4 text-base-content flex-1">
      <li v-for="item in menuItems" :key="item.route">
        <Link
          :href="ziggyRoute(item.route)"
          :class="[
            'flex items-center gap-2',
            isActiveRoute(item.route) ? 'text-primary' : '',
          ]"
        >
          <SvgIcon
            :name="item.icon"
            :class="[
              'w-5 h-5',
              isActiveRoute(item.route) ? 'text-primary' : '',
            ]"
          />
          <span :class="isActiveRoute(item.route) ? 'font-semibold' : ''">
            {{ item.label }}
          </span>
        </Link>
      </li>
    </ul>
  </div>
</template>

