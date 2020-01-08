<template>
  <div class="container">
    <h1 class="title">Exercises</h1>

    <Breadcrumbs :breadcrumbs="{ 'exercises/index': 'Exercises' }" />

    <div class="columns">
      <div class="column is-three-quarters-desktop">
        <div class="card">
          <div class="card-header">
            <div class="card-header-title">
              Exercises
            </div>
          </div>

          <b-loading :is-full-page="false" :active="!loaded" v-if="!loaded" />
          <ExerciseList v-else :exercises="exercises" />
        </div>
      </div>

      <div class="column">
        <div class="card">
          <div class="card-header">
            <div class="card-header-title">Create exercise</div>
          </div>

          <div class="card-content">
            <CreateForm title="Create exercise" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import CreateForm from '../components/Exercise/CreateForm.vue';
import ExerciseList from '../components/Exercise/ExerciseList.vue';

export default {
  components: { CreateForm, ExerciseList },
  computed: {
    ...mapState('exercise', ['exercises', 'loaded']),
  },
  beforeMount() {
    this.$store.dispatch('exercise/load');
  },
};

</script>
