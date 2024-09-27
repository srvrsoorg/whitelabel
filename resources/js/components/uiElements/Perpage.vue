<template>
  <div>
    <label class="text-tiny mr-1.5 "
      >Result Per Page :
    </label>
    <select
      v-model="per_page"
      class="text-tiny border-1 border-gray-300 focus:ring-0 focus:border-gray-500 rounded-md py-2 focus:outline-none"
    >
      <option value="10">10</option>
      <option value="20">20</option>
      <option value="30">30</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: String,
      required: true,
    },
    initialPerPage: {
      type: Number,
      required: false,
      default: 10
    },
  },
  data() {
    return {
      per_page: this.initialPerPage,
    };
  },
  watch: {
    per_page(val) {
      this.$emit("update:modelValue", val);
      this.$emit("changePage",val);
    },
  },
};
</script>
