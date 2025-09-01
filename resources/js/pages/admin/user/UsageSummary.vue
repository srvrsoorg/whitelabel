<template>
  <div class="flex gap-3 justify-between items-center pt-5">
    <h1
      class="text-xl flex gap-3 text-[#31363f] justify-between items-center font-medium"
    >
      Usage Summary
    </h1>
    <div class="flex gap-5">
      <button
        @click="fetchUsageSummary(pagination.current_page)"
        type="button"
        class="bg-[#F6F6F6] items-center flex border-primary border rounded-lg text-gray-500 text-sm p-1.5 text-center"
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
  </div>
  <div class="h-full mt-5">
    <Table :head="thead" v-if="usageSummary.length > 0">
      <tr
        class="border-b border-primary text-[#2c3138] text-sm"
        v-for="usage in usageSummary"
        :key="usage.id"
      >
        <td class="py-3 px-5 pl-10 font-medium">#{{ usage.id }}</td>
        <td class="px-4 py-3">
          <div class="flex flex-col max-w-[200px] truncate">
            <span
              class="font-medium pb-1.5 text-tiny truncate"
              v-tooltip="usage.server_name"
              >{{ usage.server_name }}</span
            >
            <p class="text-gray-500">{{ usage.server_ip }}</p>
          </div>
        </td>
        <td class="px-4 py-3">
          <p class="">
            {{
              usage.subscription
                ? formatCurrency(usage.subscription.monthly_price)
                : "-"
            }}
          </p>
        </td>
        <td class="px-4 py-3">
          <div class="">
            <span class="block">{{
              formatCurrency(usage.deduct_amount)
            }}</span>
            <span class="text-gray-500">{{ usage.hours }} Hr</span>
          </div>
        </td>
        <td class="px-4 py-3">
          <p class="">{{ usage.last_deduct_at }}</p>
        </td>
        <td class="px-4 py-3">
          <div class="flex items-center whitespace-nowrap">
            <span class="">{{ usage.created_at }}</span>
          </div>
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
          <div v-if="usageSummary.length > 0" class="mt-5 sm:mt-0">
            <Pagination
              :pagination="pagination"
              @page-change="handlePageChange"
            />
          </div>
        </div>
      </template>
    </Table>
    <template v-else>
      <TableSkeleton :heads="6" v-if="refreshing" />
      <Table :head="thead" v-else>
        <tr>
          <td colspan="6" class="text-center px-6 py-4 text-sm">
            {{ refreshing ? "Please Wait" : "No Summary Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>
</template>

<script>
export default {
  data() {
    return {
      breadcrumb: {
        // title: "User",
        icon: "groups",
        pages: [{ name: "Usage Summary" }],
      },
      usageSummary: [],
      thead: [
        { title: "ID", classes: "text-nowrap pl-10" },
        { title: "Server Details", classes: "" },
        { title: "Price per Month", classes: "" },
        { title: "Usage", classes: "" },
        { title: "Last Deducted At", classes: "" },
        { title: "Started At", classes: "" },
      ],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      current_page: 1,
      per_page: 10,
      processing: false,
      refreshing: false,
      openConfirmation: false,
    };
  },
  mounted() {
    this.fetchUsageSummary();
    this.$emit("pass-breadcrumb", this.breadcrumb);
  },
  methods: {
    async fetchUsageSummary(page = 1) {
      this.refreshing = true;
      await this.$axios
        .get(
          `/admin/users/${this.$route.params.user}/usage-summaries?page=${page}&per_page=${this.per_page}`
        )
        .then(({ data }) => {
          this.usageSummary = data.usageSummaries.data;
          this.pagination = {
            current_page: data.usageSummaries.current_page,
            last_page: data.usageSummaries.last_page,
            per_page: data.usageSummaries.per_page,
            total: data.usageSummaries.total,
            next_page_url: data.usageSummaries.next_page_url,
            prev_page_url: data.usageSummaries.prev_page_url,
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
      this.fetchUsageSummary(newPage);
    },

    handlePerPageChange() {
      this.fetchUsageSummary(1);
    },
  },
};
</script>
