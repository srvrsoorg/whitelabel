
<template>
  <TailwindPagination
    class="flex flex-wrap"
    :active-classes="[
      'bg-gray-50  border-[#CBD5E0] !px-3 !py-2 !text-tiny',
      textColorClass ? 'text-custom-700' : 'text-custom-500',
    ]"
    :item-classes="[
      'bg-white text-[#818995] !text-tiny border-[#CBD5E0] first:rounded-l-lg last:rounded-r-lg !px-3 !py-2',
    ]"
    :limit="1"
    :data="pagination"
    @pagination-change-page="changePage"
  >
    <template #prev-nav>
      <div class="flex items-center gap-2">
        <i class="fas fa-angle-left"></i>
        <span>Previous</span>
      </div>
    </template>
    <template #next-nav>
      <div class="flex items-center gap-2">
        <span>Next</span>
        <i class="fas fa-angle-right"></i>
      </div>
    </template>
  </TailwindPagination>
</template>

<script>
import { TailwindPagination } from "laravel-vue-pagination";

export default {
  props: ["pagination"],
  components: { TailwindPagination },
  data() {
    return {
      textColorClass: "text-custom-500",
    };
  },
  methods: {
    changePage(value) {
      this.$emit("page-change", value);
    },
  },
};
</script>


