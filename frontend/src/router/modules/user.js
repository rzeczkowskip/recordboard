export default [
  {
    path: '/user/register',
    name: 'user/register',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '@/views/User/Register.vue'),
  },
  {
    path: '/user/logout',
    name: 'user/logout',
  },
];
