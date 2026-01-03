<script setup lang="ts">
  import { Head, router, usePage } from '@inertiajs/vue3';
  import NavBar from '@/Components/Layout/NavBar.vue';
  import { ref, onMounted, onUnmounted } from 'vue';
  import { useToast } from 'vue-toastification';
  import { watchDebounced } from '@vueuse/core';
  import { Link } from '@inertiajs/vue3';
  import ziggyRoute from 'ziggy-js';

  interface Paginate<T> {
    data: T[];
    links: {
      active: boolean;
      label: string;
      url: string | null;
    }[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
  }

  interface UserFood {
    id: number;
    name: string;
    color: string;
    points: number;
    quantity: number;
  }

  const props = defineProps<{
    userFoods: Paginate<UserFood>;
    time?: string;
    dayActive?: string;
    noCountDay?: boolean | string;
  }>();

  const toast = useToast();
  const page = usePage();
  const showModal = ref(false);
  const editingFood = ref<UserFood | null>(null);
  const showDeleteModal = ref(false);
  const foodToDelete = ref<UserFood | null>(null);
  const showMenu = ref<number | null>(null);
  const urlParams = new URLSearchParams(window.location.search);
  const search = ref(urlParams.get('q') || '');

  const form = ref({
    name: '',
    color: 'yellow',
    points: 0,
    quantity: 0,
  });

  const colorOptions = [
    { value: 'yellow', label: '🟡 Sin identificar' },
    { value: 'green', label: '🟢 Hidratos de carbono' },
    { value: 'blue', label: '🔵 Proteínas' },
    { value: 'red', label: '🔴 Grasas' },
    { value: 'black', label: '⚫ Negro' },
  ];

  const colorClasses = {
    blue: 'bg-blue-500',
    green: 'bg-green-500',
    yellow: 'bg-yellow-500',
    red: 'bg-red-500',
    black: 'bg-black',
  };

  watchDebounced(
    search,
    () => {
      router.get(
        page.url,
        {
          q: search.value,
        },
        {
          preserveState: true,
          preserveScroll: true,
        }
      );
    },
    { debounce: 300 }
  );

  const openCreateModal = () => {
    editingFood.value = null;
    form.value = {
      name: '',
      color: 'yellow',
      points: 0,
      quantity: 0,
    };
    showModal.value = true;
  };

  const openEditModal = (food: UserFood) => {
    editingFood.value = food;
    form.value = {
      name: food.name,
      color: food.color,
      points: food.points,
      quantity: food.quantity,
    };
    showModal.value = true;
    showMenu.value = null;
  };

  const closeModal = () => {
    showModal.value = false;
    editingFood.value = null;
  };

  const saveFood = () => {
    if (editingFood.value) {
      router.put(
        ziggyRoute('my-foods.update', { userFood: editingFood.value.id }),
        form.value,
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            closeModal();
            toast.success('Alimento actualizado correctamente');
          },
          onError: () => {
            toast.error('Error al actualizar el alimento');
          },
        }
      );
    } else {
      router.post(
        ziggyRoute('my-foods.store'),
        form.value,
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            closeModal();
            toast.success('Alimento creado correctamente');
          },
          onError: () => {
            toast.error('Error al crear el alimento');
          },
        }
      );
    }
  };

  const openDeleteModal = (food: UserFood) => {
    foodToDelete.value = food;
    showDeleteModal.value = true;
    showMenu.value = null;
  };

  const closeDeleteModal = () => {
    showDeleteModal.value = false;
    foodToDelete.value = null;
  };

  const confirmDelete = () => {
    if (foodToDelete.value) {
      router.delete(
        ziggyRoute('my-foods.destroy', { userFood: foodToDelete.value.id }),
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            closeDeleteModal();
            toast.success('Alimento eliminado correctamente');
          },
          onError: () => {
            toast.error('Error al eliminar el alimento');
          },
        }
      );
    }
  };

  const handleFoodClick = (food: UserFood) => {
    const isNoCountDay = props.noCountDay === true || props.noCountDay === 'true';

    if (isNoCountDay) {
      toast.error('No se pueden añadir alimentos propios el día de no contar');
      return;
    }

    router.post(
      ziggyRoute('my-foods.add-meal', { userFood: food.id }),
      {
        time: props.time,
        dayActive: props.dayActive,
        noCountDay: props.noCountDay,
      },
      {
        onSuccess: () => {
          toast.success('Alimento añadido correctamente');
        },
        onError: () => {
          toast.error('Error al añadir el alimento');
        },
      }
    );
  };

  const toggleMenu = (foodId: number) => {
    showMenu.value = showMenu.value === foodId ? null : foodId;
  };

  const closeMenuOnClickOutside = () => {
    if (showMenu.value !== null) {
      showMenu.value = null;
    }
  };

  onMounted(() => {
    document.addEventListener('click', closeMenuOnClickOutside);
  });

  onUnmounted(() => {
    document.removeEventListener('click', closeMenuOnClickOutside);
  });
</script>

