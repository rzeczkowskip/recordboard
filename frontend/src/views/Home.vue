<template>
  <section id="home">
    <div class="grid-2" v-if="!isLoggedIn">
      <div>
        <p>Welcome to recordboard, a place where you can log Your workout progress</p>

        <p>
          In order to use the application, either log in or
          <router-link to="{ name: 'user/register' }">create an account</router-link>
        </p>
      </div>

      <div>
        <AuthForm class="card"/>
      </div>
    </div>
  </section>
</template>

<script>
import { mapGetters } from 'vuex';
import AuthForm from '@/components/User/AuthForm';

export default {
  components: { AuthForm },
  computed: {
    ...mapGetters('user', ['isLoggedIn']),
  },
};

</script>
