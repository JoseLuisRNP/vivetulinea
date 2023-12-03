<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavBar from "@/Components/Layout/NavBar.vue";
import { Link } from '@inertiajs/vue3';
import ziggyRoute from "ziggy-js";
import {ref, watch} from "vue";

const times = [
    'Desayuno',
    'Media mañana',
    'Almuerzo',
    'Merienda',
    'Cena',
]

const oneDay = 24 * 60 * 60 * 1000;
const dayActive = ref(new Date());

const nextDay = () => {
    dayActive.value = new Date(dayActive.value.getTime() + oneDay);
}

const prevDay = () => {
    dayActive.value = new Date(dayActive.value.getTime() - oneDay);
}

watch(dayActive, () => {
    // Get points of this day

})
</script>

<template>
    <div>
        <Head title="Dashboard" />

        <NavBar>
            <Link :href="ziggyRoute('calculator', {
                dayActive: dayActive.toISOString()
            })" class="btn btn-square btn-ghost flex items-end text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z" />
                </svg>
            </Link>
        </NavBar>

        <div class="bg-primary flex flex-col items-center w-full p-4 text-primary-content">
            <div class="relative m-2 rounded-md shadow-sm w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input type="text" name="text" id="text" class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-primary placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6" placeholder="Buscar alimento">
                </div>

                <div class="flex justify-between w-full px-8 py-4">
                    <div class="flex flex-col items-center">
                        <div class="text-4xl">20</div>
                        <div class="text-xs">semanales</div>
                        <div class="text-xs">restantes</div>
                    </div>
                    <div class="flex items-center">No contar</div>
                    <div class="flex flex-col items-center">
                        <div class="text-4xl">20</div>
                        <div class="text-xs">diarios</div>
                        <div class="text-xs">restantes</div>
                    </div>
                </div>
            <div class="m-4 flex justify-evenly w-full text-xs">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                    <div>5 puntos</div>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    <div>3 puntos</div>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                    <div>8 puntos</div>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                    <div>3 puntos</div>
                </div>
            </div>
            <div @click.stop>
                <span class="p-2" @click="prevDay">&lt;</span>
                <span class="p-2">{{dayActive.getDate()}}/{{dayActive.getMonth() + 1}}/{{dayActive.getFullYear()}}</span>
                <span class="p-2" @click="nextDay">&gt;</span>
                  </div>
        </div>
        <div>
            <div class="collapse collapse-arrow bg-base-200" v-for="time in times" :key="time">
                <input type="checkbox" class="peer w-full" />
                <div class="collapse-title bg-primary-content text-primary peer-checked:bg-primary-content peer-checked:text-primary">
                    {{ time }}
                </div>
                <div class="collapse-content bg-primary-content text-primary peer-checked:bg-primary-content peer-checked:text-primary">
                    <ul class="ml-4 text-neutral">
                        <li class="border-b py-2">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                <div class="flex justify-between w-full items-center">
                                    <div>
                                        <div class="text-sm">Pollo</div>
                                        <div class="text-xs">140g</div>
                                    </div>
                                    <div class="text-sm">5 puntos</div>
                                </div>
                            </div>
                        </li>
                        <li class="border-b py-2">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                <div class="flex justify-between w-full items-center">
                                    <div>
                                        <div class="text-sm">Lechuga</div>
                                        <div class="text-xs">300g</div>
                                    </div>
                                    <div class="text-sm">2 puntos</div>
                                </div>
                            </div>
                        </li>
                        <li class="border-b py-2">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                <div class="flex justify-between w-full items-center">
                                    <div>
                                        <div class="text-sm">Patatas</div>
                                        <div class="text-xs">140g</div>
                                    </div>
                                    <div class="text-sm">10 puntos</div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="text-neutral text-right py-2">Total: <span class="font-bold">17 puntos</span> </div>
                </div>
            </div>

        </div>
    </div>

</template>
