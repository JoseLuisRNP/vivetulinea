<script setup lang="ts">
  import NavBar from '@/Components/Layout/NavBar.vue';


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
      <svg
        class="mx-auto h-12 w-12 text-gray-400"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
        />
      </svg>
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
