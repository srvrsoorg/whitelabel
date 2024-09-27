<template>
  <button
    type="button"
    @click="togglePasswordVisibility"
    class="absolute inset-y-0 right-0 focus:border-primary ring-gray-300 leading-6 bg-white border border-l-0 border-primary rounded-r-md flex items-center px-2 py-3.5 pr-3 bg-transparent"
  >
    <span
      class="material-symbols-outlined text-[18px] text-gray-400"
      v-if="showPassword"
    >
      visibility_off
    </span>
    <span class="material-symbols-outlined text-[18px] text-gray-400" v-else>
      visibility
    </span>
  </button>
</template>

<script>
export default {
  props: {
    showPassword: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    togglePasswordVisibility() {
      this.$emit("toggle");
    },
  },
};
</script>
