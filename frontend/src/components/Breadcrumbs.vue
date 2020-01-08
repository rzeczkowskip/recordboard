<template>
  <nav class="breadcrumb" aria-label="breadcrumbs">
    <ul>
      <li><router-link to="/">Recordboard</router-link></li>
      <li v-for="(crumb, route) in breadcrumbs" :key="crumb">
        <router-link :to="{ name: route }">{{ crumb }}</router-link>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  props: {
    breadcrumbs: {
      type: Object,
      required: false,
      default: () => {},
    },
  },
};
</script>
