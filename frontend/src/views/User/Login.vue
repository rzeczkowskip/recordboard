<template>
  <section class="container has-text-centered">
    <div class="content">
      <h1>Recordboard</h1>

      <p>
        Welcome to <strong>Recordboard</strong>!<br>
        A place where you can log Your workout progress
      </p>
    </div>

    <div class="columns is-centered is-mobile">
      <div class="column is-two-fifths-desktop is-one-quarter-widescreen is-two-fifths-tablet is-four-fifths-mobile">
        <div class="box is-relative">
          <AuthForm @success="onSuccess" />
        </div>
      </div>
    </div>

    <p>
      Don't have an account yet?
      <router-link :to="{ name: 'user/register' }">Create an account</router-link>
    </p>
  </section>
</template>

<script>
import { mapGetters } from 'vuex';
import AuthForm from '../../components/User/AuthForm.vue';

export default {
  components: {AuthForm},
  computed: {
    ...mapGetters('user', ['isLoggedIn']),
  },
  methods: {
    onSuccess() {
      this.$buefy.toast.open({
        message: 'Welcome!',
        type: 'is-success',
      });

      return this.$router.push({ name: 'home' });
    },
  },
};

</script>
