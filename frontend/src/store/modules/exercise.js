import exerciseRepository from '@/api/exercise';

// initial state
const initialState = {
  attributes: ['weight', 'rep', 'time'],
  exercises: [],
  loaded: false,
};

// getters
const getters = {};

// actions
const actions = {
  load({ commit, state }) {
    if (!state.loaded) {
      return exerciseRepository.getList()
        .then((data) => {
          commit('setExercises', data);
          return data;
        });
    }

    return new Promise((resolve => resolve(state.exercises)));
  },
  addExercise({ commit }, context) {
    return exerciseRepository
      .createExercise(context.name, context.attributes)
      .then((data) => {
        commit('addExercise', data);
      });
  },
  deleteExercise({ commit }, context) {
    return exerciseRepository
      .deleteExercise(context.id)
      .then(() => {
        commit('deleteExercise', context);
      });
  },
};

// mutations
const mutations = {
  setExercises(state, data) {
    state.exercises = data;
    state.loaded = true;
  },
  addExercise(state, exercise) {
    state.exercises.push(exercise);
  },
  deleteExercise(state, exercise) {
    const position = state.exercises.findIndex(item => item.id === exercise.id);

    if (position === -1) {
      return;
    }

    state.exercises.splice(position, 1);
  },
};

export default {
  namespaced: true,
  state: initialState,
  getters,
  actions,
  mutations,
};
