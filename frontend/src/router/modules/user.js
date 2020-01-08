export default [
  {
    path: '/user/register',
    name: 'user/register',
    component: () => import(/* webpackChunkName: "exercises" */ '@/views/User/Register.vue'),
  },
  {
    path: '/user/login',
    name: 'user/login',
    component: () => import(/* webpackChunkName: "exercises" */ '@/views/User/Login.vue'),
  },
  {
    path: '/user/logout',
    name: 'user/logout',
  },
];
