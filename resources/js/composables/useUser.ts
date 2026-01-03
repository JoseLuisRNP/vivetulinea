import { PageProps } from "@/types";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export const useUser = () => {
  const page = usePage<PageProps>();

  const user = computed(() => page.props.auth.user);
  const isAdminOrDietician = computed(() =>  ['admin', 'dietician'].includes(user.value.role));


  return {
    user,
    isAdminOrDietician,
  };

};