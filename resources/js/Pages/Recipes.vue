<script setup lang="ts">
import NavBar from "@/Components/Layout/NavBar.vue";
import {ref} from "vue";
import {watchDebounced} from "@vueuse/core";
import {Head, router, Link} from "@inertiajs/vue3";
import ziggyRoute from "ziggy-js";

interface Paginate<T> {
    data: T[],
    links: {
        active: boolean,
        label: string,
        url: string|null,
    },
    current_page: number,
    first_page_url: string,
    from: number,
    last_page: number,
    last_page_url: string,
    next_page_url: string|null,
    path: string,
    per_page: number,
    prev_page_url: string|null,
    to: number,
    total: number,
}
interface Food {
    id: number,
    name: string,
    color: string,
    special_no_count: boolean,
    oil_no_count: boolean,
}

const props = defineProps<{
    foods: Paginate<Food>,
}>();

const search = ref('');
const urlParams = new URLSearchParams(window.location.search);
const dayActiveParam = urlParams.get('dayActive') || new Date().toISOString();
const dayActive = ref(new Date(dayActiveParam));
const time = ref(urlParams.get('time') || 'Desayuno');


watchDebounced(search, () => {
    router.reload({preserveState:true, data: {q: search.value}});
}, {debounce: 300})
</script>
<template>
    <Head title="Tus recetas - ViveTuLinea" />
    <NavBar></NavBar>

    <div class="flex flex-col content-center items-center h-full">
        <div class="px-6 py-4 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Tus recetas</h2>
                <p class="text-base mt-2 font-semibold leading-7 text-primary">Busca tus recetas</p>
            </div>
        </div>

        <label class="form-control w-full max-w-xs mt-4">
<!--            <Link :href="ziggyRoute('recipes.new')" class="btn btn-outline text-base mb-2 font-semibold text-primary">Crear receta</Link>-->
            <input v-model="search" type="text" placeholder="Buscar alimento" class="input input-bordered"  />
        </label>

        <div class="mt-4 w-full p-4">
            <ul>
                <li class="collapse collapse-arrow bg-base-200" v-for="food in foods.data" :key="food.id">
                    <input type="checkbox" class="peer w-full" />
                    <div class="collapse-title bg-primary-content text-primary peer-checked:bg-primary-content peer-checked:text-primary flex justify-between items-center">
                        {{ food.name }}
                        <div class="flex">
                            <Link :href="ziggyRoute('points.show', {food:food.id, time, dayActive, recipe:true})" class="text-primary-content bg-primary rounded-full h-3 w-3 flex items-center justify-center p-3 z-10">+</Link>
                        </div>
                    </div>
                    <div class="collapse-content bg-primary-content text-primary peer-checked:bg-primary-content peer-checked:text-primary">
                        <ul class="ml-4 text-neutral">
                            <li class="border-b py-2"  v-for="f in food.foods" :key="f.id" >
                                <div class="flex items-center mt-2 ">
                                    <div class="w-2 h-2  rounded-full mr-2" :class="{
                                    'bg-blue-500': f.food.color === 'blue',
                                    'bg-green-500': f.food.color === 'green',
                                    'bg-yellow-500': f.food.color === 'yellow',
                                    'bg-red-500': f.food.color === 'red',
                                    'bg-black': f.food.color === 'black',
                                }"></div>
                                    <div class="flex justify-between w-full items-center">
                                        <div>
                                            <div class="text-sm">{{ f.food.name }}</div>
                                            <div class="text-xs" > {{ f.quantity }} {{f.unit}}</div>
                                        </div>

                                    </div>

                                </div>
                            </li>
                        </ul>
<!--                        <div class="text-neutral text-right py-2">Total: <span class="font-bold">{{totalPointsPerMeal[time]}} puntos</span> </div>-->
                        <div class="text-neutral text-right py-2">
                            <button class="btn btn-xs btn-error btn-outline">Borrar receta</button>
                        </div>
                    </div>
                </li>

            </ul>
        </div>
        <div class="join justify-self-end sticky bottom-0" v-show="foods.prev_page_url || foods.next_page_url">
            <Link :href="foods.prev_page_url" class="join-item btn " :class="foods.prev_page_url ? 'bg-primary text-primary-content' : 'bg-gray-100 text-neutral'">Anterior</Link>
            <Link :href="foods.next_page_url" class="join-item btn " :class="foods.next_page_url ? 'bg-primary text-primary-content' : 'bg-gray-100 text-neutral'">Siguiente</Link>
        </div>
    </div>
</template>

