<template>
  <div>
    <div
      class="border border-gray-200 rounded-md p-3 px-4"
      v-if="ticketDetails !== null"
    >
      <div
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'font-medium flex items-center gap-2',
        ]"
      >
        <span
          :class="[
            isLightColor
              ? 'bg-custom-200 text-custom-700'
              : 'bg-custom-50 text-custom-500s',
            'material-symbols-outlined p-1 rounded-md text-[20px]',
          ]"
        >
          confirmation_number
        </span>

        Ticket Details
      </div>
      <hr class="my-2" />
      <div class="flex flex-col space-y-2 py-1 text-tiny">
        <div class="flex justify-between items-center">
          <h1 class="font-medium">Department</h1>
          <h1 class="text-gray-500 max-w-24 capitalize truncate">
            {{ ticketDetails.department }}
          </h1>
        </div>
        <div class="flex justify-between items-center">
          <h1 class="font-medium">Created At</h1>
          <h1 class="text-gray-500">
            {{ ticketDetails.created_at }}
          </h1>
        </div>
      </div>
    </div>
    <div
      class="my-5 border border-gray-200 rounded-md p-3 px-4"
      v-if="ticketDetails && ticketDetails.server"
    >
      <div class="flex justify-between items-center">
        <div
          :class="[
            isLightColor ? 'text-custom-700' : 'text-custom-500',
            'font-medium flex items-center gap-2',
          ]"
        >
          <span
            :class="[
              isLightColor ? 'bg-custom-200' : 'bg-custom-50',
              'material-symbols-outlined p-1 rounded-md text-[20px]',
            ]"
          >
            dns
          </span>
          Server Details
        </div>
        <div
          :class="[
            ticketDetails.server.agent_status == 1
              ? 'text-green-500'
              : 'text-red-500',
            'text-tiny flex items-center gap-1 relative',
          ]"
        >
        <span
            class="absolute flex h-3 w-3"
            :class="ticketDetails.server.agent_status == 1 ? 'text-green-500' : 'text-red-500'"
            >
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
              :class="ticketDetails.server.agent_status == 1 ? 'bg-green-400' : 'bg-red-400'">
          </span>
        </span>
          <i class="fa-solid fa-circle text-xs"></i>
          <span>{{
            ticketDetails.server.agent_status == 1
              ? "Connected"
              : "Disconnected"
          }}</span>
        </div>
      </div>
      <hr class="my-2" />
      <div class="text-tiny flex justify-between space-y-2 py-1 items-center">
        <h1 class="font-medium">IP</h1>
        <h1 class="text-gray-500">{{ ticketDetails.server.ip }}</h1>
      </div>
      <div class="text-tiny flex justify-between items-center">
        <h1 class="font-medium">Name</h1>
        <h1
          class="text-gray-500 max-w-40 truncate"
          v-tooltip="ticketDetails.server.name"
        >
          {{ ticketDetails.server.name }}
        </h1>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      ticketDetails: null,
    };
  },
  methods: {
    async fetchTicketDetails() {
      await this.$axios
        .get(`/user/tickets/${this.$route.params.id}`)
        .then(({ data }) => {
          this.ticketDetails = data.ticket;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
  },
  mounted() {
    this.fetchTicketDetails();
  },
};
</script>

<style>
</style>