<template>
  <div id="app">
    <header>
      <div class="brand">
        <router-link :to="{ name: 'home' }">Recordboard</router-link>
      </div>
    </header>

    <nav>
      <ul v-if="isLoggedIn">
        <li>
          <router-link to="/exercises">Exercises</router-link>
        </li>
        <li>
          <router-link to="/records">Records</router-link>
        </li>
        <li class="nav-right-start">
          <router-link to="/profile">Profile</router-link>
        </li>
        <li>
          <router-link :to="{ name: 'user/logout' }">Logout</router-link>
        </li>
      </ul>
    </nav>

    <router-view/>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  computed: {
    ...mapGetters('user', ['isLoggedIn']),
  },
};
</script>

<style lang="scss" src="@/scss/app.scss" />
