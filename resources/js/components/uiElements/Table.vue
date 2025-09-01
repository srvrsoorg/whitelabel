<template>
  <div class="flow-root h-full rounded-md shadow relative">
    <div class="overflow-x-auto rounded-md w-full">
      <div class="inline-block min-w-full align-middle">
        <div class="min-w-full">
          <slot name="header"></slot>
          <div class="">
            <PerfectScrollbar
              :class="bodyHeight ? bodyHeight : 'h-full min-w-full '"
            >
              <table class="min-w-full">
                <thead
                  :class="bgHead"
                  class="bg-[#F6F6F6] sticky z-10 top-0 min-w-full whitespace-nowrap"
                  v-if="head.length"
                >
                  <tr>
                    <td
                      v-for="(header, index) in head"
                      :class="[
                        'px-4 mx-10 py-4 text-left text-[15px] font-medium text-[#31363f]',
                        header && header.classes ? header.classes : '',
                      ]"
                      :key="index"
                    >
                      <template v-if="header !== null">
                        <span class="flex gap-2 items-center" v-if="header.tooltip">
                          {{ header.title ? header.title : header }}
                          <span class="material-symbols-outlined text-xl" :class="isLightColor ? 'text-custom-700' : 'text-custom-500'" v-tooltip="header.tooltip">
                            info
                          </span>
                        </span>
                        <span v-else>
                          {{ header.title ? header.title : header }}
                        </span>
                      </template>
                      <Skeleton v-else :count="1" />
                    </td>
                  </tr>
                </thead>
                <tbody class="text-gray-500 mx-4" :class="bodyPadding">
                  <slot />
                </tbody>
              </table>
            </PerfectScrollbar>
          </div>
        </div>
      </div>
    </div>
    <slot name="pagination"></slot>
  </div>
</template>

<script>
export default {
  props: {
    head: {
      type: Array,
      default: [],
    },
    bodyHeight: {
      type: String,
    },
    bgHead: {
      type: String,
    },
    bodyPadding: {
      type: String,
    },
  },
};
</script>