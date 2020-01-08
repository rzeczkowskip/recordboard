<template>
    <ul class="block-list is-oulined">
      <li v-for="(exercise, key) in exercises" :key="key">
        <router-link :to="{ name: 'exercises/show', params: { exerciseId: exercise.id } }">{{ exercise.name }}</router-link>
        &nbsp;
        <b-taglist class="is-inline-block">
          <b-tag size="small" type="is-info" v-for="(attribute, key) in exercise.attributes" :key="key">{{ attribute }}</b-tag>
        </b-taglist>
      </li>
    </ul>
</template>

<script>
export default {
  props: {
    exercises: {
      type: Array,
      required: true,
    },
  },
};
</script>
