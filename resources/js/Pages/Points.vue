<script setup>
import {Head, router, usePage} from '@inertiajs/vue3';
import NavBar from "@/Components/Layout/NavBar.vue";
import { Link } from '@inertiajs/vue3';
import ziggyRoute from "ziggy-js";
import {computed, ref, watch} from "vue";
import {times} from "@/data";


const urlParams = new URLSearchParams(window.location.search);
const dayActiveParam = urlParams.get('dayActive') || new Date().toISOString();
const dayActive = ref(new Date(dayActiveParam));

const quantity = ref(0);
const name = ref('');
const color = ref('yellow');
const timeOfDay = ref(urlParams.get('time') || 'Snack');
const points = ref(0);

const registerPoints = () => {
    const data = {
        quantity: quantity.value,
        points: points.value,
        name: name.value,
        color: color.value,
        time_of_day: timeOfDay.value,
        consumed_at: dayActive.value.toISOString(),
    }

    router.post(ziggyRoute('points.store'), data)
}
</script>
<template>

    <div>
        <Head title="Añadir puntos" />
        <NavBar>
        </NavBar>
        <div class="px-6 py-4 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Puntos</h2>
                <p class="text-base font-semibold leading-7 text-primary">Añade tus puntos manualmente</p>
            </div>
        </div>
        <div class="flex flex-col justify-center my-8">
            <div class="w-full justify-center items-center flex flex-col">
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Nombre</span>
                    </label>
                    <input v-model="name" @focus="$event.target.select()" type="text" placeholder="Alimento" class="input input-bordered  w-full max-w-xs focus:border-primary" />
                </div>
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Puntos</span>
                    </label>
                    <input v-model.number="points" @focus="$event.target.select()" type="number" placeholder="Alimento" class="input input-bordered  w-full max-w-xs focus:border-primary" />
                </div>
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Cantidad</span>
                    </label>
                    <input v-model.number="quantity" @focus="$event.target.select()" type="number" placeholder="Alimento" class="input input-bordered  w-full max-w-xs focus:border-primary" />
                </div>
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Color</span>
                    </label>
                    <select v-model="color" class="select select-bordered w-full max-w-xs">
                        <option value="red">🔴 Grasas</option>
                        <option value="green">🟢 Azúcares</option>
                        <option value="blue">🔵 Proteínas</option>
                        <option value="yellow">🟡 Sin identificar</option>
                    </select>
                </div>
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Momento</span>
                    </label>
                    <select v-model="timeOfDay" class="select select-bordered w-full max-w-xs">
                        <option v-for="time in times" :key="time" :value="time">{{time}}</option>
                    </select>
                </div>
                <button class="btn btn-primary  mt-4 w-2/4 m-auto"  @click="registerPoints">Añadir puntos al diario</button>
            </div>
        </div>

    </div>
</template>

