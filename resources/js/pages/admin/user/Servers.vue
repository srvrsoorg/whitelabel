<template>
  <div class="sm:flex gap-3 justify-between items-center pt-5">
    <h1
      class="text-xl flex gap-3 justify-between text-[#31363f] items-center font-medium"
    >
      Servers
    </h1>
    <div class="flex gap-5 mt-2 sm:mt-0">
      <div class="relative w-full">
        <span
          class="absolute inset-y-0 right-0 flex items-center p-2 border-primary border bg-gray-50 text-gray-500 rounded-r-md"
        >
          <span class="material-symbols-outlined text-[22px]"> search </span>
        </span>
        <input
          v-model="search"
          @input="handleSearch()"
          type="text"
          name="text"
          id="text"
          class="block w-full sm:min-w-[350px] rounded-md border border-primary focus:border-neutral-300 py-2 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          placeholder="Search"
        />
      </div>

      <button
        @click="fetchServers(pagination.current_page)"
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
  </div>

  <div class="h-full mt-5">
    <Table :head="thead" v-if="servers.length > 0">
      <tr
        class="border-b border-primary text-[#2c3138] text-sm"
        v-for="server in servers"
        :key="server.id"
      >
        <td class="whitespace-nowrap py-4 px-4 pl-10">
          <p>#{{ server.id }}</p>
        </td>
        <td class="whitespace-nowrap py-4 px-4 text-sm min-w-[280px]">
          <div class="flex gap-3 items-center">
            <template v-if="CloudLogo && server.cloud_provider">
              <img
                :src="CloudLogo[server.cloud_provider.provider].logo"
                class="w-7 h-auto"
                v-tooltip="`${CloudLogo[server.cloud_provider.provider].title}`"
              />
            </template>
            <template v-else-if="hasOneCloudProvider">
              <div :class="'w-7 min-w-7 max-w-7'"></div>
            </template>
            <div class="">
              <router-link
                :to="{
                  name: 'serverDetails',
                  params: {
                    server: server.id,
                  },
                }"
                class="flex items-center"
              >
                <p
                  class="truncate font-medium text-tiny max-w-[200px]"
                  v-tooltip="`${server.name}`"
                >
                  {{ server.name }}
                </p>
              </router-link>
              <span class="text-gray-500">
                {{ server.created_at ? server.created_at : "-" }}
              </span>
            </div>
          </div>
        </td>
        <td class="whitespace-nowrap py-4 px-4 items-center">
          <div
            class="text-tiny cursor-pointer inline-flex"
            @click="copyToClipboard(server.ip)"
          >
            <span v-tooltip="server.ip">
              {{ server.ip }}
            </span>
            <span
              class="material-symbols-outlined px-1 text-blue-500 cursor-pointer text-[16px] py-1"
            >
              content_copy
            </span>
          </div>
        </td>

        <td class="whitespace-nowrap py-4 px-4 text-center">
          <div class="max-w-fit mx-auto" v-if="service">
            <img
              v-tooltip="service[server.web_server].title"
              :src="service[server.web_server].logo"
              :class="['w-auto', server.web_server == 'mern' ? 'h-4' : 'h-8']"
            />
          </div>
        </td>
        <td class="whitespace-nowrap py-2 px-4 text-center">
          <div class="flex justify-center">
            <img
              v-if="server.database_type === 'mysql'"
              src="/logo/mysql.svg"
              class="w-auto h-6 mx-auto"
              v-tooltip="`MySQL`"
            />
            <img
              v-else-if="server.database_type === 'mongodb'"
              src="/logo/mongodb.png"
              class="w-auto h-6"
              v-tooltip="`MongoDB`"
            />
            <img
              v-else
              src="/service/mariadb-logo.png"
              class="w-auto h-6"
              v-tooltip="`MariaDB`"
            />
          </div>
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          <p>{{ server.ssh_port ? server.ssh_port : "-" }}</p>
        </td>

        <td class="whitespace-nowrap py-9 px-4 items-center flex">
          <p
            v-if="server.agent_status == '1' || server.agent_status == '0'"
            :class="[
              server.agent_status == '1' ? 'text-green-600' : 'text-red-500',
              'text-xs items-center flex gap-1 ',
            ]"
          >
            <span class="material-symbols-outlined text-[18px]">
              {{ server.agent_status == "1" ? " task_alt" : "cancel" }}</span
            >
            {{ server.agent_status == "1" ? "Connected" : "Disconnect" }}
          </p>
          <p v-else class="flex items-center gap-1 text-sm text-gray-600">
            <span class="material-symbols-outlined text-[20px]"> cached </span>
            <span
              class="max-w-24 truncate capitalize"
              v-tooltip="server.agent_status"
              >{{ server.agent_status }}</span
            >
          </p>
        </td>

        <td class="whitespace-nowrap py-4 px-4 text-center">
          <div class="flex justify-center items-cener gap-3">
            <router-link
              :to="{
                name: 'serverDetails',
                params: {
                  server: server.id,
                },
              }"
            >
              <span
                v-tooltip="'View'"
                :class="[
                  isLightColor
                    ? 'text-custom-700 bg-custom-200'
                    : 'bg-custom-50 text-custom-500',
                ]"
                class="material-symbols-outlined p-1.5 rounded-md items-center text-[20px]"
              >
                visibility
              </span>
            </router-link>
            <button
              v-tooltip="'Delete'"
              @click="deleteServers(server)"
              :class="[
                'material-symbols-outlined p-1.5 rounded-md text-[20px] bg-red-50',
                isLightColor ? 'text-red-700' : 'text-red-500',
              ]"
            >
              delete
            </button>
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
            <Perpage v-model="per_page" :initialPerPage="pagination.per_page" @changePage="handlePerPageChange" />
          </div>
          <div v-if="servers.length > 0" class="mt-5 sm:mt-0">
            <Pagination
              :pagination="pagination"
              @page-change="handlePageChange"
            />
          </div>
        </div>
      </template>
    </Table>
    <template v-else>
      <TableSkeleton :heads="8" v-if="refreshing" />
      <Table :head="thead" v-else>
        <tr>
          <td colspan="8" class="text-center text-sm px-6 py-4">
            {{ refreshing ? "Please Wait" : "No Servers Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>

  <Confirmation
    @closeModal="openConfirmation = false"
    :show="openConfirmation"
    :showLoader="showLoader"
    :confirmationTitle="'Delete Server'"
    :submitBtnTitle="`Yes I'm sure`"
    @confirm="deleteServer"
  >
    <template #icon>
      <span class="material-symbols-outlined text-red-500 text-[22px]"
        >dns</span
      >
    </template>
    <template v-slot:content
      ><span class="text-tiny text-gray-600"
        >Are you Sure you want to delete this server?
      </span>
      <div class="pt-1 xs:flex items-center gap-2 my-5">
        <p class="text-tiny font-medium text-gray-800 min-w-52">Server Name</p>
        <p class="text-gray-500 text-tiny truncate">
          {{ serverData ? serverData.name : "-" }}
        </p>
      </div>
      <div class="xs:flex items-center gap-2 my-5">
        <p class="text-tiny font-medium text-gray-800 min-w-52">Server IP</p>
        <p class="text-gray-500 text-tiny truncate">
          {{ serverData ? serverData.ip : "-" }}
        </p>
      </div>
      <hr />
      <p class="my-5 text-tiny font-medium">
        Once you deleted the server all data associated with it will be deleted
        and it is not possible to recover it later.
      </p>
    </template>
  </Confirmation>
</template>

<script>
import CloudLogo from "@/CloudProviders";
import service from "@/Services";
export default {
  name: "Servers",
  data() {
    return {
      breadcrumb: {
        title: "User",
        icon: "groups",
        pages: [{ name: "Servers" }],
      },
      refreshing: false,
      CloudLogo: CloudLogo,
      service: service,
      openConfirmation: false,
      showLoader: false,
      serverData: null,
      pagination: null,
      processing: false,
      servers: [],
      search: "",
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
      thead: [
        {
          title: "ID",
          classes: "pl-10",
        },

        {
          title: "Server Name",
          classes: "whitespace-nowrap",
        },

        {
          title: "IP",
          classes: "",
        },
        {
          title: "Web Server",
          classes: "text-center whitespace-nowrap",
        },
        {
          title: "Database",
          classes: "text-center",
        },
        {
          title: "SSH Port",
          classes: " whitespace-nowrap",
        },
        {
          title: "Status",
          classes: "",
        },
        {
          title: "Actions",
          classes: "text-center",
        },
      ],
    };
  },
  computed: {
    hasOneCloudProvider() {
      return this.servers.some((server) => server.cloud_provider !== null);
    },
  },
  methods: {
    async fetchServers(page = 1) {
      this.refreshing = true;
      let url = `/admin/servers?user_id=${this.$route.params.user}&page=${page}&per_page=${this.per_page}&search=${this.search}`;
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.servers = data.servers.data;
          this.pagination = {
            current_page: data.servers.current_page,
            last_page: data.servers.last_page,
            per_page: data.servers.per_page,
            total: data.servers.total,
            next_page_url: data.servers.next_page_url,
            prev_page_url: data.servers.prev_page_url,
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
      this.fetchServers(newPage);
    },
    handlePerPageChange() {
      this.fetchServers(1);
    },
    deleteServers(server) {
      this.serverData = server;
      this.openConfirmation = true;
    },
    async deleteServer() {
      this.showLoader = true;
      await this.$axios
        .delete(`/admin/servers/${this.serverData.id}`)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.openConfirmation = false;
          this.fetchServers();
        })
        .catch(({ response }) => {
          this.openConfirmation = false;
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.showLoader = false;
        });
    },
  },
  mounted() {
    this.fetchServers();
    this.handleSearch = this.$debounce(this.fetchServers, 1000);
    this.$emit("pass-breadcrumb", this.breadcrumb);
  },
};
</script>
