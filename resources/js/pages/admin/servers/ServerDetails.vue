<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div v-if="serverDetails">
    <div class="flex items-center justify-between mb-5">
      <p class="text-xl font-medium">Server Details</p>
    </div>
    <div class="container-fluid mx-auto">
      <div class="grid xl:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5">
        <div
          :class="[
            isLightColor ? 'border-custom-700' : 'border-custom-500',
            'bg-white border-t-4 rounded-lg py-3 shadow',
          ]"
        >
          <div class="flex justify-between items-center gap-2 px-5 py-2.5">
            <span class="text-tiny font-medium">Server Name</span>
            <span
              class="text-tiny max-w-36 truncate"
              v-tooltip="serverDetails.name"
              >{{ serverDetails.name ? serverDetails.name : "-" }}</span
            >
          </div>
          <div class="flex justify-between items-center px-5 py-2.5">
            <span class="text-tiny font-medium">IP Address</span>
            <div class="flex">
              <span
                v-if="serverDetails.ip"
                @click="copyToClipboard(serverDetails.ip)"
                class="material-symbols-outlined cursor-pointer text-blue-500 text-[16px] px-1 py-1"
              >
                content_copy
              </span>
              <span
                class="text-tiny max-w-40 truncate"
                v-tooltip="serverDetails.ip"
                >{{ serverDetails.ip ? serverDetails.ip : "-" }}</span
              >
            </div>
          </div>
          <div class="flex justify-between items-center px-5 py-2.5">
            <div class="flex flex-col gap-1.5">
              <span class="text-tiny font-medium">User</span>
            </div>
            <span
              class="text-tiny truncate max-w-40"
              v-tooltip="serverDetails.user && serverDetails.user.name"
              >{{ serverDetails.user ? serverDetails.user.name : "-" }}</span
            >
          </div>
          <div class="flex justify-between items-center px-5 py-2.5">
            <span class="text-tiny font-medium">Email</span>
            <div class="flex">
              <span
                  v-if="serverDetails.user"
                  @click="copyToClipboard(serverDetails.user.email)"
                  class="material-symbols-outlined cursor-pointer text-blue-500 text-[16px] px-1 py-1"
                >
                content_copy
              </span>
              <span
                class="text-tiny max-w-40 truncate"
                v-tooltip="serverDetails.user && serverDetails.user.email"
                >{{ serverDetails.user ? serverDetails.user.email : "-" }}</span
              >
            </div>
          </div>
        </div>
        <div
          :class="[
            isLightColor ? 'border-custom-700' : 'border-custom-500',
            'bg-white border-t-4 rounded-lg py-3 shadow',
          ]"
        >
          <div class="flex justify-between items-center px-5 py-2.5">
            <span class="text-tiny font-medium">Server Provider</span>
            <img
              v-tooltip="cloudProviders[serverDetails.provider_name].title"
              :src="cloudProviders[serverDetails.provider_name].logo"
              alt=""
              class="h-7"
              v-if="serverDetails.provider_name"
            />
            <span v-else>-</span>
          </div>
          <div class="flex justify-between items-center px-5 py-2.5">
            <span class="text-tiny font-medium">Web Server</span>
            <img
              v-tooltip="services[serverDetails.web_server].title"
              :src="services[serverDetails.web_server].logo"
              alt=""
              class="h-7"
              v-if="serverDetails.web_server"
            />
            <span v-else>-</span>
          </div>
          <div class="flex justify-between items-center px-5 py-2.5">
            <span class="text-tiny font-medium">Database</span>
            <img
              v-tooltip="'MySQL'"
              src="/logo/mysql.svg"
              alt=""
              class="h-6"
              v-if="serverDetails.database_type === 'mysql'"
            />
            <img
              v-tooltip="'MariaDB'"
              src="/service/mariadb-logo.png"
              alt=""
              class="h-6"
              v-else-if="serverDetails.database_type === 'mariadb'"
            />
            <img
              v-tooltip="'MongoDB'"
              src="/logo/mongodb.png"
              alt=""
              class="h-6"
              v-else-if="serverDetails.database_type === 'mongodb'"
            />
            <span v-else>-</span>
          </div>
          <div class="flex justify-between items-center px-5 py-2.5">
            <span class="text-tiny font-medium">Operating System</span>
            <div class="flex items-center gap-1" v-if="serverDetails.version">
              <img src="/service/ubuntu.png" alt="" class="h-3.5" />
              <span class="text-sm capitalize">{{
                serverDetails.operating_system
                  ? serverDetails.operating_system
                  : "Ubuntu"
              }}</span>
              <span class="text-sm">{{ serverDetails.version }}</span>
            </div>
            <span v-else>-</span>
          </div>
        </div>
        <div
          :class="[
            isLightColor ? 'border-custom-700' : 'border-custom-500',
            'bg-white border-t-4 rounded-lg py-3 shadow',
          ]"
        >
          <div class="flex justify-between items-center px-5 py-2.5">
            <span class="text-tiny font-medium">Status</span>
            <div
              class="bg-red-100 px-2.5 py-1.5 rounded-full text-xs text-red-500"
              v-if="serverDetails.agent_status == 0"
            >
              <i class="fa-solid fa-circle pr-2 text-xs"></i>
              <span class="text-xs">Disconnected</span>
            </div>
            <div
              class="bg-green-100 px-2.5 py-1.5 text-xs rounded-full text-green-500"
              v-else-if="serverDetails.agent_status == 1"
            >
              <i class="fa-solid fa-circle pr-2 text-xs"></i>
              <span class="text-xs">Connected</span>
            </div>
            <div
              class="bg-gray-100 px-2.5 max-w-36 truncate py-1.5 text-xs rounded-full text-gray-500"
              v-else
            >
              <i class="fa-solid fa-circle pr-2 text-xs"></i>
              <span class="text-xs " v-tooltip="serverDetails.agent_status">{{
                serverDetails.agent_status
              }}</span>
            </div>
          </div>
          <div class="flex justify-between items-center px-5 py-2.5">
            <span class="text-tiny font-medium">SSH Port</span>
            <span class="text-tiny">{{
              serverDetails.ssh_port ? serverDetails.ssh_port : "-"
            }}</span>
          </div>
          <div class="flex justify-between items-center px-5 py-2.5">
            <span class="text-tiny font-medium">PHP CLI Version</span>
            <span class="text-tiny">{{
              serverDetails.php_cli_version
                ? serverDetails.php_cli_version
                : "-"
            }}</span>
          </div>
          <div class="flex justify-between items-center px-5 py-2.5">
            <span class="text-tiny font-medium">Hostname</span>
            <span
              class="text-tiny max-w-40 truncate"
              v-tooltip="serverDetails.hostname"
              >{{ serverDetails.hostname || "-" }}</span
            >
          </div>
        </div>
      </div>
    </div>
    <div class="grid xl:grid-cols-3 grid-cols-1 xl:gap-5 my-5">
      <div class="border border-primary p-5 py-4 rounded-md col-span-2">
        <div class="sm:flex justify-between gap-5">
          <div class="">
            <p class="text-lg font-medium">Plan & Pricing Details</p>
            <span class="text-sm">
              Server's current plan and pricing information shown here.
            </span>
          </div>
          <Button @click="openUpdatePlanModal" v-if="serverDetails.subscription"
            >Update Plan</Button
          >
        </div>
        <div class="justify-between xs:flex mt-2">
          <p class="text-lg font-medium">General Purpose</p>
          <div>
            <span class="text-lg font-medium" v-if="serverDetails">
              {{ formatCurrency(serverDetails?.subscription?.monthly_price) }}
            </span>
            <span class="text-lg font-medium" v-else>-</span>
            <span
              class="items-end inline-flex text-sm"
              v-if="
                serverDetails.subscription &&
                serverDetails.subscription.monthly_price
              "
              >/Month</span
            >
          </div>
        </div>
        <hr class="my-3" />
        <div class="text-tiny space-y-3">
          <div class="flex justify-between">
            <p class="">Started At</p>
            <p class="text-tiny">
              {{
                serverDetails.subscription
                  ? serverDetails.subscription.formatted_created_at
                  : "-"
              }}
            </p>
          </div>
          <div class="flex justify-between">
            <p class="">Total Hours</p>
            <p>
              {{
                serverDetails.subscription
                  ? serverDetails.subscription.total_hours
                  : "-"
              }}
            </p>
          </div>
          <div class="flex justify-between">
            <p class="">Total Charges</p>
            <p v-if="serverDetails">
              {{
                serverDetails?.subscription
                  ? formatCurrency(serverDetails?.subscription?.total_charges)
                  : "-"
              }}
            </p>
          </div>
        </div>
      </div>
      <div
        class="border border-primary p-5 py-4 rounded-md col-span-1 my-5 xl:my-0 text-tiny"
      >
        <div class="flex justify-between items-center mb-4">
          <p>Username</p>
          <button
            @click="copyToClipboard(serverDetails.username)"
            :disabled="!serverDetails.username"
            class="border border-primary rounded px-1 flex justify-center items-center p-1 disabled:opacity-50"
          >
            <span
              :class="[
                'material-symbols-outlined text-blue-500 text-[18px]',
              ]"
              >content_copy
            </span>
          </button>
        </div>
        <div class="flex justify-between items-center mb-4">
          <p>Password</p>
          <button
            @click="copyToClipboard(serverDetails.password)"
            :disabled="!serverDetails.password"
            class="border border-primary rounded px-1 flex justify-center items-center p-1 disabled:opacity-50"
          >
            <span
              :class="[
                'material-symbols-outlined text-blue-500 text-[18px]',
              ]"
              >content_copy
            </span>
          </button>
        </div>
        <div class="flex justify-between items-center mb-4">
          <p>Database Username</p>
          <button
            @click="copyToClipboard(serverDetails.database_username)"
            :disabled="!serverDetails.database_username"
            class="border border-primary rounded px-1 flex justify-center items-center p-1 disabled:opacity-50"
          >
            <span
              :class="[
                'material-symbols-outlined text-blue-500 text-[18px]',
              ]"
              >content_copy
            </span>
          </button>
        </div>
        <div class="flex justify-between items-center mb-4">
          <p>Database Password</p>
          <button
            @click="copyToClipboard(serverDetails.database_password)"
            :disabled="!serverDetails.database_password"
            class="border border-primary rounded px-1 flex justify-center items-center p-1 disabled:opacity-50"
          >
            <span
              :class="[
                'material-symbols-outlined text-blue-500 text-[18px]',
              ]"
              >content_copy
            </span>
          </button>
        </div>

        <div class="flex justify-between items-center mb-4">
          <p>Redis Password</p>
          <button
            @click="copyToClipboard(serverDetails.redis_password)"
            :disabled="!serverDetails.redis_password"
            class="border border-primary rounded px-1 flex justify-center items-center p-1 disabled:opacity-50"
          >
            <span
              :class="[
                'material-symbols-outlined text-blue-500 text-[18px]',
              ]"
              >content_copy
            </span>
          </button>
        </div>
        <div class="flex justify-between items-center">
          <p>SSH Login</p>
          <button
            @click="copyToClipboard(sshLoginCommand)"
            :disabled="serverDetails.agent_status!=='1'"
            class="border border-primary rounded px-1 flex justify-center items-center p-1 disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-blue-500 text-[18px]">
              content_copy
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
  <Modal
    :show="openModal"
    @closeModal="closeModal"
    :modalTitle="'Update Plan'"
    :modelIcon="'draft_orders'"
  >
    <div class="flex justify-between gap-5 flex-wrap text-tiny">
      <span class="font-semibold">Current Price</span>
      <span
        >{{
          serverDetails && serverDetails?.subscription
            ? formatCurrency(serverDetails?.subscription?.monthly_price)
            : 0
        }}/Month</span
      >
    </div>
    <div class="flex justify-between gap-5 flex-wrap text-tiny mt-2">
      <span class="font-semibold">Server IP</span>
      <span>{{
        serverDetails && serverDetails.ip ? serverDetails.ip : "-"
      }}</span>
    </div>
    <div class="mt-3">
      <label for="amount" class="text-tiny font-semibold">Price per Month ({{ siteSettings.currency_symbol }})</label>
      <input
        type="text"
        name="amount"
        v-model="payload.amount"
        id="amount"
        placeholder="Enter Price"
        class="border text-gray-800 text-sm mt-2 rounded-lg border-gray-300 focus:border-sa-500 focus:ring-0 block w-full p-2"
      />
      <small
        class="text-red-500 error_message text-xs"
        id="amount_message"
      ></small>
    </div>
    <div class="mt-4 flex justify-end gap-4">
      <button @click="closeModal" type="button" class="rounded-md border font-medium px-4 py-2 text-center text-sm">
        Cancel
      </button>
      <Button @click="updateServerPlan" :disabled="processing">
        <i
          v-if="processing"
          class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
        ></i>
        {{ processing ? "Please wait" : "Update" }}
      </Button>
    </div>
  </Modal>
