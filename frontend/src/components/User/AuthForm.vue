<template>
  <form method="post" @submit.prevent="auth">
    <b-field label="E-mail" :message="errorMessage" :type="{ 'is-danger': hasError }">
      <b-input type="email" v-model="email"/>
    </b-field>

    <b-field label="Password">
      <b-input type="password" v-model="password"/>
    </b-field>

    <b-button native-type="submit" :disabled="loading">Save</b-button>

    <b-loading v-if="loading" :active="true" :is-full-page="false" />
  </form>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  data() {
    return {
      email: '',
      password: '',
      hasError: false,
      loading: false,
    };
  },
  computed: {
    errorMessage() {
      if (this.hasError) {
        return 'Could not sign in. Check your e-mail and password';
      }

      return null;
    },
  },
  methods: {
    ...mapActions({
      doAuth: 'user/auth',
    }),
    auth() {
      this.loading = true;

      this.doAuth({ email: this.email, password: this.password })
        .then(() => {
          this.$emit('success');
        })
        .catch(() => {
          this.hasError = true;
          this.$emit('error');
        })
        .finally(() => {
          this.loading = false;
        });
    },
  },
};
</script>

<style lang="scss">
  .auth.card {
    form {
      margin-bottom: 0;
    }
  }
</style>
