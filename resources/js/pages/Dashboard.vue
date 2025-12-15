<template>
  <template v-if="processing && search == ''">
    <TableSkeleton />
  </template>
  <template v-else>
    <template v-if="(!providerList.length || !isSetBilling) && search == ''">
      <CloudPlatformInfo
        :isSetProviders="providerList.length"
        :isSetBilling="isSetBilling"
      />
    </template>
    <div :class="[user && user.email_verified_at === null ? 'opacity-50 pointer-events-none select-none' : '']" v-else-if="!servers.length && search == '' && !isSearching">
      <ConnectServer />
    </div>
    <template v-else>
      <Breadcrumb :breadcrumb="breadcrumb" />
      <div class="sm:flex justify-between items-center mb-3">
        <p class="text-xl text-[#31363f] font-medium">Servers</p>
        <div class="xs:flex items-center gap-5 sm:mt-0 mt-4">
          <div class="relative w-full">
            <input
              v-model="search"
              @input="handleSearch()"
              type="text"
              name="account-number"
              id="account-number"
              class="block w-full sm:min-w-80 bg-[#FDFDFD] rounded-md text-sm border-0 py-2 text-gray-800 ring-1 ring-gray-300 placeholder:text-[#818995] sm:leading-5 focus:outline-none focus:ring-gray-300"
              placeholder="Search Server"
            />
            <button
              v-if="search"
              type="button"
              @click="clearSearch"
              class="absolute inset-y-0 rounded-r-md bg-[#F8F9FA] right-9 border-l flex justify-center items-center px-2"
            >
              <span v-tooltip="'Clear'" class="material-symbols-outlined text-[22px]">close</span>
            </button>
            <div
              class="pointer-events-none absolute inset-y-0 rounded-r-md bg-[#F8F9FA] right-0 border-l flex justify-center items-center px-2"
            >
              <i class="las la-search text-xl text-[#818995] -rotate-90"></i>
            </div>
          </div>

          <div
            class="flex justify-end items-center gap-5 xs:mt-0 mt-4"
            v-if="user"
          >
          
            <button
              @click="getServers(pagination.current_page, true)"
              type="button"
              :class="[
                'rounded-md px-2.5 py-1.5 bg-[#F8F9FA] border border-[#CBD5E0] text-[#818995] text-[15px]',
              ]"
            >
              <i
                class="fa-solid fa-rotate-right"
                :class="{ 'fa-spin': refreshing }"
              ></i>
            </button>
            <div
              v-if="user.email_verified_at === null"
              v-tooltip="
                'Your email address has not been verified. Please verify your email to connect a server.'
              "
            >
              <Button
                :disabled="user.email_verified_at === null"
                @click="$router.push({ name: 'serverConnect' })"
              >
                Create
              </Button>
            </div>
            <router-link
              v-else-if="user.email_verified_at !== null"
              :class="[
                isLightColor
                  ? 'bg-custom-700 text-black'
                  : 'bg-custom-500 text-white',
                'rounded-md  px-4 py-2 font-medium text-sm text-center',
              ]"
              :to="{
                name: 'serverConnect',
              }"
            >
              Create
            </router-link>
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
            <td class="whitespace-nowrap py-3 pl-10 px-4 min-w-[300px]">
              <div class="flex items-center gap-5">
                  <div class="flex justify-center items-center gap-4">
                      <span class="relative flex h-2.5 w-2.5">
                          <!-- Pinging effect -->
                          <span
                            :class="[
                                'absolute inline-flex animate-ping h-full w-full rounded-full opacity-75',
                                server.agent_status === '0'
                                ? 'bg-red-500'
                                : server.agent_status === '1'
                                ? 'bg-green-500'
                                : 'bg-gray-600',
                                ]"
                            ></span>
                        <!-- Actual icon -->
                        <i
                            :class="[
                            'relative fa-solid fa-circle text-[10px] capitalize',
                            server.agent_status === '0'
                                ? 'text-red-500'
                                : server.agent_status === '1'
                                ? 'text-green-500'
                                : 'text-gray-500',
                            ]"
                            v-tooltip="
                            server.agent_status === '0'
                                ? 'Disconnected'
                                : server.agent_status === '1'
                                ? 'Connected'
                                : server.agent_status
                            "
                        ></i>
                    </span>
                  <template v-if="CloudLogos && server.provider_name">
                    <img
                      :src="CloudLogos[server.provider_name].logo"
                      class="w-7 h-auto"
                      v-tooltip="`${CloudLogos[server.provider_name].title}`"
                    />
                  </template>
                  <template v-else>
                    <span :class="server.provider_name ? '' : 'w-7'">
                      {{ server.provider_name ? server.provider_name : "" }}
                    </span>
                  </template>
                  <span
                    v-if="server.provider_name === ''"
                    class="text-red-700"
                    >{{ server.provider_name }}</span
                  >
                </div>
                <div class="max-w-[200px]">
                  <button
                    v-if="server.agent_status == '1'"
                    @click="redirectToHostingPanel(server.id)"
                    class="-pl-[1px] truncate block text-[14px] font-medium"
                    role="link"
                    tabindex="0"
                    v-tooltip="server.name"
                  >
                    {{ server.name }}
                  </button>
                  <p
                    v-else-if="server.agent_status == 0 || server.agent_status === 'failed'"
                    class="-pl-[1px] truncate block text-[14px] font-medium"
                    v-tooltip="server.name"
                  >
                    {{ server.name }}
                  </p>
                  <router-link
                    v-else
                    :to="{ name: 'InstallationStatus', params: { server: server.id } }"
                    class="-pl-[1px] truncate block text-[14px] font-medium"
                    role="link"
                    tabindex="0"
                    v-tooltip="server.name"
                  >
                    {{ server.name }}
                  </router-link>
                  <span class="flex text-gray-500 py-0.5 items-center">
                    {{ server.ip ? server.ip : "-" }}
                    <span
                      @click="copyToClipboard(server.ip)"
                      class="material-symbols-outlined cursor-pointer text-blue-500 text-sm pl-1 "
                    >
                      content_copy
                    </span>
                  </span>
                  <p class="text-[#777A7E]">
                    {{ server.created_at ? server.created_at : "-" }}
                  </p>
                </div>
              </div>
            </td>
            <td
              class="whitespace-nowrap py-1 px-4 text-sm truncate max-w-[150px]"
            >
              <div class="text-gray-500 py-0.5 flex items-center gap-2.5" v-if="server.plan">
                <div class="flex items-center gap-0.5">
                  <span class="material-symbols-outlined text-[18px]" v-tooltip="'Core'">
                    database
                  </span>
                  <p>
                    {{ server.plan.cores}}
                  </p>
                </div>
                <div class="flex items-center gap-0.5">
                  <span class="material-symbols-outlined text-[20px]" v-tooltip="'RAM'">
                    memory
                  </span>
                  <p class="">
                    {{ (server.plan.ram /1024).toFixed(1) }} GB
                  </p>
                </div>
                <div class="flex items-center gap-0.5">
                  <span class="material-symbols-outlined text-[18px]" v-tooltip="'Disk'">
                    sd_card
                  </span>
                  <p>
                    {{ server.plan.disk ? server.plan.disk : "-" }}
                    GB
                  </p>
                </div>
              </div>
              <div v-else>-</div>
            </td>

            <td class="py-2 px-4 text-center">
              <div class="flex justify-center">
                <img
                  v-if="server.web_server === 'nginx'"
                  src="/logo/nginx.svg"
                  class="w-auto h-9"
                  v-tooltip="`Nginx`"
                />
                <img
                  v-else-if="server.web_server === 'openlitespeed'"
                  src="/logo/ols.svg"
                  class="w-auto h-9"
                  v-tooltip="`Openlitespeed`"
                />
                <img
                  v-else-if="server.web_server === 'mern'"
                  src="/logo/node.svg"
                  class="w-auto h-5"
                  v-tooltip="`NodeJs`"
                />
                <img
                  v-else-if="server.web_server === 'apache2'"
                  src="/service/apache.svg"
                  class="w-auto h-9"
                  v-tooltip="`Apache`"
                />
                <span v-if="server.web_server === ''" class="text-red-700">{{
                  server.web_server ? server.web_server : "-"
                }}</span>
              </div>
            </td>

            <td class="py-2 px-4 text-center">
              <div class="flex justify-center">
                <img
                  v-if="server.database_type === 'mysql'"
                  src="/logo/mysql.svg"
                  class="w-auto h-6"
                  v-tooltip="`MySQL`"
                />
                <img
                  v-else-if="server.database_type === 'mongodb'"
                  src="/logo/mongodb.png"
                  class="w-auto h-6"
                  v-tooltip="`MongoDB`"
                />
                <img
                  v-else-if="server.database_type === 'mariadb'"
                  src="/svg/mariadb.svg"
                  class="w-auto h-6"
                  v-tooltip="`MariaDB`"
                />
                <span v-if="server.database_type === ''" class="text-red-700">{{
                  server.database_type ? server.database_type : "-"
                }}</span>
              </div>
            </td>

            <td class="whitespace-nowrap py-2 px-4 text-base text-center">
              <div class="flex justify-center px-4">
                <span class="text-tiny flex items-center">
                  <img
                    v-if="server.version"
                    :src="`/logo/ubuntu.svg`"
                    v-tooltip="`Ubuntu`"
                    class="w-auto h-7"
                  />
                  <span class="px-2 flex font-medium capitalize">
                    {{ server.version ? server.version : "-" }}
                  </span>
                </span>
              </div>
            </td>
            <td class="whitespace-nowrap py-2 px-4 text-center cursor-pointer">
              <div>
                <button
                  :disabled="isProcessing"
                  v-if="
                    server.agent_status == 0 || server.agent_status === 'failed'
                  "
                  @click="reconnect(server.id)"
                  v-tooltip="'Reconnect'"
                >
                  <span
                    :class="[
                      'material-symbols-outlined',
                      'p-1.5 bg-blue-50 rounded-md text-[20px]',
                      isLightColor ? 'text-blue-700' : 'text-blue-500',
                    ]"
                  >
                    <span
                      style="display: inline-block; transform: rotate(-45deg)"
                    >
                      link
                    </span>
                  </span>
                </button>

                <button
                  v-else-if="server.agent_status == 1"
                  @click="redirectToHostingPanel(server.id)"
                  v-tooltip="'Server Panel'"
                >
                  <span
                    :class="[
                      'material-symbols-outlined',
                      'p-1.5 bg-custom-50 rounded-md text-[20px]',
                      isLightColor ? 'text-custom-700' : 'text-custom-500',
                    ]"
                  >
                    readiness_score
                  </span>
                </button>

                <router-link
                  v-else
                  :to="{
                    name: 'InstallationStatus',
                    params: {
                      server: server.id,
                    },
                  }"
                  v-tooltip="'Installation Status'"
                >
                  <span
                    :class="[
                      'material-symbols-outlined',
                      'p-1.5 bg-custom-50 rounded-md text-[20px]',
                      isLightColor ? 'text-custom-700' : 'text-custom-500',
                    ]"
                  >
                    readiness_score
                  </span>
                </router-link>

                <button
                  @click="openDeleteServers(server)"
                  :class="[
                    'material-symbols-outlined p-1.5 ml-3 rounded-md text-[20px] bg-red-50',
                    isLightColor ? 'text-red-700' : 'text-red-500',
                  ]"
                  v-tooltip="'Delete'"
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
                <Perpage v-model="per_page" :initialPerPage="pagination.per_page" @change="handlePerPageChange" />
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
          <TableSkeleton :heads="6" v-if="refreshing" />
          <Table :head="thead" v-else>
            <tr>
              <td colspan="6" class="text-center text-sm px-6 py-5">
                {{ refreshing ? "Please Wait" : "No Servers Found" }}
              </td>
            </tr>
          </Table>
        </template>
        <Confirmation
          :show="openConfirmation"
          :showLoader="showLoader"
          :confirmationTitle="'Delete Server'"
          :btnBgColor="'bg-red-500'"
          :submitBtnTitle="`Yes, I'm Sure`"
          @confirm="deleteServer"
          @closeModal="closeModal"
        >
          <template #icon>
            <span class="material-symbols-outlined text-red-500 text-[22px]">
              dns
            </span>
          </template>
          <template v-slot:content
            ><span class="text-tiny text-gray-600"
              >Are you sure you want to delete your server?
            </span>
            <div class="pt-1 xs:flex items-center gap-2 my-4">
              <p class="text-tiny font-medium text-gray-800 min-w-52">
                Server Name
              </p>
              <p class="text-gray-500 text-tiny truncate">
                {{ serverData ? serverData.name : "-" }}
              </p>
            </div>
            <div class="xs:flex items-center gap-2 my-5">
              <P class="text-tiny font-medium text-gray-800 min-w-52"
                >Server IP</P
              >
              <p class="text-gray-500 text-tiny">
                {{ serverData ? serverData.ip : "-" }}
              </p>
            </div>
            <hr />
            <p class="my-5 mt-4 font-medium text-tiny">
              Once you delete your server your all data will be lost and you
              won't be able to recover it later. Therefore, before deleting your
              server make sure that you have taken backups of your data.
            </p>
          </template>
        </Confirmation>
      </div>
    </template>
  </template>
