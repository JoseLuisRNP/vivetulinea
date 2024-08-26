<script setup lang="ts">

import NavBar from "@/Components/Layout/NavBar.vue";
import {Head, router} from "@inertiajs/vue3";
import {computed, ref, watch} from "vue";
import {onClickOutside, watchDebounced} from "@vueuse/core";
import ziggyRoute from "ziggy-js";

interface Food {
    id: number,
    name: string,
    color: string,
    points: number,
    quantity: number,
    unit: string,
    recipeQuantity?: number,
}

const props = defineProps<{
    resultSearch: any,
}>();

const search = ref('');

watchDebounced(search, () => {
    router.reload({preserveState:true, only:['resultSearch'] , data: {q: search.value}});
}, {debounce: 500})

const name = ref('')
const ration = ref(1);
const foods = ref<Food[]>([]);
const calculatedPointsPerFood = computed(() =>
    foods.value.map(food => {
        const result = (food.recipeQuantity * food.points) / food.quantity;
        return Math.max(Math.round(result * 2) / 2, 0);
    })
)

const totalRecipePoints = computed(() => {
    return calculatedPointsPerFood.value.reduce((acc, points) => {
        return acc + points;
    }, 0)
});

const totalRecipePointsByColor = computed(() => {
    return foods.value.reduce((acc, food, currentIndex) => {
        acc[food.color] += calculatedPointsPerFood.value[currentIndex];
        return acc;
    }, {
        blue: 0,
        green: 0,
        yellow: 0,
        red: 0,
        black: 0,
    })
});


const addToRecipe = (food: Food) => {
    if(foods.value.some(
        (f) => f.id === food.id
    )) return;

    food.recipeQuantity = food.quantity;
    foods.value.push(food);
    search.value = '';

}

const deleteFood = (food: Food) => {
    foods.value = foods.value.filter(
        (f) => f.id !== food.id
    )
}
watch(search, () => {
    openResults.value = true;
})
const openResults = ref(false);
const resultsWrapper = ref();
onClickOutside(resultsWrapper, () => {
    if(!openResults.value) return;

    search.value = '';
    openResults.value = false;
})

const createRecipe = () => {
    const recipe = {
        name: name.value,
        ration: ration.value,
        points: totalRecipePoints.value,
        proteins: totalRecipePointsByColor.value.blue,
        sugars: totalRecipePointsByColor.value.yellow,
        fats: totalRecipePointsByColor.value.red,
        foods: foods.value.map((food, index) => {
            return {
                food_id: food.id,
                quantity: food.recipeQuantity,
                unit: food.unit,
                points: calculatedPointsPerFood.value[index]
            }
        })
    }

    router.post(ziggyRoute('recipes.create'), recipe)
}
</script>

<template>
    <Head title="Tus recetas - ViveTuLinea" />
    <NavBar></NavBar>
    <div class="px-6 py-4 sm:py-32 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Crear receta</h2>
            <p class="text-base font-semibold leading-7 text-primary">Añade los alimentos de tu receta</p>
        </div>
        <div class="mx-8 my-2 md:flex md:justify-between">
            <div class="form-control w-full max-w-xs">
                <label class="label">
                    <span class="label-text">Nombre</span>
                </label>
                <input v-model="name" @focus="$event.target.select()" type="text" placeholder="Tu receta" class="input input-bordered  w-full max-w-xs focus:border-primary" />
            </div>
            <div class="form-control w-full max-w-xs">
                <label class="label">
                    <span class="label-text">Número de raciones</span>
                </label>
                <input v-model.number="ration" @focus="$event.target.select()" type="number" placeholder="1" class="input input-bordered  w-full max-w-xs focus:border-primary" />
            </div>
            <div class="form-control w-full max-w-xs">
                <label class="label">
                    <span class="label-text">Alimentos</span>
                </label>
                <input v-model="search" @focus="$event.target.select()" placeholder="Buscar alimento" class="input input-bordered  w-full max-w-xs focus:border-primary" />
                <div class="relative">
                    <div class="absolute dropdown dropdown-open w-full bg-primary-content text-neutral max-h-48 h-48 overflow-auto" :class="resultSearch.length ? 'h-48' : ''" v-show="resultSearch.length" ref="resultsWrapper">
                        <div class="dropdown-content">
                            <div v-for="result in resultSearch" :key="result.id" class="hover:bg-primary hover:text-primary-content flex items-center p-1" @click="addToRecipe(result)">
                                <div class="w-2 h-2 rounded-full mr-2 " :class="{
                                        'bg-blue-500': result.color === 'blue',
                                        'bg-green-500': result.color === 'green',
                                        'bg-yellow-500': result.color === 'yellow',
                                        'bg-red-500': result.color === 'red',
                                        'bg-black': result.color === 'black',
                                    }"></div>
                                <div>{{result.name}}</div>
                                <div class="ml-4 text-xs text-gray-400" >{{result.points}} puntos / {{result.quantity }} {{result.unit.toLowerCase()}}</div>
                            </div>
                        </div>

                    </div>
                </div>
                <ul class="ml-4 text-neutral">
                    <li class="border-b py-2" v-for="(food, i) in foods" :key="food.id">
                        <div class="flex items-center">
                            <div class="w-2 h-2  rounded-full mr-2" :class="{
                                    'bg-blue-500': food.color === 'blue',
                                    'bg-green-500': food.color === 'green',
                                    'bg-yellow-500': food.color === 'yellow',
                                    'bg-red-500': food.color === 'red',
                                    'bg-black': food.color === 'black',
                                }"></div>
                            <div class="flex justify-between w-full items-center">
                                <div class="max-w-[2rem]">
                                    <div class="text-sm">{{food.name}}</div>

                                </div>
                                <div class="flex items-center">
                                    <div class="mr-4">
                                        <input type="number" v-model.number="food.recipeQuantity" placeholder="0" class="input input-bordered input-xs w-12 max-w-xs focus:border-primary" />
                                        <span class="text-xs ml-2">Cantidad</span>
                                    </div>
                                    <div class="text-sm mr-2">{{ calculatedPointsPerFood[i] }} pt</div>
                                    <button @click="deleteFood(food)" class="btn btn-xs btn-error btn-outline">

                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4     ">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>

                                    </button>
                                </div>

                            </div>
                        </div>
                    </li>
                </ul>
                <div class="w-full flex justify-evenly mt-4">
                    <span class="flex items-center" v-for="(quantity, color) in totalRecipePointsByColor" >
                        <span class="w-2 h-2  rounded-full mr-2" :class="{
                                    'bg-blue-500': color === 'blue',
                                    'bg-green-500': color === 'green',
                                    'bg-yellow-500': color === 'yellow',
                                    'bg-red-500': color === 'red',
                                    'bg-black': color === 'black',
                                }"></span>
                        {{quantity}}
                    </span>
                </div>
                <div class="text-neutral text-right py-2">Total: <span class="font-bold">{{totalRecipePoints}} puntos</span> </div>
            </div>
        </div>
        <div class="flex justify-center">
            <button @click="createRecipe" class="btn btn-primary mt-4 w-2/4 m-auto" :disabled="!(name && ration && foods.length)">Crear receta</button>
        </div>

    </div>
</template>

<style scoped>

</style>
