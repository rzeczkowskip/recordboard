<template>
  <Error404 v-if="!exercise" />
  <div class="container" v-else>
    <div class="level">
      <div class="level-left">
        <div class="level-item">
          <h1 class="title">
            {{ exercise.name }}
          </h1>
        </div>
        <div class="level-item">
          <b-taglist class="is-pulled-left">
            <b-tag type="is-info" v-for="(attribute, key) in exercise.attributes" :key="key">{{ attribute }}</b-tag>
          </b-taglist>
        </div>
      </div>
    </div>

    <Breadcrumbs :breadcrumbs="{ 'exercises/index': 'Exercises', 'exercises/show': exercise.name }" />

    <div class="card">
      <div class="card-content">

      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';

import store from '../store';

const routeBefore = (to, from, next) => {
  store
    .dispatch('exercise/load')
    .then(() => next());
};

export default {
  props: {
    exerciseId: {
      type: String,
      required: true,
    },
  },
  computed: {
    ...mapState('exercise', ['exercises']),
    exercise() {
      return this.exercises.find(exercise => exercise.id === this.exerciseId);
    },
  },
  beforeRouteEnter(to, from, next) {
    routeBefore(to, from, next);
  },
  beforeRouteUpdate(to, from, next) {
    routeBefore(to, from, next);
  },
};

</script>
