<template>
  <b-navbar wrapper-class="container" v-if="isLoggedIn">
    <template slot="brand">
      <b-navbar-item tag="router-link" :to="{ name: 'home' }">
        <strong>Recordboard</strong>
      </b-navbar-item>
    </template>

    <template slot="start">
      <b-navbar-item tag="router-link" :to="{ name: 'exercises/index' }" active-class="is-active">Exercises</b-navbar-item>
    </template>

    <template slot="end">
      <b-navbar-dropdown :label="profile.name" v-if="isLoggedIn">
        <b-navbar-item tag="router-link" :to="{ name: 'user/logout' }">Logout</b-navbar-item>
      </b-navbar-dropdown>
    </template>
  </b-navbar>
</template>

<script>
import { mapGetters, mapState } from 'vuex';

export default {
  computed: {
    ...mapGetters('user', ['isLoggedIn']),
    ...mapState('user', ['profile']),
  },
};
</script>
