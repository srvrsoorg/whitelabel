<template>
    <PerfectScrollbar
      :options="options"
      class="custom-scrollbar"
      @ps-scroll-y="scrollHandle"
      @ps-scroll-x="scrollHandle"
      @ps-scroll-up="scrollHandle"
      @ps-scroll-down="scrollHandle"
      @ps-scroll-left="scrollHandle"
      @ps-scroll-right="scrollHandle"
      @ps-y-reach-start="scrollHandle"
      @ps-y-reach-end="scrollHandle"
      @ps-x-reach-start="scrollHandle"
      @ps-x-reach-end="scrollHandle"
    >
      <slot></slot>
    </PerfectScrollbar>
  </template>
  
  <script>
  import PerfectScrollbar from "vue3-perfect-scrollbar";
  import "vue3-perfect-scrollbar/dist/vue3-perfect-scrollbar.css";

  export default {
    name: "VueCustomScrollbar",
    components: { PerfectScrollbar },
    props: {
      options: {
        type: Object,
        default: () => ({
          suppressScrollX: true, // default: horizontal scroll disable
        }),
      },
    },
    methods: {
      scrollHandle(evt) {
        this.$emit(evt.type, evt);
      },
    },
  };
  </script>
  
  <style>
  .custom-scrollbar {
    position: relative;
    height: 100%;
  }
  
  /* Custom vertical scrollbar */
  .ps__rail-y,
  .ps__thumb-y {
    width: 6px !important;
    left: auto !important;
  }
  
  .ps__thumb-y {
    right: 0 !important;
  }
  
  /* Hide horizontal scrollbar */
  .ps--active-x > .ps__rail-x {
    display: none !important;
  }
  </style>
  