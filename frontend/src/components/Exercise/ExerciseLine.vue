<template>
  <div>
    {{ exercise.name }}&nbsp;
    <b-taglist class="is-pulled-right">
      <b-tag size="small" type="is-info" v-for="(attribute, key) in exercise.attributes" :key="key">{{ attribute }}</b-tag>
    </b-taglist>
  </div>
</template>

<script>
export default {
  props: {
    exercise: {
      type: String,
      required: true,
    },
  },
};
</script>
