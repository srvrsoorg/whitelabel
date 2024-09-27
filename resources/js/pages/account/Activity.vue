<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="md:flex justify-between items-center md:gap-5 gap-3">
    <div class="text-xl text-[#31363f] xl:w-2/5 min-w-32 font-medium">
      Activities
    </div>
    <div class="flex-1">
      <div class="sm:flex gap-5 mt-5 md:mt-0">
        <div class="flex-1">
          <div class="grid sm:grid-cols-2 grid-cols-1 sm:gap-5 gap-3">
            <div class="">
              <select
                class="w-full block rounded-lg border border-gray-200 focus:border-gray-300 py-1.5 ring-gray-200 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                v-model="current.type"
              >
                <option value="">All Type</option>
                <option
                  :value="type_value"
                  v-for="(type_value, type_key) in types"
                  :key="type_key"
                >
                  {{ type_value }}
                </option>
              </select>
            </div>
            <div class="">
              <select
                class="w-full block rounded-lg border border-gray-200 focus:border-gray-200 py-1.5 ring-gray-200 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                v-model="current.action"
              >
                <option value="">All Action</option>
                <option
                  :value="action"
                  v-for="(action, key) in actions"
                  :key="key"
                >
                  {{ action }}
                </option>
              </select>
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-5 mt-5 sm:mt-0">
          <div>
            <button
              v-tooltip="'Filter'"
              type="button"
              @click="
                (pagination.current_page = 1),
                  fetchActivity(pagination.current_page)
              "
              class="bg-[#F6F6F6] items-center flex border-gray-200 border rounded-lg text-gray-500 text-sm p-2 text-center"
            >
              <span class="material-symbols-outlined text-[22px]">
                filter_alt
              </span>
            </button>
          </div>

          <div v-if="current.type || current.action">
            <button
              v-tooltip="'Clear Filter'"
              @click="clearFilter"
              type="button"
              class="bg-[#F6F6F6] items-center flex border-gray-200 border rounded-lg text-gray-500 text-sm p-2 text-center"
            >
              <span class="material-symbols-outlined text-[22px]">
                filter_alt_off
              </span>
            </button>
          </div>
          <div>
            <button
              @click="fetchActivity(pagination.current_page)"
              type="button"
              class="bg-[#F6F6F6] items-center flex border-gray-200 border rounded-lg text-gray-500 text-sm p-2 text-center"
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
      </div>
    </div>
  </div>

  <div class="h-full mt-5">
    <Table :head="thead" v-if="activities.length > 0">
      <tr
        class="border-b border-gray-200 text-[#2c3138] text-sm"
        v-for="user_activity in activities"
        :key="user_activity"
      >
        <td class="whitespace-nowrap py-4 px-4 pl-10">
          {{ user_activity.ip }}
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          {{ user_activity.on }}
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          {{ user_activity.action ? user_activity.action : "-" }}
        </td>
        <td class="whitespace-nowrap py-4 px-4 max-w-[280px] truncate">
          <span v-tooltip="user_activity.content">
            {{ user_activity.content ? user_activity.content : "-" }}
          </span>
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          {{ user_activity.created_at ? user_activity.created_at : "-" }}
        </td>
      </tr>
      <template #pagination>
        <div
          v-if="pagination.total > 10"
          :class="[
            pagination.total > 10 ? 'justify-between' : 'justify-end',
            'sm:flex gap-3 py-5 px-4',
          ]"
        >
          <div v-if="pagination.total > 10">
            <Perpage v-model="per_page" :initialPerPage="pagination.per_page" @changePage="handlePerPageChange" />
          </div>
          <div v-if="activities.length > 0" class="mt-5 sm:mt-0">
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
          <td colspan="5" class="text-center text-sm px-6 py-5">
            {{ refreshing ? "Please Wait" : "No Activities Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>
</template>

<script>
export default {
  name: "Activity",
  data() {
    return {
      breadcrumb: {
        title: "Account",
        icon: "account_box",
        pages: [{ name: "Activities" }],
      },
      thead: [
        { title: "IP Address", classes: "text-nowrap pl-10" },
        { title: "Type" },
        { title: "Action" },
        { title: "Description" },
        { title: "Date & Time"},
      ],
      activities: [],
      types: [],
      actions: [],
      current: {
        type: "",
        action: "",
      },
      filter: {
        type: "",
        action: "",
      },
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      per_page: 10,
      refreshing: false,
    };
  },

  computed: {
    isFilterActive() {
      return this.current.type !== "" || this.current.action !== "";
    },
  },

  methods: {
    clearFilter() {
      this.current.type = "";
      this.current.action = "";
      this.fetchActivity();
    },
    async fetchActivity(page = "") {
      this.refreshing = true;
      let url = `/activities?page=${page}&per_page=${this.per_page}&type=${this.current.type}&action=${this.current.action}`;
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.activities = data.activities.data;
          this.types = data.types;
          this.actions = data.actions;

          this.pagination = {
            current_page: data.activities.current_page,
            last_page: data.activities.last_page,
            per_page: data.activities.per_page,
            total: data.activities.total,
            next_page_url: data.activities.next_page_url,
            prev_page_url: data.activities.prev_page_url,
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
      this.fetchActivity(newPage);
    },
    handlePerPageChange() {
      this.fetchActivity(1);
    },
  },
  mounted() {
    this.fetchActivity();
  },
};
</script>
