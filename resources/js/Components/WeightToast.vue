<template>
  <div class="flex items-center gap-4">
    <span>!Día de registro de peso!</span>
    <button 
      @click="handleClick" 
      class="btn btn-white text-primary btn-sm"
    >
      Registrar peso
    </button>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import ziggyRoute from 'ziggy-js';


const handleClick = () => {
  router.get(ziggyRoute('weight.show'));
};
</script> 