</template>

<script>
import CloudLogo from "@/CloudProviders.js";
import CloudPlatformInfo from "@/components/CloudPlatformInfo.vue";
import ConnectServer from "@/components/user/ConnectServer.vue";
import { mapState } from "pinia";
import { useAuthStore } from "@/store/auth";

export default {
  name: "Dashboard",
  components: {
    CloudPlatformInfo,
    ConnectServer,
  },

  computed: {
    ...mapState(useAuthStore, ["user"]),
  },
  watch:{
    search(newVal,preValue){
        if (preValue) {
            this.isSearching = true
        }
    }
  },
  data() {
    return {
      breadcrumb: {
        // title: "Servers",
        icon: "dns",
        pages: [{ name: "Servers" }],
      },
      refreshing: false,
      openConfirmation: false,
      serverData: null,
      showLoader: false,
      processing: false,
      isSearching:false,
      servers: [],
      CloudLogos: CloudLogo,
      search: "",
      thead: [
        {
          title: "Name",
          classes: ["max-w-[10rem] w-10 pl-10"],
        },
        {
          title: "Resources (Core/RAM/Disk)",
          classes: ["w-10 whitespace-nowrap"],
        },
        {
          title: "Web Server",
          classes: ["w-10 text-center whitespace-nowrap"],
        },
        {
          title: "Database",
          classes: ["w-10 text-center"],
        },
        {
          title: "OS",
          classes: ["w-10 text-center"],
        },
        {
          title: "Actions",
          classes: ["xl:max-w-[10rem] xl:w-10 text-center"],
        },
      ],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      per_page: 10,
      whitelabel: {
        key: "",
        domain: "whitelabel.serveravatar.test",
      },
      providerList: [],
      isSetBilling: false,
      isProcessing: false,
    };
  },
  methods: {
    openDeleteServers(server) {
      this.openConfirmation = true;
      this.id = server.id;
      this.serverData = server;
    },
    closeModal() {
      this.openConfirmation = false;
      this.id = "";
    },
    async getProviders() {
      this.processing = true;
      await this.$axios
        .get("/cloud-providers")
        .then(({ data }) => {
          this.providerList = data.cloud_providers;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.processing = false;
        });
    },
    async getEnabledProviders() {
      this.processing = true;
      await this.$axios
        .get("/enable-providers")
        .then(({ data }) => {
          this.isSetBilling = data.basic_details ? true : false;
        })
        .catch((error) => {
          this.$toast.error(error.response.data.message);
        })
        .finally(() => {
          this.processing = false;
        });
    },
    async deleteServer() {
      this.showLoader = true;
      await this.$axios
        .delete(`/servers/${this.id}`)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.closeModal();
          this.getServers();
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
          this.closeModal();
        })
        .finally(() => {
          this.openConfirmation = false;
          this.showLoader = false;
          this.getServers();
        });
    },

    async reconnect(id) {
      if (this.isProcessing) return;
      this.isProcessing = true;
      await this.$axios
        .get(`/servers/${id}/reconnect`)
        .then(({ data }) => {
          this.$toast.success(data.message);
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.isProcessing = false;
          this.getServers();
        });
    },
    redirectToHostingPanel(id) {
      this.$axios
        .get(`/servers/${id}/panel`)
        .then((response) => {
          const resellerDomain = window.location.origin;
          const panelDomain = response.data.panelUser.domain;
          const confirmationTimer = this.user.confirmation_timer;
          if (response.data.agent_status === 1) {
            window.open(
              `http://${panelDomain}?confirmation_timer=${confirmationTimer}&source=reseller&reseller_domain=${encodeURIComponent(resellerDomain)}`,
              "_blank"
            );
          } else {
            window.open(
              `http://${panelDomain}/host/login` +
                `?key=${response.data.panelUser.key}` +
                `&server=${id}` +
                `&redirect=false` +
                `&confirmation_timer=${confirmationTimer}` +
                `&source=reseller` +
                `&reseller_domain=${encodeURIComponent(resellerDomain)}`,
              "_blank"
            );
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },

    async getServers(page = "", isClickRefresh = false) {
      this.refreshing = true;
      if (!isClickRefresh) {
        this.processing = true;
      }
      let url = `/servers?page=${page}&per_page=${this.per_page}&search=${this.search}`;
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
          this.processing = false;
        });
    },
    handlePerPageChange() {
      this.getServers(1, true);
    },
    handlePageChange(newPage) {
      this.getServers(newPage, true);
    },
    handleSearch() {
      if (this.search === "") {
        this.getServers();
      } else {
        this.debouncedGetServers();
      }
    },
    clearSearch() {
      this.search = "";
      this.handleSearch();
    },
  },
  async mounted() {
    this.debouncedGetServers = this.$debounce(this.getServers, 500);
    await this.getServers();
    await this.getProviders();
    await this.getEnabledProviders();
  },
};
</script>

