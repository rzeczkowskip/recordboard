import Vue from 'vue';
import Router from 'vue-router';
import Error404 from '@/views/Error404.vue';

import guards from './guards';

import userRoutes from './modules/user';
import exerciseRoutes from './modules/exercise';

Vue.use(Router);

const router = new Router({
  linkActiveClass: 'a',
  linkExactActiveClass: 'a',
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'home',
    },
    ...userRoutes,
    ...exerciseRoutes,
    {
      path: '*',
      component: Error404,
    },
  ],
});

router.beforeEach(guards.authRedirect);
router.beforeEach(guards.logoutBeforeGuard);

export default router;
