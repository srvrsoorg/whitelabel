<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="sm:flex items-center gap-3 justify-between">
    <p class="text-xl font-medium text-[#31363f]">Servers</p>
    <div class="mt-3 sm:mt-0 flex items-center gap-5">
      <div class="relative w-full">
        <input
          v-model="search"
          @input="handleSearch()"
          type="text"
          name="text"
          id="text"
          class="block w-full sm:min-w-[350px] rounded-md border border-primary focus:border-neutral-300 py-2 text-gray-800 leading-6 ring-gray-300 placeholder:text-gray-400 text-sm focus:ring-0"
          placeholder="Search"
        />
        <button
          v-if="search"
          type="button"
          @click="clearSearch"
          class="border border-primary bg-[#F6F6F6] absolute inset-y-0 text-gray-500 right-8 flex items-center px-2.5"
        >
          <span v-tooltip="'Clear'" class="material-symbols-outlined text-[20px]">close</span>
        </button>
        <div
          :class="[
            textColorClass,
            'pointer-events-none absolute rounded-r-md inset-y-0 bg-[#F6F6F6] border border-primary -right-1 items-center justify-center flex p-1.5',
          ]"
        >
          <span class="material-symbols-outlined text-[22px] text-gray-400">
            search
          </span>
        </div>
      </div>
      <div class="flex items-center gap-5">
        <button
          @click="fetchServers(pagination.current_page)"
          type="button"
          class="bg-[#F6F6F6] items-center flex border-gray-200 border rounded-md text-gray-500 text-sm p-2 text-center"
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

  <div class="h-full mt-5">
    <Table :head="thead" v-if="servers.length > 0">
      <tr
        class="border-b border-primary text-[#2c3138] text-sm"
        v-for="server in servers"
        :key="server.id"
      >
        <td class="whitespace-nowrap py-2 px-4 pl-10">#{{ server.id }}</td>
        <td
          class="whitespace-nowrap py-4 px-4 max-w-[300px] min-w-[280px] text-sm"
        >
          <div class="flex gap-3 items-center">
            <template v-if="CloudLogos && server.cloud_provider">
              <img
                :src="CloudLogos[server.cloud_provider.provider].logo"
                class="w-7 h-auto"
                v-tooltip="
                  `${CloudLogos[server.cloud_provider.provider].title}`
                "
              />
            </template>
            <template v-else-if="hasOneCloudProvider">
              <div :class="'w-7 min-w-7 max-w-7'"></div>
            </template>
            <div class="">
              <div class="flex items-center gap-1">
               
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
              <span
                class="relative flex items-center justify-center"
                v-tooltip.top="`${server.agent_status == '1' ? 'Connected' : server.agent_status == '0' ? 'Not Connected' : server.agent_status}`"
                >                                                   
                <span
                  v-if="server.agent_status == '1' || server.agent_status == '0'"
                  class="absolute inline-flex h-[9px] w-[9px] animate-ping rounded-full"
                  :class="server.agent_status == '1' ? 'bg-green-400 opacity-75' : 'bg-red-400 opacity-75'"
                ></span>                                         
                <span
                  class="relative inline-flex h-[9px] w-[9px] rounded-full"
                  :class="server.agent_status == '1' ? 'bg-green-600' : server.agent_status == '0' ? 'bg-red-500' : 'bg-gray-500'"
                ></span>
            </span>
            </div>
              <div class="flex">
                <span class="text-gray-500 py-0.5 truncate">
                  {{ server.ip }}
                </span>
                <span
                  @click="copyToClipboard(server.ip)"
                  class="material-symbols-outlined cursor-pointer text-blue-500 text-[16px] px-1 py-1"
                >
                  content_copy
                </span>
              </div>
              <span class="text-gray-500">
                {{ server.created_at ? server.created_at : "-" }}
              </span>
            </div>
          </div>
        </td>

        <td class="whitespace-nowrap py-2 truncate px-4 max-w-[300px]">
          <div class="flex flex-col" v-if="server.user">
            <router-link
              :to="{
                name: 'userProfile',
                params: {
                  user: server.user.id,
                },
              }"
              class="flex items-center"
            >
              <p
                class="truncate font-medium text-tiny max-w-[250px]"
                v-tooltip="`${server.user.name}`"
              >
                {{ server.user.name ? server.user.name : "-" }}
              </p>
            </router-link>
            <div
              class="flex items-center cursor-pointer"
              @click="copyToClipboard(server.user.email)"
            >
              <span class="max-w-40 truncate" v-tooltip="server.user.email">
                {{ server.user.email ? server.user.email : "-" }}
              </span>
              <span
                class="material-symbols-outlined cursor-pointer text-blue-500 text-[16px] pl-1"
              >
                content_copy
              </span>
            </div>
          </div>
          <span v-else>-</span>
        </td>
        <td class="whitespace-nowrap py-2 px-4 truncate max-w-[100px]">
          <div class="text-gray-500" v-if="server.plan">
            <div class="flex items-center gap-2.5">
              <div class="flex items-center gap-0.5">
              <span class="material-symbols-outlined text-[18px]" v-tooltip="'CPU'">
                database
              </span>
              <p>{{ server.plan.bandwidth }} {{ server.cloud_provider.provider == 'hetzner'?'TB':'GB' }}</p>
              </div>
            <div class="flex items-center gap-0.5">
              <span class="material-symbols-outlined text-[20px]" v-tooltip="'RAM'">
                memory
              </span>
              <p class="">{{ server.plan.ram }} MB</p>
            </div>

              <div class="flex items-center gap-0.5">
              <span class="material-symbols-outlined text-[18px]" v-tooltip="'Disk'">
                sd_card
              </span>
              <p>{{ server.plan.disk }} GB</p>
            </div>
            </div>
          </div>
          <div v-else>-</div>
        </td>
        <td class="py-2 px-4 text-center">
          <div class="max-w-fit mx-auto" v-if="service">
            <img
              v-tooltip="service[server.web_server].title"
              :src="service[server.web_server].logo"
              :class="['w-auto', server.web_server == 'mern' ? 'h-4' : 'h-8']"
            />
          </div>
        </td>
        <td class="py-2 px-4 text-center">
          <div class="max-w-fit min-w-full">
            <img
              v-if="server.database_type === 'mysql'"
              src="/logo/mysql.svg"
              class="w-auto h-6 mx-auto"
              v-tooltip="`MySQL`"
            />
            <img
              v-else-if="server.database_type === 'mongodb'"
              src="/logo/mongodb.png"
              class="w-auto h-6 mx-auto"
              v-tooltip="`MongoDB`"
            />
            <img
              v-else-if="server.database_type === 'mariadb'"
              src="/svg/mariadb.svg"
              class="w-auto h-6 mx-auto"
              v-tooltip="`MariaDB`"
            />
            <span
              v-if="server.database_type === ''"
              class="text-sm text-red-700"
              >{{ server.database_type ? server.database_type : "-" }}</span
            >
          </div>
        </td>
        <td class="whitespace-nowrap py-2 px-4 text-center cursor-pointer">
          <div class="flex items-center justify-center gap-3">
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
                  'material-symbols-outlined p-1.5  rounded-md text-[20px]',
                  isLightColor
                    ? 'text-custom-700 bg-custom-300'
                    : ' bg-custom-50 text-custom-500',
                ]"
              >
                visibility
              </span>
            </router-link>
            <span
              v-tooltip="'Delete'"
              @click="deleteServers(server)"
              :class="[
                'material-symbols-outlined p-1.5 rounded-md text-[20px] bg-red-50',
                isLightColor ? 'text-red-700' : 'text-red-500',
              ]"
            >
              delete
            </span>
          </div>
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
          <td colspan="8" class="text-center text-sm px-6 py-5">
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
    :btnBgColor="'bg-red-500'"
    :confirmationTitle="'Delete Server'"
    :submitBtnTitle="`Yes I'm sure`"
    @confirm="deleteServer"
  >
    <template #icon>
      <span class="material-symbols-outlined text-[22px] text-red-500"
        >dns</span
      >
    </template>

    <template v-slot:content
      ><span class="text-tiny text-gray-600"
        >Are you Sure you want to delete this server?</span
      >
      <div class="pt-1 xs:flex items-center gap-2 my-5">
        <p class="text-tiny font-medium text-gray-800 min-w-52">Server Name</p>
        <p class="text-gray-500 text-tiny truncate">
          {{ serverData ? serverData.name : "-" }}
        </p>
      </div>
      <div class="xs:flex items-center gap-2 my-5">
        <P class="text-tiny font-medium text-gray-800 min-w-52">Server IP</P>
        <p class="text-gray-500 text-tiny">
          {{ serverData ? serverData.ip : "-" }}
        </p>
      </div>
      <hr />
      <p class="my-5 text-tiny font-medium">
        Once you deleted the server all data associated with it will be deleted
        and it will not be possible to recover it later.
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
        icon: "dns",
        pages: [
          {
            name: "Servers",
          },
        ],
      },
      refreshing: false,
      openConfirmation: false,
      showLoader: false,
      serverData: null,
      pagination: null,
      processing: false,
      servers: [],
      CloudLogos: CloudLogo,
      service: service,
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
        "Server",
        "User",
        {
          title: "Resources (CPU/RAM/Disk)",
          classes: "whitespace-nowrap",
        },
        {
          title: "Web Server",
          classes: "text-center",
        },
        {
          title: "Database",
          classes: "text-center",
        },
        {
          title: "Actions",
          classes: "text-center",
        },
      ],
    };
  },
  mounted() {
    this.fetchServers();
    this.handleSearch = this.$debounce(this.fetchServers, 1000);
  },
  computed: {
    hasOneCloudProvider() {
      return this.servers.some((server) => server.cloud_provider !== null);
    },
  },
  methods: {
    deleteServers(server) {
      this.openConfirmation = true;
      this.serverData = server;
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
          this.$toast.error(response.data.message);
          this.openConfirmation = false;
        })
        .finally(() => {
          this.showLoader = false;
        });
    },

    async fetchServers(page = 1) {
      this.refreshing = true;
      let url = `/admin/servers?page=${page}&per_page=${this.per_page}&search=${this.search}`;
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
          this.$toast.error(response.servers.data.message);
        })
        .finally(() => {
          this.refreshing = false;
        });
    },
    clearSearch() {
      this.search = "";
      this.handleSearch();
    },
    handlePageChange(newPage) {
      this.fetchServers(newPage);
    },
    handlePerPageChange() {
      this.fetchServers(1);
    },
  },
};
</script>
