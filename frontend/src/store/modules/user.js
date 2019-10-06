import userRepository from '@/api/user';

const STORAGE_USER_TOKEN = 'user/token';
const STORAGE_USER_PROFILE = 'user/profile';

// initial state
const initialState = {
  token: localStorage.getItem('user/token'),
  profile: null,
};

try {
  const profile = JSON.parse(localStorage.getItem('user/profile'));
  if (profile) {
    initialState.profile = profile;
  }
} catch (err) {
  // ignore
}

// getters
const getters = {
  isLoggedIn: state => !!state.token && !!state.profile,
};

// actions
const actions = {
  auth({ commit }, context) {
    return userRepository.auth(context.email, context.password)
      .then((data) => {
        commit('setUserData', data);
      });
  },
  logout({ commit }) {
    commit('setUserData', null);
  },
};

// mutations
const mutations = {
  setUserData(state, data) {
    if (data && data.user && data.token) {
      localStorage.setItem(STORAGE_USER_PROFILE, JSON.stringify(data.user));
      localStorage.setItem(STORAGE_USER_TOKEN, data.token);
      state.token = data.token;
      state.profile = data.user;
      return;
    }

    localStorage.removeItem(STORAGE_USER_PROFILE);
    localStorage.removeItem(STORAGE_USER_TOKEN);
    state.token = null;
    state.profile = null;
  },
};

export default {
  namespaced: true,
  state: initialState,
  getters,
  actions,
  mutations,
};
