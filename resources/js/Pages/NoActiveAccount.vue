<script setup lang="ts">
  import NavBar from '@/Components/Layout/NavBar.vue';
  import SvgIcon from '@/Components/SvgIcon.vue';

  const props = defineProps<{
    isCampaignUser: boolean
  }>();
  
</script>
<template>
  <NavBar />
  <div class="flex items-center justify-center h-5/6 px-8">
    <button
      type="button"
      class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
    >
      <SvgIcon name="warning" class="mx-auto h-12 w-12 text-gray-400" />
      <span v-if="!isCampaignUser" class="mt-2 block text-sm font-semibold text-gray-900"
        >Tu cuenta está desactivada contacta con tu monitora</span
      >
      <span v-else class="mt-2 block text-sm font-semibold text-gray-900"
        >Tu cuenta está desactivada contacta en <a
          class="text-primary hover:text-primary-focus"
          href="mailto:info@vivetulinea.es"
          >info@vivetulinea.es</a
        ></span
      >
    </button>
  </div>
</template>
