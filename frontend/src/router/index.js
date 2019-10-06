import Vue from 'vue';
import Router from 'vue-router';
import Home from '@/views/Home.vue';

import guards from './guards';
import userRoutes from './modules/user';

Vue.use(Router);

const router = new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
    },
    ...userRoutes,
  ],
});

router.beforeEach(guards.logoutBeforeGuard);

export default router;
