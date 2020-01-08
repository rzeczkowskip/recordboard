<template>
  <form method="post" @submit.prevent="save">
    <b-field :message="form.getPathErrors('')" v-if="form.isPathValid('')" />

    <b-field label="Name" :message="form.getPathErrors('name')" :type="{ 'is-danger': form.isPathValid('name') }">
      <b-input type="text" v-model="form.data.name"/>
    </b-field>

    <b-field label="Attributes" :message="form.getPathErrors('attributes')" :type="{ 'is-danger': form.isPathValid('name') }">
      <b-field class="is-marginless">
        <b-checkbox-button v-model="form.data.attributes" v-for="(attribute, index) in attributes"
                           :key="index" :native-value="attribute">
          <span>{{ attribute }}</span>
        </b-checkbox-button>
      </b-field>
    </b-field>

    <b-button native-type="submit" :disabled="form.loading">Save</b-button>

    <b-loading v-if="form.loading" :active="true" :is-full-page="false" />
  </form>
</template>

<script>
import { mapActions, mapState } from 'vuex';
import form from '../../helper/form';

export default {
  props: {
    title: {
      type: String,
      default: null,
      required: false,
    },
  },
  data() {
    return {
      form: new form.Form({ data: { name: '', attributes: [] } }),
      loading: false,
      errors: {},
    };
  },
  computed: {
    ...mapState('exercise', ['attributes']),
  },
  methods: {
    ...mapActions({
      doSave: 'exercise/addExercise',
    }),
    save() {
      this.form.submit(this.doSave(this.form.data));
    },
  },
};
</script>
