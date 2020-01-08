<template>
  <b-modal active :on-cancel="cancel">
    <div class="card">
      <div class="card-content">
        Are you sure you want to delete "{{ exercise.name }}"? This action annot be undone,
        You will loose any progress recorded to that exercise.
      </div>

      <footer class="modal-card-foot is-right">
        <button @click.prevent="doDelete" class="button is-danger">Delete</button>
      </footer>
    </div>
  </b-modal>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  props: {
    exercise: {
      type: Object,
      required: false,
    },
  },
  methods: {
    ...mapActions('exercise', ['deleteExercise']),
    cancel() {
      this.$emit('cancel');
    },
    doDelete() {
      this
        .deleteExercise(this.exercise)
        .then(() => {
          this.$buefy.toast.open({
            message: 'Exercise deleted',
            type: 'is-success',
          });

          this.$emit('success');
        })
        .catch(() => {
          this.$buefy.toast.open({
            message: 'Error',
            type: 'is-danger',
          });

          this.$emit('error');
        });
    },
  },
};
</script>
