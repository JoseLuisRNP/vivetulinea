<script setup lang="ts">
import Logo from '@/assets/logo.png'
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <div class="navbar bg-base-100 border-b-[1px] border-b-base">
        <div class="flex-1">
            <img :src="Logo" class="btn btn-ghost text-xl"/>
        </div>
        <div class="flex-none">
            <button class="btn btn-square btn-ghost flex items-end">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
            </button>
        </div>
    </div>

</template>
