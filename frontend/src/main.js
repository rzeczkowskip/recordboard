import Vue from 'vue';
import Buefy from 'buefy';
import App from './App.vue';
import router from './router/index';
import store from './store/index';
import api from './api/api';

import Breadcrumbs from './components/Breadcrumbs.vue';
import Error404 from './views/Error404.vue';

Vue.config.productionTip = false;

Vue.use(Buefy);
Vue.component('Breadcrumbs', Breadcrumbs);
Vue.component('Error404', Error404);

router.linkActiveClass = 'is-active';
router.linkExactActiveClass = 'is-active';

new Vue({
  router,
  store,
  render: h => h(App),
  beforeCreate() {
    if (this.$store.state.user.token) {
      api.setAuthToken(this.$store.state.user.token);
    }
  },
}).$mount('#app');
