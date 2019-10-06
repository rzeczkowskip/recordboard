import store from '@/store/index';

const logoutBeforeGuard = (to, from, next) => {
  if (to.name === 'user/logout') {
    store.dispatch('user/logout');
    return next({ name: 'home' });
  }

  return next();
};

export default {
  logoutBeforeGuard,
};
