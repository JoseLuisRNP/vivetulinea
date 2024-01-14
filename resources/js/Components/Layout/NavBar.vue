<script setup>
import Logo from '@/assets/logo.png';
import { useToast} from "vue-toastification";
import {usePage, Link} from "@inertiajs/vue3";
import { watch} from "vue";
import ziggyRoute from "ziggy-js";

const toast = useToast()


watch(() => usePage().props.flash, flash => {
    let toastId = null;

    if (flash.message) {
        toastId = toast.info(flash.message)
    }
    if (flash.success) {
        toastId = toast.success(flash.success)
    }
    if (flash.error) {
        toastId = toast.error(flash.error)
    }

    if (toastId !== null) {
        setTimeout(() => toast.dismiss(toastId), 3000)
    }
}, {deep: true})
</script>
<template>
    <div class="navbar bg-base-100 border-b-[1px] border-b-base">
        <Link :href="ziggyRoute('menu')" class="flex-1">
            <img :src="Logo" class="btn btn-ghost text-xl" alt="ViveTuLinea Logo"/>
        </Link>
        <div class="flex-none">
            <slot>

            </slot>
            <Link v-if="!route().current('menu')  && !route().current('no-active')" :href="ziggyRoute(!route().current('dashboard') ? 'dashboard' : 'menu')" class="btn btn-square btn-ghost flex items-end text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>
            </Link>
        </div>
    </div>
</template>