</template>

<script>
import CloudProviders from "@/CloudProviders.js";
import CloudServices from "@/Services.js";
export default {
  name: "AdminServerDetails",
  data() {
    return {
      breadcrumb: {
        icon: "dns",
        pages: [
          {
            name: "Servers",
            path:{name : 'servers'}
          },
          {
            name: "Server Details",
          },
        ],
      },
      serverDetails: {},
      cloudProviders: CloudProviders,
      services: CloudServices,
      openModal: false,
      payload: {
        amount: "",
      },
      processing: false,
    };
  },
  mounted() {
    this.fetchServerDetails();
  },
  computed:{
    sshLoginCommand(){
      return `ssh ${this.serverDetails.username}@${this.serverDetails.ip} -p ${this.serverDetails.ssh_port}`
    }
  },
  methods: {
    openUpdatePlanModal() {
      this.openModal = true;
    },
    closeModal() {
      this.openModal = false;
      this.fetchServerDetails();
    },
    async fetchServerDetails() {
      await this.$axios
        .get(`admin/servers/${this.$route.params.server}`)
        .then(({ data }) => {
          this.serverDetails = data.server;
          this.payload.amount = data.server.subscription
            ? data.server.subscription.monthly_price
            : "";
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    async updateServerPlan() {
      this.processing = true;
      let data = { ...this.payload };
      await this.$axios
        .patch(
          `/admin/users/${this.serverDetails.user.id}/servers/${this.serverDetails.id}/subscriptions/${this.serverDetails.subscription.id}`,
          data
        )
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.closeModal();
        })
        .catch((error) => {
          if (error.response && error.response.status === 422) {
            this.displayError(error.response.data);
          } else {
            this.$toast.error(error.response.data.message);
            this.closeModal();
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },
};
</script>