<template>
  <Head title="Mis alimentos" />
  <NavBar />

  <div class="flex flex-col content-center items-center h-full">
    <div class="px-6 py-4 sm:py-32 lg:px-8">
      <div class="mx-auto max-w-2xl text-center">
        <h2 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
          Mis alimentos
        </h2>
        <p class="text-base font-semibold leading-7 text-primary">
          Gestiona tus alimentos personalizados
        </p>
      </div>
    </div>  

    <div class="flex gap-4 items-center justify-center mt-4 w-full px-4">
      <div class="fieldset max-w-xs flex-1">
        <input
          v-model="search"
          type="text"
          placeholder="Buscar alimento"
          class="input"
        />
      </div>
      <button
        v-if="userFoods.total > 0"
        class="btn btn-primary"
        @click="openCreateModal"
      >
        + Crear alimento
      </button>
    </div>

    <div v-if="userFoods.total === 0 && !search.trim()" class="flex flex-col items-center justify-center py-16 px-4">
      <div class="text-center">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">No tienes alimentos creados</h3>
        <p class="text-gray-600 mb-8">
          Crea tu primer alimento personalizado para añadirlo rápidamente a tus comidas
        </p>
        <button class="btn btn-primary btn-lg" @click="openCreateModal">
          Crear mi primer alimento
        </button>
      </div>
    </div>

    <div v-else-if="userFoods.data.length === 0 && search.trim()" class="flex flex-col items-center justify-center py-16 px-4">
      <div class="text-center">
        <p class="text-gray-600">No se encontraron alimentos que coincidan con "{{ search }}"</p>
      </div>
    </div>

    <div v-else class="mt-4 w-full p-4">
      <ul>
        <li
          v-for="food in userFoods.data"
          :key="food.id"
          class="flex items-center border-b p-2 w-full cursor-pointer hover:bg-base-200"
          @click="handleFoodClick(food)"
        >
          <div
            class="w-2 h-2 rounded-full mr-2"
            :class="colorClasses[food.color as keyof typeof colorClasses] || 'bg-gray-500'"
          />
          <div class="flex justify-between w-full min-w-0 gap-2">
            <div class="flex items-center gap-2 min-w-0">
              <div class="line-clamp-2 min-w-0 wrap-break-word">{{ food.name }}</div>
            </div>
            <div class="flex items-center gap-2 shrink-0 whitespace-nowrap">
              <div v-if="food.points && food.quantity">
                {{ food.points }} pts / {{ food.quantity }}
              </div>
              <div class="relative">
                <button
                  @click.stop="toggleMenu(food.id)"
                  class="btn btn-ghost btn-sm btn-square"
                  type="button"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"
                    />
                  </svg>
                </button>
                <div
                  v-if="showMenu === food.id"
                  class="absolute right-0 top-full mt-1 bg-base-100 shadow-lg rounded-md border border-base-300 z-10 min-w-[120px] flex flex-col"
                  @click.stop
                >
                  <button
                    class="w-full text-left px-4 py-2 hover:bg-base-200 rounded-t-md"
                    @click="openEditModal(food)"
                  >
                    Editar
                  </button>
                  <button
                    class="w-full text-left px-4 py-2 hover:bg-base-200 text-error rounded-b-md"
                    @click="openDeleteModal(food)"
                  >
                    Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <div
      v-if="userFoods.prev_page_url || userFoods.next_page_url"
      class="join justify-self-end sticky bottom-0"
    >
      <Link
        v-if="userFoods.prev_page_url"
        :href="userFoods.prev_page_url"
        class="join-item btn bg-primary text-primary-content"
      >
        Anterior
      </Link>
      <button
        v-else
        disabled
        class="join-item btn bg-gray-100 text-neutral"
      >
        Anterior
      </button>
      <Link
        v-if="userFoods.next_page_url"
        :href="userFoods.next_page_url"
        class="join-item btn bg-primary text-primary-content"
      >
        Siguiente
      </Link>
      <button
        v-else
        disabled
        class="join-item btn bg-gray-100 text-neutral"
      >
        Siguiente
      </button>
    </div>

    <!-- Modal crear/editar -->
    <dialog :class="{ modal: true, 'modal-open': showModal }">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">
          {{ editingFood ? 'Editar alimento' : 'Crear alimento' }}
        </h3>
        <div class="space-y-4">
          <div class="fieldset">
            <label class="label">Nombre</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="Nombre del alimento"
              class="input"
            />
          </div>
          <div class="fieldset">
            <label class="label">Color</label>
            <select v-model="form.color" class="select">
              <option v-for="option in colorOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>
          <div class="fieldset">
            <label class="label">Puntos</label>
            <input
              v-model.number="form.points"
              type="number"
              step="0.5"
              min="0"
              placeholder="0"
              class="input"
            />
          </div>
          <div class="fieldset">
            <label class="label">Cantidad</label>
            <input
              v-model.number="form.quantity"
              type="number"
              step="0.5"
              min="0"
              placeholder="0"
              class="input"
            />
          </div>
        </div>
        <div class="modal-action">
          <button class="btn" @click="closeModal">Cancelar</button>
          <button class="btn btn-primary" @click="saveFood">Guardar</button>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop">
        <button @click="closeModal">Cerrar</button>
      </form>
    </dialog>

    <!-- Modal eliminar -->
    <dialog :class="{ modal: true, 'modal-open': showDeleteModal }">
      <div class="modal-box">
        <h3 class="font-bold text-lg">Confirmar eliminación</h3>
        <p class="py-4">
          ¿Estás seguro de que quieres eliminar este alimento? Esta acción no se puede deshacer.
        </p>
        <div class="modal-action">
          <button class="btn" @click="closeDeleteModal">Cancelar</button>
          <button class="btn btn-error" @click="confirmDelete">Eliminar</button>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop">
        <button @click="closeDeleteModal">Cerrar</button>
      </form>
    </dialog>
  </div>
</template>

