<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import NavBar from "@/Components/Layout/NavBar.vue";
import ziggyRoute from "ziggy-js";

const points = computed(() => {
    if(!calories.value || !fats.value || !sugars.value || !proteins.value || !quantity.value) return '-';

    return Math.round(((calories.value * 0.0305) + (fats.value * 0.275) + (sugars.value * 0.12) - (proteins.value * 0.098)) * (quantity.value / 100))
})

const calories = ref(0);
const fats = ref(0);
const sugars = ref(0);
const proteins = ref(0);
const quantity = ref(0);
</script>
<template>
    <div>
        <Head title="Calculadora" />
        <NavBar>
            <Link :href="ziggyRoute('dashboard')" class="btn btn-square btn-ghost flex items-end text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>

            </Link>
        </NavBar>
        <div class="px-6 py-4 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Calculadora</h2>
                <p class="text-base font-semibold leading-7 text-primary">Introducir información nutricional para 100g/ml</p>
            </div>
        </div>
        <div class="mx-8 my-2">

            <div class="form-control w-full max-w-xs">
                <label class="label">
                    <span class="label-text">Calorías</span>
                </label>
                <input v-model.number="calories" @focus="$event.target.select()" type="number" placeholder="0" class="input input-bordered  w-full max-w-xs focus:border-primary" />
            </div>
            <div class="form-control w-full max-w-xs">
                <label class="label">
                    <span class="label-text">Proteínas</span>
                </label>
                <input v-model.number="proteins" @focus="$event.target.select()" type="number" class="input input-bordered  w-full max-w-xs focus:border-primary" />
            </div>
            <div class="form-control w-full max-w-xs">
                <label class="label">
                    <span class="label-text">Azúcares</span>
                </label>
                <input v-model.number="sugars" @focus="$event.target.select()" type="number" class="input input-bordered  w-full max-w-xs focus:border-primary" />
            </div>
            <div class="form-control w-full max-w-xs">
                <label class="label">
                    <span class="label-text">Grasas saturadas</span>
                </label>
                <input v-model.number="fats" @focus="$event.target.select()" type="number" class="input input-bordered  w-full max-w-xs focus:border-primary" />
            </div>
            <div class="form-control w-full max-w-xs">
                <label class="label">
                    <span class="label-text">Cantidad consumida</span>
                </label>
                <input v-model.number="quantity" @focus="$event.target.select()" type="number" class="input input-bordered  w-full max-w-xs focus:border-primary" />
            </div>
        </div>
        <div class="flex justify-center my-8">
            <div class="avatar placeholder ">
                <div class="bg-primary text-primary-content rounded-full w-16 m-auto">
                    <span class="text-xl">{{ points }}</span>
                </div>
            </div>
        </div>

    </div>
</template>

