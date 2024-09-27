<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="xl:flex justify-between items-center gap-5">
    <div class="text-xl flex xl:w-2/5 whitespace-nowrap font-medium">
      Admin Activities
    </div>
    <div class="flex-1 mt-4 xl:mt-0">
      <div class="sm:flex gap-5">
        <div class="flex-1">
          <div class="grid sm:grid-cols-3 grid-cols-1 gap-5">
            <div class="">
              <select
                class="w-full text-sm border-gray-200 bg-white text-gray-700 rounded-md focus:outline-none"
                v-model="current.user"
              >
                <option value="">Select User</option>
                <option :value="user.id" v-for="user in users" :key="user.id">
                  {{ truncate(user.name, 26) }}
                </option>
              </select>
            </div>
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
        <div class="flex justify-end items-center gap-5 mt-5 sm:mt-0">
          <div>
            <button
              v-tooltip="'Filter'"
              type="button"
              @click="
                (pagination.current_page = 1),
                  fetchAdminActivity(pagination.current_page)
              "
              class="bg-[#F6F6F6] items-center flex gap-1.5 border-gray-200 border rounded-lg text-gray-500 text-sm p-1.5 text-center"
            >
              <span class="material-symbols-outlined text-[22px]">
                filter_alt
              </span>
            </button>
          </div>
          <div v-if="current.type || current.action || current.user">
            <button
              v-tooltip="'Clear Filter'"
              @click="clearFilter"
              type="button"
              class="bg-[#F6F6F6] items-center flex border-gray-200 border rounded-lg text-gray-500 text-sm p-1.5 text-center"
            >
              <span class="material-symbols-outlined text-[22px]">
                filter_alt_off
              </span>
            </button>
          </div>
          <div>
            <button
              @click="fetchAdminActivity(pagination.current_page)"
              type="button"
              class="bg-[#F6F6F6] items-center flex border-gray-200 border rounded-lg text-gray-500 text-sm p-1.5 text-center"
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
        <td
          class="whitespace-nowrap text-tiny py-5 px-4 font-medium truncate pl-10 max-w-[300px] flex"
        >
          <span
            v-tooltip="admin_activity.user && admin_activity.user.name"
            :class="[
              isLightColor ? 'text-custom-700' : 'text-custom-500',
              'truncate',
            ]"
            >{{ admin_activity.user ? admin_activity.user.name : "-" }}</span
          >
        </td>

        <td class="whitespace-nowrap text-[#2c3138] py-5 px-4">
          {{ admin_activity.on }}
        </td>
        <td class="whitespace-nowrap text-[#2c3138] py-5 px-4">
          {{ admin_activity.action ? admin_activity.action : "-" }}
        </td>
        <td
          class="whitespace-nowrap text-[#2c3138] py-5 px-4 truncate max-w-[300px]"
        >
          <span v-tooltip="admin_activity.content">
            {{ admin_activity.content ? admin_activity.content : "-" }}
          </span>
        </td>
        <td
          class="whitespace-nowrap text-[#2c3138] py-5 px-4 truncate max-w-[300px]"
        >
          {{ admin_activity.ip }}
        </td>
        <td class="whitespace-nowrap text-[#2c3138] py-5 px-4">
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
      <TableSkeleton :heads="6" v-if="refreshing" />
      <Table :head="thead" v-else>
        <tr>
          <td colspan="6" class="text-center text-sm px-6 py-5">
            {{ refreshing ? "Please Wait" : "No Activities Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>
</template>

<script>
export default {
  name: "AdminActivities",
  data() {
    return {
      breadcrumb: {
        title: "User Management",
        icon: "groups",
        pages: [
          {
            name: "Activities",
          },
        ],
      },
      pagination: null,
      processing: false,
      thead: [
        {
          title: "User",
          classes: "font-medium text-tiny pl-10",
        },
        {
          title: "Type",
          classes: "font-medium text-tiny",
        },
        {
          title: "Action",
          classes: "font-medium text-tiny",
        },
        {
          title: "Activity",
          classes: "font-medium text-tiny",
        },
        {
          title: "IP Address",
          classes: "font-medium text-tiny",
        },
        {
          title: "Date & Time",
          classes: "font-medium text-tiny",
        },
      ],
      activities: [],
      types: [],
      users: [],
      actions: [],
      current: {
        type: "",
        action: "",
        user: "",
      },
      filter: {
        user: "",
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
  methods: {
    truncate(text, length) {
    return text.length > length ? text.substring(0, length) + '...' : text;
  },
    clearFilter() {
      this.current.user = "";
      this.current.type = "";
      this.current.action = "";
      this.fetchAdminActivity();
    },

    async fetchAdminActivity(page = "") {
      this.refreshing = true;
      let url = `/admin/activities?page=${page}&per_page=${this.per_page}&type=${this.current.type}&action=${this.current.action}&user=${this.current.user}`;
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.activities = data.activities.data;
          this.users = data.users;
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

    handlePerPageChange() {
      this.fetchAdminActivity(1);
    },
    handlePageChange(newPage) {
      this.fetchAdminActivity(newPage);
    },
  },
  mounted() {
    this.fetchAdminActivity();
  },
};
</script>
