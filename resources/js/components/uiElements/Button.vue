<template>
  <button
    type="submit"
    :class="[
      textColorClass,
      'bg-custom-500 h-fit disabled:opacity-75 disabled:pointer-events-none focus:outline-none focus:ring-0 font-medium rounded-md text-sm px-4 py-2 text-center',
    ]"
  >
    <slot />
  </button>
</template>

<script>
export default {};
</script>