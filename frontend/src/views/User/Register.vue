<template>
  <section class="container has-text-centered">
    <div class="content">
      <h1>Create account</h1>

      <p>Creating an account giver you ability to:</p>

      <ul class="list-unstyled">
        <li>Creating your own exercises</li>
        <li>Saving records on each of the exercises</li>
      </ul>

      <p>
        Have an account already?
        <router-link :to="{ name: 'user/login' }">Sign in</router-link>
      </p>
    </div>

    <div class="columns is-centered is-mobile">
      <div
        class="column is-two-fifths-desktop is-one-quarter-widescreen is-two-fifths-tablet is-four-fifths-mobile">
        <div class="box is-relative">
          <RegisterForm @success="onSuccess" @error="onError" :autoLogin="true" />
        </div>
      </div>
    </div>
  </section>
</template>
<script>
import RegisterForm from '../../components/User/RegisterForm.vue';

export default {
  components: { RegisterForm },
  methods: {
    onSuccess() {
      this.$buefy.toast.open({
        message: 'Welcome!',
        type: 'is-success',
      });

      this.$router.push({ name: 'home' });
    },
    onError() {
      this.$router.push({ name: 'user/login' });
    },
  },
};
</script>
