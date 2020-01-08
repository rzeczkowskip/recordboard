<template>
    <form method="post" @submit.prevent="register">
      <b-field :message="form.getPathErrors('')" v-if="form.isPathValid('')" />

      <b-field label="Name" :message="form.getPathErrors('name')" :type="{ 'is-danger': form.isPathValid('name') }">
        <b-input type="text" v-model="form.data.name" autocomplete="name" />
      </b-field>

      <b-field label="E-mail" :message="form.getPathErrors('email')" :type="{ 'is-danger': form.isPathValid('email') }">
        <b-input type="email" v-model="form.data.email" autocomplete="email" />
      </b-field>

      <b-field label="Password" :message="form.getPathErrors('password')" :type="{ 'is-danger': form.isPathValid('password') || form.data.password !== passwordRepeat }">
        <b-input type="password" v-model="form.data.password" password-reveal />
      </b-field>

      <b-field :type="{ 'is-danger': form.data.password !== passwordRepeat }">
        <b-input type="password" v-model="passwordRepeat" password-reveal />
      </b-field>

      <b-button native-type="submit" :disabled="form.loading || form.data.password !== passwordRepeat">Register</b-button>

      <b-loading v-if="form.loading" :active="true" :is-full-page="false" />
    </form>
</template>

<script>
import { mapActions } from 'vuex';
import form from '../../helper/form';

export default {
  props: {
    autoLogin: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data() {
    return {
      form: new form.Form({ data: { email: '', password: '', name: '' } }),
      passwordRepeat: '',
    };
  },
  methods: {
    ...mapActions({
      doRegister: 'user/register',
      auth: 'user/auth',
    }),
    register() {
      this.loading = true;

      this.form.submit(this.doRegister(this.form.data))
        .then(() => {
          this.passwordRepeat = '';

          if (this.autoLogin) {
            this
              .auth({ email: this.form.data.email, password: this.form.data.password })
              .then(() => {
                this.$emit('success');
              })
              .catch(() => {
                this.$emit('error', 'auth');
              });
          } else {
            this.$emit('success');
          }
        })
        .catch(() => {
          this.$emit('error', 'register');
        })
        .finally(() => {
          this.loading = false;
        });
    },
  },
};
</script>
