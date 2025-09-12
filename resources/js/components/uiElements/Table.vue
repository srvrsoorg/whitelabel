<template>
  <div :class="['h-full', ...customClass, margin ? margin:'mt-5']">
    <div class="overflow-x-auto overflow-y-visible h-full rounded-md custom-scrollbar shadow">
      <div class="inline-block min-w-full align-middle overscroll-none" :class="bodyHeight ? bodyHeight : ''">
        <div class="shadow overscroll-none">
          <table class="min-w-full overscroll-none">
              <thead
                  :class="bgHead"
                  class="bg-[#F6F6F6] sticky z-10 top-0 min-w-full whitespace-nowrap"
                  v-if="head.length"
                >
              <tr>
                <th
                  v-for="(header, index) in head"
                  :key="index"
                  scope="col"
                  :class="[
                    'px-4 mx-10 py-4 text-left text-[15px] font-medium text-[#31363f]',
                    header && header.classes ? header.classes : ''
                  ]"
                >   
                  <template v-if="header !== null">
                    <div
                      :class="`flex items-center gap-2 h-full ${
                        header.classes &&
                        (Array.isArray(header.classes) 
                          ? header.classes.some(cls => cls.includes('text-center')) 
                          : header.classes.includes('text-center')) 
                          ? 'justify-center' 
                          : 'justify-start'
                      }`"
                    >
                      <div>{{ header.title ? header.title : header }}</div>
                    </div>
                  </template>
                  <Skeleton v-else :count="1" />
                </th>
              </tr>
            </thead>
            <tbody :class="bodyHeight ? bodyHeight : ''" class="divide-y divide-gray-200 bg-white">
              <slot />
            </tbody>
          </table>
        </div>
      </div>
      </div>
      <div class="-mx-4">
        <slot name="pagination"></slot>
      </div>
  </div>
</template>

<script>
export default {
  props: {
    customClass: {
      type: Array,
      default: () => []
    },
    head: {
      type: Array,
      default: () => []
    },
    margin: {
      type: String
    },
    bodyHeight: {
      type: String
    }
  }
}
</script>

