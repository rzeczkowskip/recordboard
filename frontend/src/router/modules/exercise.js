export default [
  {
    path: '/exercises/',
    name: 'exercises/index',
    component: () => import(/* webpackChunkName: "exercises" */ '@/views/Exercises.vue'),
  },
  {
    path: '/exercises/:exerciseId',
    name: 'exercises/show',
    component: () => import(/* webpackChunkName: "exercises" */ '@/views/Exercise.vue'),
    props: true,
  },
];
