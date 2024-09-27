<template>
  <Table :head="headers">
    <tr v-for="i in rows" :key="i">
      <td class="px-4 py-3" v-for="j in heads" :key="j">
        <Skeleton :count="1" />
      </td>
    </tr>
  </Table>
</template>

<script>
export default {
  props: {
    rows: {
      type: Number,
      default: 5,
    },
    heads: {
      type: Number,
      default: 3,
    },
  },
  computed: {
    headers() {
      let arr = [];
      for (let i = 0; i < this.heads; i++) {
        arr[i] = null;
      }
      return arr;
    },
  },
};
</script>
