<template>
  <div class="auth">
    <form method="post" @submit.prevent="auth">
      <div>
        <label for="auth-email">E-mail</label>
        <input type="email" v-model="email" id="auth-email"/>
        <p class="form-message-error" v-if="error">
          Could not sign in. Check your e-mail and password
        </p>
      </div>

      <div>
        <label for="auth-password">Password</label>
        <input type="password" v-model="password" id="auth-password"/>
      </div>

      <button type="submit" :disabled="loading">Sign in</button>
    </form>
  </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  data() {
    return {
      email: '',
      password: '',
      error: false,
      loading: false,
    };
  },
  methods: {
    ...mapActions({
      doAuth: 'user/auth',
    }),
    auth() {
      this.loading = true;

      this.doAuth({ email: this.email, password: this.password })
        .catch(() => {
          this.error = true;
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
