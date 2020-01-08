import Vue from 'vue';
import Vuex from 'vuex';

import user from './modules/user';
import exercise from './modules/exercise';

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    user,
    exercise,
  },
});
