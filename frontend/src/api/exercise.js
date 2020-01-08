import api from './api';

export default {
  getList() {
    return api.client
      .get('v1/exercises')
      .then(response => response.data.data);
  },
  createExercise(data) {
    return api.client
      .post(
        'v1/exercises',
        data,
      )
      .then(response => response.data.data);
  },
  updateExercise(id, data) {
    return api.client
      .post(
        `v1/exercises/${id}`,
        data,
      )
      .then(response => response.data.data);
  },
  deleteExercise(id) {
    return api.client
      .delete(`v1/exercises/${id}`);
  },
};
