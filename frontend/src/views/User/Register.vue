<template>
  <section id="home">
    <h1>Create account</h1>

    <UserRegister />
  </section>
</template>

<script>
import AuthForm from '@/components/User/AuthForm';

export default {
  components: { AuthForm },
};
</script>
