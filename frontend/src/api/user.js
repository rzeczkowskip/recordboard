import api from './api';

export default {
  auth(email, password) {
    return api.client
      .post(
        'v1/user/auth',
        {
          email,
          password,
        },
      )
      .then(response => response.data.data);
  },
};
