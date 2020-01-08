import store from '@/store/index';

const logoutBeforeGuard = (to, from, next) => {
  if (to.name === 'user/logout') {
    store.dispatch('user/logout');
    return next({ name: 'user/login' });
  }

  return next();
};

const authRedirect = (to, from, next) => {
  const { name } = to;
  const isLoggedIn = store.getters['user/isLoggedIn'];

  const protectedRoutes = [
    'user/login',
    'user/register',
  ];

  if (!isLoggedIn && protectedRoutes.indexOf(name) === -1) {
    return next({ name: 'user/login' });
  }

  if (isLoggedIn && protectedRoutes.indexOf(name) !== -1) {
    return next({ name: 'home' });
  }

  return next();
};

export default {
  authRedirect,
  logoutBeforeGuard,
};
