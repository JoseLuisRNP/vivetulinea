<script setup>
import Logo from '@/assets/logo.png';

</script>
<template>
    <div class="navbar bg-base-100 border-b-[1px] border-b-base">
        <div class="flex-1">
            <img :src="Logo" class="btn btn-ghost text-xl" alt="ViveTuLinea Logo"/>
        </div>
        <div class="flex-none">
            <slot>

            </slot>
        </div>
    </div>
</template>

