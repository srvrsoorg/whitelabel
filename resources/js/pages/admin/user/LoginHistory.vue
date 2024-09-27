<template>
  <div class="flex gap-3 justify-between items-center pt-5">
    <h1 class="text-xl text-[#31363f] font-medium">Login History</h1>

    <button
      @click="fatchloginHistory(pagination.current_page)"
      type="button"
      class="bg-[#F6F6F6] items-center flex border-primary border rounded-lg text-gray-500 text-sm p-2 text-center"
    >
      <span
        :class="[
          refreshing ? 'fa-spin' : '',
          'material-symbols-outlined text-[22px] ',
        ]"
      >
        refresh
      </span>
    </button>
  </div>

  <div class="h-full mt-5">
    <Table :head="thead" v-if="login.length > 0">
      <tr
        class="border-b border-primary text-[#2c3138] text-sm"
        v-for="item in login"
        :key="item"
      >
        <td class="whitespace-nowrap pl-10 text-left py-4 px-4">
          {{ item.ip }}
        </td>
        <td class="whitespace-nowrap py-4 px-4 truncate max-w-[300px]">
          <span v-tooltip="item.browser_agent">
            {{ item.browser_agent ? item.browser_agent : "-" }}
          </span>
        </td>
        <td class="whitespace-nowrap py-4 px-4 text-left">
          {{ item.created_at }}
        </td>
      </tr>
      <template #pagination>
        <div
          v-if="pagination.total > 10"
          :class="[
            pagination.total > 10 ? 'justify-between' : 'justify-end',
            'sm:flex gap-3  py-5 px-4',
          ]"
        >
          <div v-if="pagination.total > 10">
            <Perpage v-model="per_page" @changePage="handlePerPageChange" />
          </div>
          <div v-if="login.length > 0" class="mt-5 sm:mt-0">
            <Pagination
              :pagination="pagination"
              @page-change="handlePageChange"
            />
          </div>
        </div>
      </template>
    </Table>
    <template v-else>
      <TableSkeleton :heads="3" v-if="refreshing" />
      <Table :head="thead" v-else>
        <tr>
          <td colspan="3" class="text-center text-sm px-6 py-5">
            {{ refreshing ? "Please Wait" : "No Login History Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>
</template>

<script>
export default {
  name: "LoginHistory",

  data() {
    return {
      breadcrumb: {
        title: "User",
        icon: "groups",
        pages: [{ name: "Login History" }],
      },
      refreshing: false,
      openConfirmation: false,
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      per_page: 10,
      thead: [
        { title: "IP Address", classes: "text-nowrap pl-10" },
        { title: "Browser Agent", classes: "text-nowrap " },
        { title: "Date & Time", classes: "text-nowrap " },
      ],
      login: [],
    };
  },
  methods: {
    async fatchloginHistory(page = 1) {
      const id = this.$route.params.user;
      this.refreshing = true;
      let url = `/admin/users/${id}/login-history?page=${page}&per_page=${this.per_page}`;
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.login = data.logins.data;
          this.pagination = {
            current_page: data.logins.current_page,
            last_page: data.logins.last_page,
            per_page: data.logins.per_page,
            total: data.logins.total,
            next_page_url: data.logins.next_page_url,
            prev_page_url: data.logins.prev_page_url,
          };
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.refreshing = false;
        });
    },

    handlePageChange(newPage) {
      this.fatchloginHistory(newPage);
    },
    handlePerPageChange() {
      this.fatchloginHistory(1);
    },
  },
  mounted() {
    this.fatchloginHistory();
    this.$emit("pass-breadcrumb", this.breadcrumb);
  },
};
</script>
