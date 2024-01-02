<script setup>

import NavBar from "@/Components/Layout/NavBar.vue";
import ziggyRoute from "ziggy-js";
import { Link } from '@inertiajs/vue3';
import Capital from "@/Components/Capital.vue";
import Pautas from "@/Components/Pautas.vue";

</script>
<template>
    <Head title="Menu" />

    <div class="h-screen">
        <NavBar></NavBar>
        <div class="w-full h-5/6 flex flex-col justify-between items-center">
            <div class="flex w-full h-full flex-col content-between justify-between">
                <Capital/>

                <div class="flex w-full justify-between my-6">
                    <div class="grid h-20 flex-grow card rounded-box place-items-center basis-1/2">
                        <Link :href="ziggyRoute('dashboard')" class="text-primary flex flex-col justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            <span class="text-xl">Diario</span>
                        </Link>

                    </div>
                    <div class="divider divider-horizontal "></div>
                    <div class="grid h-20 flex-grow card rounded-box place-items-center basis-1/2">
                        <Link :href="ziggyRoute('calculator')" class="text-primary flex flex-col justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z" />
                            </svg>
                            <span class="text-xl">Calculadora</span>
                        </Link>
                    </div>
                </div>
                <Pautas/>

            </div>
        </div>
    </div>


</template>

