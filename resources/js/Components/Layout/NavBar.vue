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
        </div>
    </div>
</template>

