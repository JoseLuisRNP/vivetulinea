import './bootstrap';
import '../css/app.css';

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import Toast, { POSITION } from 'vue-toastification';
import * as Sentry from '@sentry/vue';
import VCalendar from 'v-calendar';
import 'v-calendar/style.css';

// Import the CSS or use your own!
import 'vue-toastification/dist/index.css';
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

if (import.meta.env.PROD) {
  Sentry.init({
    dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC,
  });
}

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob<DefineComponent>('./Pages/**/*.vue')
    ),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .use(Toast, { position: POSITION.BOTTOM_LEFT })
      .use(VCalendar, {})
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});
