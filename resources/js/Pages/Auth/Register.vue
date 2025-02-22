<script setup lang="ts">
import Logo from '@/assets/logo.png'
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
import ziggyRoute from "ziggy-js";

const route = (name: string) => ziggyRoute(name);
const urlParams = new URLSearchParams(window.location.search);
const referer = urlParams.get('referer');
const page = usePage();
const form = useForm({
    name: '',
    email: '',
    referer: referer,
});

const submit = () => {
    form.post(route('register'));
};
</script>

<template>
        <Head title="Register" />
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-16 w-auto mt-4" :src="Logo" alt="ViveTuLinea">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"></h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" method="POST" @submit.prevent="submit">
<!--                <div>-->
<!--                    <label for="text" class="block text-sm font-medium leading-6 text-gray-900">Nombre</label>-->
<!--                    <div class="mt-2">-->
<!--                        <input v-model="form.name" id="name" name="text" type="text" autocomplete="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6">-->
<!--                    </div>-->
<!--                </div>-->
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Teléfono</label>
                    <div class="mt-2">
                        <input v-model="form.email" id="email" name="email" type="text"  required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6">
                    </div>
                    <label class="label">
                        <span v-if="Object.keys(page.props.errors).length" class="label-text-alt text-error"> {{ Object.values(page.props.errors)[0] }}</span>
                    </label>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary w-full mt-6">Registrarse</button>
                </div>
            </form>

            <p class="mt-10 text-center text-sm text-gray-500">
                <Link :href="ziggyRoute('login')" class="font-semibold leading-6 text-primary hover:text-primary-focus">Volver</Link>
            </p>
        </div>
    </div>
</template>
