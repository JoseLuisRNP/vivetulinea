<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import NavBar from "@/Components/Layout/NavBar.vue";
import ziggyRoute from "ziggy-js";
import {computed, ref } from "vue";
import {times} from "@/data";

const props = defineProps<{
    food: any
}>();

const urlParams = new URLSearchParams(window.location.search);
const dayActiveParam = urlParams.get('dayActive') || new Date().toISOString();
const dayActive = ref(new Date(dayActiveParam));

const name = ref(props.food?.name || '');
const color = ref(props.food?.color || 'yellow');
const timeOfDay = ref(urlParams.get('time') || 'Desayuno');
const points = ref(0);
const noCountDay = ref(!!parseInt(urlParams.get('noCountDay')))
const oilCount = ref(parseInt(urlParams.get('oil')))
const specialCount = ref(parseInt(urlParams.get('special')))
const quantity = ref(noCountDay && props.food &&( props.food.special_no_count || props.food.oil_no_count) ? 1 : 0);

const calculatedPoints = computed(() => {
    if(!props.food || (noCountDay.value && props.food.no_count && !(props.food.oil_no_count && oilCount.value >= 2 || props.food.special_no_count && specialCount.value >=3) )) return 0;
    const result = (quantity.value * props.food.points) / props.food.quantity;
    return Math.max(Math.round(result * 2) / 2, 0);
})

const registerPoints = () => {
    const data = {
        quantity: quantity.value,
        points: props.food ? calculatedPoints.value : points.value,
        name: name.value,
        color: color.value,
        time_of_day: timeOfDay.value,
        consumed_at: dayActive.value.toISOString(),
        special_no_count: noCountDay.value && props.food && props.food.special_no_count,
        oil_no_count: noCountDay.value && props.food && props.food.oil_no_count
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
                <p class="text-base font-semibold leading-7 text-primary">{{ food ? 'Registra puntos del alimento' : 'Añade tus puntos manualmente' }}</p>
            </div>
        </div>
        <div class="flex flex-col justify-center my-8">
            <div class="w-full justify-center items-center flex flex-col">
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Nombre</span>
                    </label>
                    <input v-model="name" @focus="$event.target.select()" type="text" placeholder="Alimento" class="input input-bordered  w-full max-w-xs focus:border-primary" :disabled="food"/>
                </div>
                <div class="form-control w-full max-w-xs" v-if="!food">
                    <label class="label">
                        <span class="label-text">Puntos</span>
                    </label>
                    <input v-model.number="points" @focus="$event.target.select()" type="number" placeholder="Alimento" class="input input-bordered  w-full max-w-xs focus:border-primary" />
                </div>
                <div class="form-control w-full max-w-xs" v-if="!(noCountDay && food &&( food.special_no_count || food.oil_no_count))">
                    <label class="label">
                        <span class="label-text">Cantidad <span v-if="food && food.unit"> en {{food.unit}}</span></span>
                    </label>
                    <input v-model.number="quantity" @focus="$event.target.select()" type="number" placeholder="0" class="input input-bordered  w-full max-w-xs focus:border-primary" />
                </div>
                <div class="form-control w-full max-w-xs" v-if="!food">
                    <label class="label">
                        <span class="label-text">Color</span>
                    </label>
                    <select v-model="color" class="select select-bordered w-full max-w-xs">
                        <option value="green">🟢 Hidratos de carbono</option>
                        <option value="blue">🔵 Proteínas</option>
                        <option value="red">🔴 Grasas</option>
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
                <div class="avatar placeholder mt-6" v-if="food">
                    <div class="bg-primary text-primary-content rounded-full w-16 m-auto">
                        <span class="text-xl">{{ calculatedPoints }}</span>
                    </div>
                </div>
                <button class="btn btn-primary  mt-4 w-2/4 m-auto"  @click="registerPoints">Añadir puntos al diario</button>
            </div>
        </div>

    </div>
</template>

