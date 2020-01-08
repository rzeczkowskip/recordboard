<template>
  <div>
    <p class="form-message-error" v-for="error in errorMessages" :key="error.id">{{ error.message }}</p>
  </div>
</template>

<script>
import form from '../helper/form';

export default {
  props: {
    form: {
      type: Object,
      required: false,
      validator(value) {
        return value instanceof form.Form;
      },
    },
    path: {
      type: String,
      required: false,
    },
    error: {
      type: [String, Object],
      required: false,
      validator(value) {
        return value instanceof form.FormError || typeof value === 'string';
      },
    },
  },
  data() {
    return {
      key: 0,
    };
  },
  computed: {
    errorMessages() {
      if (this.error) {
        return [
          new form.FormError(null, this.error, null),
        ];
      }

      if (this.form) {
        return this.form.getPathErrors(this.path);
      }

      return [];
    },
  },
};
</script>
