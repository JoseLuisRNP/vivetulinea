<script setup lang="ts">
  import logo from '@/assets/logo.png';

  import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
  import ziggyRoute from 'ziggy-js';

  defineProps<{
    canResetPassword?: boolean;
    status?: string;
  }>();

  const page = usePage();

  const form = useForm({
    email: '',
    password: '',
    remember: true,
  });

  const submit = () => {
    form.post(route('login'), {
      onFinish: () => {
        form.reset('password');
      },
    });
  };
</script>

<template>
  <Head title="Log in" />

  <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <img class="mx-auto h-16 w-auto mt-4" :src="logo" alt="ViveTuLinea" />
      <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900" />
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" method="POST" @submit.prevent="submit">
        <div>
          <label for="email" class="block text-sm font-medium leading-6 text-gray-900"
            >Teléfono</label
          >
          <div class="mt-2">
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="text"
              autocomplete="email"
              required
              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
            />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm font-medium leading-6 text-gray-900"
              >Constraseña</label
            >
            <!--                            <div class="text-sm">-->
            <!--                                <a href="#" class="font-semibold text-secondary hover:text-secondary-focus">¿Olvidaste la contraseña?</a>-->
            <!--                            </div>-->
          </div>
          <div class="mt-2">
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
            />
          </div>
        </div>
        <label class="label">
          <span v-if="page.props.errors.email" class="label-text-alt text-error">{{
            page.props.errors.email
          }}</span>
        </label>
        <div>
          <button type="submit" class="btn btn-primary w-full">Iniciar sesión</button>
        </div>
      </form>

      <p class="mt-10 text-center text-sm text-gray-500">
        ¿Aún no tienes cuenta?
        <Link
          :href="ziggyRoute('register')"
          class="font-semibold leading-6 text-primary hover:text-primary-focus"
        >
          Registrate
        </Link>
      </p>
    </div>
  </div>
</template>
