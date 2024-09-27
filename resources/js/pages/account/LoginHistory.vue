 <template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="flex gap-3 justify-between items-center">
    <h1 class="text-xl text-[#31363f] font-medium">Login History</h1>
    <button
      @click="loginHistory(pagination.current_page)"
      :class="[
        textColorClass,
        'bg-gray-50 p-1.5 border rounded-md flex items-center ',
      ]"
    >
      <span
        :class="[
          refreshing ? 'fa-spin' : '',
          'material-symbols-outlined text-[22px] text-gray-400',
        ]"
      >
        refresh
      </span>
    </button>
  </div>
  <div class="h-full mt-5">
    <Table :head="thead" v-if="login.length > 0" :bodyPadding="'px-5'">
      <tr
        class="border-b border-gray-200 text-[#2c3138] text-sm"
        v-for="item in login"
        :key="item"
      >
        <td class="whitespace-nowrap text-left py-5 px-4 pl-10">
          {{ item.ip }}
        </td>
        <td class="whitespace-nowrap py-5 px-4 truncate max-w-[300px]">
          <span v-tooltip="item.browser_agent" class="">
            {{ item.browser_agent ? item.browser_agent : "-" }}
          </span>
        </td>
        <td
          class="whitespace-nowrap py-5 px-4 truncate sm:max-w-40 sm:min-w-20 sm:pl-14 sm:text-center text-nowrap"
        >
          {{ item.created_at ? item.created_at : "-" }}
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
      <TableSkeleton :heads="5" v-if="refreshing" />
      <Table :head="thead" v-else>
        <tr>
          <td colspan="6" class="text-center text-sm px-6 py-5">
            {{ refreshing ? "Please Wait" : "No Data Found" }}
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
        title: "Account",
        icon: "account_box",
        pages: [{ name: "Login History" }],
      },
      login: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      refreshing: false,
      per_page: 10,
      thead: [
        {
          title: "IP Address",
          classes: "text-nowrap  pl-10",
        },
        {
          title: "Browser Agent",
          classes: "text-nowrap",
        },
        {
          title: "Date & Time ",
          classes: "text-nowrap  sm:text-center",
        },
      ],
    };
  },

  methods: {
    async loginHistory(page = 1) {
      this.refreshing = true;
      let url = `/login-history?page=${page}&per_page=${this.per_page}`;
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
    handlePerPageChange() {
      this.loginHistory(1);
    },
    handlePageChange(newPage) {
      this.loginHistory(newPage);
    },
  },
  mounted() {
    this.loginHistory();
  },
};
</script>
