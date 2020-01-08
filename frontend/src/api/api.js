import axios from 'axios';

const baseURL = '/api';

const client = axios.create({
  baseURL,
});

const setAuthToken = (token) => {
  if (!token) {
    delete client.defaults.headers.common.Authorization;
    return;
  }

  client.defaults.headers.common.Authorization = `Bearer ${token}`;
};

export default {
  client,
  setAuthToken,
};
