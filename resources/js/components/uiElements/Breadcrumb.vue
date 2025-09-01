<template>
  <div v-if="breadcrumb"
    class="mb-4 text-tiny hidden sm:inline-flex items-center gap-3 text-gray-500 shadow rounded-full px-8 pl-2 py-1.5">
    <span :class="[
      isLightColor
        ? 'text-custom-700 bg-custom-200'
        : 'text-custom-500 bg-custom-50',
      'material-symbols-outlined text-[20px] p-1.5 rounded-full',
    ]">
      {{ breadcrumb.icon }}
    </span>

    <div class="flex items-center gap-3" v-for="page in breadcrumb.pages" :key="page.name">
      <span class="material-symbols-outlined text-[20px]"> chevron_right </span>
      <router-link 
        v-if="page.path" 
        :to="page.path" 
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'capitalize',
          'max-w-72 truncate',
        ]"
      >
        {{ page.name }}
      </router-link>
      <span v-else :class="[
        isLightColor ? 'text-custom-700' : 'text-custom-500',
        'capitalize',
        'max-w-72 truncate',
      ]">
        {{ page.name }}
      </span>
    </div>
  </div>
</template>

<script>
export default {
  props: ["breadcrumb"],
};
</script>
