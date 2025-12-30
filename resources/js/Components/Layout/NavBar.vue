<script setup>
  import Logo from '@/assets/logo.png';
  import { useToast } from 'vue-toastification';
  import { usePage, Link } from '@inertiajs/vue3';
  import { watch } from 'vue';
  import ziggyRoute from 'ziggy-js';
  import SvgIcon from '@/Components/SvgIcon.vue';
  import Drawer from '@/Components/Layout/Drawer.vue';
  import DrawerMenu from '@/Components/Layout/DrawerMenu.vue';

  const toast = useToast();
  const page = usePage();

  watch(
    () => page.props.flash,
    (flash) => {
      let toastId = null;

      if (flash.message) {
        toastId = toast.info(flash.message);
      }
      if (flash.success) {
        toastId = toast.success(flash.success);
      }
      if (flash.error) {
        toastId = toast.error(flash.error);
      }

      if (toastId !== null) {
        setTimeout(() => toast.dismiss(toastId), 3000);
      }
    },
    { deep: true, immediate: true }
  );
</script>

<template>
  <Drawer drawer-id="nav-drawer" position="end">
    <template #content>
      <div class="navbar bg-base-100 border-b border-b-base-300">
        <Link :href="ziggyRoute('menu')" class="flex-1">
          <img :src="Logo" class="btn btn-ghost text-xl" alt="ViveTuLinea Logo" />
        </Link>
        <div class="flex">
          <slot />
          <Link
            v-if="!route().current('menu') && !route().current('no-active')"
            :href="ziggyRoute(!route().current('dashboard') ? 'dashboard' : 'menu')"
            class="btn btn-square btn-ghost flex items-end text-primary"
          >
            <SvgIcon name="arrow-back" class="w-6 h-6" />
          </Link>
          <label for="nav-drawer" class="btn btn-square btn-ghost flex items-end text-primary drawer-button">
            <SvgIcon name="burger-menu" class="w-6 h-6" />
          </label>
        </div>
      </div>
    </template>

    <template #sidebar>
      <DrawerMenu />
    </template>
  </Drawer>
</template>
