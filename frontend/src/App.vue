<template>
  <div id="app">
    <Navbar />

    <main class="section is-relative">
      <transition mode="out-in" name="fade">
        <router-view/>
      </transition>
    </main>
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue';

export default {
  components: { Navbar },
};
</script>

<style lang="scss" src="@/scss/app.scss"/>
