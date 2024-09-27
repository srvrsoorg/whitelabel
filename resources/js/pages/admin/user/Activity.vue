<template>
  <div class="xl:flex gap-3 justify-between items-center my-5">
    <h1
      class="text-xl flex gap-3 text-[#31363f] xl:w-2/5 justify-between font-medium"
    >
      Activities
    </h1>
    <div class="flex-1 xl:mt-0 mt-5">
      <div class="sm:flex gap-5">
        <div class="flex-1">
          <div class="grid sm:grid-cols-2 grid-cols-1 gap-5">
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
        <div class="flex justify-end gap-5 sm:mt-0 mt-5">
          <div>
            <button
              v-tooltip="'Filter'"
              type="button"
              @click="
                pagination.current_page = 1;
                fetchActivity(pagination.current_page);
              "
              class="bg-[#F6F6F6] items-center flex gap-1.5 border-gray-200 border rounded-lg text-gray-500 text-sm p-2 text-center"
            >
              <span class="material-symbols-outlined text-[22px]">
                filter_alt
              </span>
            </button>
          </div>
          <div v-if="current.type || current.action">
            <button
              v-tooltip="'Clear Filter'"
              @click="clearFilter()"
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
        class="border-b border-primary text-[#2c3138] text-sm"
        v-for="admin_activity in activities"
        :key="admin_activity"
      >
        <td class="whitespace-nowrap py-4 px-4" v-if="admin_activity.user"></td>
        <td class="whitespace-nowrap py-4 px-4 pl-10">
          {{ admin_activity.ip }}
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          {{ admin_activity.on }}
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          {{ admin_activity.action ? admin_activity.action : "-" }}
        </td>
        <td>
          <span
            v-tooltip="admin_activity.content"
            class="whitespace-nowrap py-4 px-4 truncate max-w-[300px]"
          >
            {{ admin_activity.content ? admin_activity.content : "-" }}
          </span>
        </td>
        <td class="whitespace-nowrap px-4 py-4">
          {{ admin_activity.created_at ? admin_activity.created_at : "-" }}
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
        title: "User",
        icon: "groups",
        pages: [{ name: "Activities" }],
      },
      processing: false,
      thead: [
        { title: "IP Address", classes: "whitespace-nowrap pl-10" },
        "Type",
        "Action",
        "Description",
        { title: "Date & Time", classes: "whitespace-nowrap" },
      ],
      activities: [],
      types: [],
      users: [],
      actions: [],
      filter: {
        types: "",
        action: "",
      },
      current: {
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
      open: false,
    };
  },
  methods: {
    clearFilter() {
      this.current.type = "";
      this.current.action = "";
      this.pagination.current_page = 1;
      this.fetchActivity();
    },

    async fetchActivity(page = 1) {
      const id = this.$route.params.user;
      this.refreshing = true;
      let url = `/admin/users/${id}/activities?page=${page}&per_page=${this.per_page}&type=${this.current.type}&action=${this.current.action}`;
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.actions = data.actions;
          this.types = data.types;
          this.activities = data.activities.data;
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
    this.$emit("pass-breadcrumb", this.breadcrumb);
  },
};
</script>
