<script setup>

import {usePage} from "@inertiajs/vue3";

const user = usePage().props.auth.user;
</script>
<template>
    <div class="mt-4">
        <h2 class="text-xl font-medium text-gray-500">Mi capital</h2>
        <ul role="list" class="mt-2 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-4">
            <li class="col-span-1 flex rounded-md shadow-sm">
                <div class="flex w-16 flex-shrink-0 items-center justify-center bg-primary rounded-l-md text-sm font-medium text-white">Totales</div>
                <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-b border-r border-t border-gray-200 bg-white">
                    <div class="flex-1 truncate px-4 text-sm">
                        <p class="font-medium text-xl text-gray-900 hover:text-gray-600">{{ user.daily_points }}</p>
                    </div>
                </div>
            </li>
            <li class="col-span-1 flex rounded-md shadow-sm">
                <div class="flex w-16 flex-shrink-0 items-center justify-center bg-green-500 rounded-l-md text-sm font-medium text-white">Hidratos</div>
                <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-b border-r border-t border-gray-200 bg-white">
                    <div class="flex-1 truncate px-4 text-sm">
                        <p class="font-medium text-xl text-gray-900 hover:text-gray-600">{{ user.sugars}}</p>
                    </div>
                </div>
            </li>
            <li class="col-span-1 flex rounded-md shadow-sm">
                <div class="flex w-16 flex-shrink-0 items-center justify-center bg-blue-500 rounded-l-md text-sm font-medium text-white">Proteínas</div>
                <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-b border-r border-t border-gray-200 bg-white">
                    <div class="flex-1 truncate px-4 text-sm">
                        <p class="font-medium text-xl text-gray-900 hover:text-gray-600">{{ user.proteins }}</p>
                    </div>
                </div>
            </li>
            <li class="col-span-1 flex rounded-md shadow-sm">
                <div class="flex w-16 flex-shrink-0 items-center justify-center bg-red-600 rounded-l-md text-sm font-medium text-white">Grasas</div>
                <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-b border-r border-t border-gray-200 bg-white">
                    <div class="flex-1 truncate px-4 text-sm">
                        <p class="font-medium text-xl text-gray-900 hover:text-gray-600">{{ user.fats }}</p>
                    </div>
                </div>
            </li>
            <li class="col-span-1 flex rounded-md shadow-sm">
                <div class="flex w-16 flex-shrink-0 items-center justify-center bg-gray-600 rounded-l-md text-sm font-medium text-white">Extras</div>
                <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-b border-r border-t border-gray-200 bg-white">
                    <div class="flex-1 truncate px-4 text-sm">
                        <p class="font-medium text-xl text-gray-900 hover:text-gray-600">{{ user.weekly_points }}</p>
                    </div>
                </div>
            </li>
        </ul>
    </div>

</template>

