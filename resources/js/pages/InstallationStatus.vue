<template>
  <Breadcrumb :breadcrumb="breadcrum" />
  <p class="text-xl font-medium pb-5">Installation Progress</p>

  <div class="mx-auto h-full shadow bg-white rounded-md p-5">
    <div
      class="grid 2xl:grid-cols-4 xl:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5 justify-center"
    >
      <!-- Provider Section -->
      <div class="grid">
        <div class="flex items-center gap-5">
          <span
            :class="[
              isLightColor
                ? 'bg-custom-300 text-custom-700'
                : 'bg-custom-50 text-custom-500',
              'material-symbols-outlined text-[22px] rounded-md p-1.5',
            ]"
            >cloud_done</span
          >
          <div class="text-nowrap">
            <p class="font-medium">Provider</p>
            <p class="text-tiny text-gray-500 capitalize">
              {{ server.provider_name || "-" }}
            </p>
          </div>
        </div>
      </div>
      <!-- Name Section -->
      <div class="flex items-center gap-5">
        <span
          :class="[
            isLightColor
              ? 'bg-custom-300 text-custom-700'
              : 'bg-custom-50 text-custom-500',
            'material-symbols-outlined rounded-md text-[22px] p-1.5',
          ]"
          >dns</span
        >
        <div class="md:text-nowrap">
          <p class="font-medium">Name</p>
          <p class="text-tiny text-gray-500 max-w-56 truncate" v-tooltip="server.name">
            {{ server.name || "-" }}
          </p>
        </div>
      </div>
      <!-- IP Section -->
      <div class="flex items-center gap-5">
        <span
          :class="[
            isLightColor
              ? 'bg-custom-300 text-custom-700'
              : 'text-custom-500 bg-custom-50',
            'material-symbols-outlined rounded-md text-[22px] p-1.5',
          ]"
          >location_on</span
        >
        <div class="text-nowrap">
          <p class="font-medium">IP</p>
          <p class="text-tiny text-gray-500">
            {{ server.ip && server.ip !== "8.8.8.8" ? server.ip : "-" }}
          </p>
        </div>
      </div>

      <div class="flex items-center gap-5">
        <span
          :class="[
            isLightColor
              ? 'bg-custom-300 text-custom-700'
              : 'bg-custom-50 text-custom-500',
            'material-symbols-outlined  rounded-md text-[22px] p-1.5',
          ]"
          >settings_b_roll</span
        >
        <div class="md:text-nowrap">
          <p class="font-medium">Operating System</p>
          <p class="text-tiny text-gray-500" v-if="server.version">
            Ubuntu {{ server.version }}
          </p>
          <p class="text-tiny text-gray-500" v-else>-</p>
        </div>
      </div>
    </div>
    <hr class="my-7" />
    <div class="flex gap-5 items-center">
      <img src="/images/Frame.png" class="w-9 h-9" />
      <div>
        <p class="font-medium">Real-Time Server Status</p>
        <p class="text-gray-500 text-tiny">
          View real-time data and performance metrics for your server to stay
          informed about its current status.
        </p>
      </div>
    </div>
    <div class="mt-10">
      <span
        class="font-medium text-lg"
        :class="response && response.status == 'failed' ? 'text-red-500' : ''"
      >
        {{
          response && response.label
            ? response.label.replace(/ServerAvatar/g, this.app_name)
            : "Creating Server"
        }}
      </span>
      <div class="text-sm">
        <div class="mt-3" aria-hidden="true">
          <div class="overflow-hidden rounded-full bg-gray-200">
            <div
              class="h-2 rounded-full bg-gradient-to-r from-blue-700 to-blue-500"
              :style="{
                width: (response?.configurationInPercentage || 3) + '%',
              }"
            ></div>
          </div>
        </div>
        <p class="text-gray-500 mt-2">
          {{ response?.configurationInPercentage || 3 }}%
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "InstallationStatus",
  data() {
    return {
      breadcrum: {
        // title: "Servers",
        icon: "dns",
        pages: [
          {
            name: "Servers",
            path:{name : 'dashboard'}
          },
          {
            name: "Installation",
          },
        ],
      },
      server: {
        provider_name: "",
        name: "",
        ip: "-",
        operating_system: "",
      },
      response: null,
    };
  },
  props: {
    serverId: {
      type: String,
      required: true,
    },
  },

  mounted() {
    this.statusServer();
    this.statusTimer = setInterval(this.statusServer, 10000);
  },
  beforeUnmount() {
    if (this.statusTimer) {
      clearInterval(this.statusTimer);
    }
  },
  methods: {
    statusServer() {
      this.$axios
        .patch(`/servers/${this.$route.params.server}/update`)
        .then(({ data }) => {
          if (!data.response) {
            this.serverShow();
          } else {
            this.response = data.response;
            this.server = data.response.server;
          }

          if (data.response && data.response.status === "failed") {
            clearInterval(this.statusTimer);
          }

          if (data.response && data.response.status === "completed") {
            this.redirectToHostingPanel();
            this.$router.push({
              name: "dashboard",
            });
          }
        })
        .catch(({ response }) => {
          if (response.status === 404) {
            this.$router.push({
              name: "dashboard",
            });
          }
          this.$toast.error(response.data.message);
        });
    },
    serverShow() {
      this.$axios
        .get(`/servers/${this.$route.params.server}`)
        .then(({ data }) => {
          this.server = data.server;
        })
        .catch(({ response }) => {
          if (response.status === 404) {
            this.$router.push({
              name: "dashboard",
            });
          }
        });
    },
    redirectToHostingPanel() {
      this.$axios
        .get(`/servers/${this.$route.params.server}/panel`)
        .then((response) => {
          if (response.data.agent_status === 1) {
            window.open(`http://${response.data.panelUser.domain}`, "_blank");
          } else {
            window.open(
              `http://${response.data.panelUser.domain}/host/login?key=${response.data.panelUser.key}&server=${this.$route.params.server}&redirect=false`,
              "_blank"
            );
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
  },
};
</script>
