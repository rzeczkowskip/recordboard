class FormError {
  path = null;

  message = null;

  id = null;

  constructor(path, message, id) {
    this.path = path || null;
    this.message = message || null;
    this.id = id || null;
  }
}

const rfc7870ResponseParser = (response) => {
  const errors = [];

  if (response.data && response.data.violations) {
    response.data.violations.forEach((violation) => {
      errors.push(new FormError(violation.propertyPath, violation.title, violation.type || null));
    });
  }

  return errors;
};

const nestedObjectClear = (obj) => {
  const newObj = { ...obj };

  Object.keys(newObj).forEach((key) => {
    if (Array.isArray(newObj[key])) {
      newObj[key] = [];
      return;
    }

    if (typeof newObj[key] === 'object') {
      newObj[key] = this(newObj[key]);
      return;
    }

    newObj[key] = null;
  });

  return newObj;
};

class Form {
  loading = false;

  data = {};

  errors = [];

  #submitTimeout;

  #clearData;

  constructor({
    data = {}, clearData, submitTimeout = 2,
  }) {
    this.loading = false;
    this.data = { ...data };
    this.errors = [];

    if (clearData) {
      this.#clearData = { ...clearData };
    }

    this.#submitTimeout = submitTimeout * 1000;
  }

  get loading() {
    return this.loading;
  }

  set loading(loading) {
    this.loading = Boolean(loading);
  }

  // errors
  clearErrors() {
    this.errors = [];
  }

  get isValid() {
    return this.errors.length === 0;
  }

  isPathValid(path) {
    return this.getPathErrors(path).length > 0;
  }

  getPathErrors(path, asObject = false) {
    const errors = this.errors.filter(err => err.path === path);

    if (!asObject) {
      return errors.map(error => error.message);
    }

    return errors;
  }

  clearPathErrors(path) {
    this.errors = this.errors.filter(err => err.path !== path);
  }

  addErrors(errors) {
    this.errors.push(...(Array.isArray(errors) ? errors : [errors]));
  }

  // data
  clearData() {
    if (this.#clearData) {
      this.data = { ...this.#clearData };
      return;
    }

    this.data = nestedObjectClear(this.data);
  }

  // actions
  async submit(promise) {
    this.loading = true;

    return promise
      .then((data) => {
        this.clearErrors();
        this.clearData();

        return data;
      })
      .catch((err) => {
        if (err.response && err.response.status === 422) {
          this.clearErrors();
          this.addErrors(rfc7870ResponseParser(err.response));
        }

        Promise.reject(err);
      })
      .finally(() => {
        setTimeout(() => { this.loading = false; }, this.#submitTimeout);
      });
  }
}

export default {
  Form,
  FormError,
};
