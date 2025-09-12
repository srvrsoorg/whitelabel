<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="sm:flex justify-between items-center gap-5">
    <h1 class="text-xl font-medium text-[#31363f] flex items-center">
      Tickets
    </h1>
    <div class="flex gap-5 items-center mt-4 sm:mt-0">
      <div class="relative rounded-md shadow-sm w-full">
        <input
          type="text"
          v-model="search"
          @input="handleSearch()"
          name="search"
          id="search"
          class="w-full sm:min-w-96 block rounded-md border border-primary focus:border-primary py-1.5 ring-gray-300 placeholder:text-gray-500 text-sm leading-6 focus:ring-0"
          placeholder="Search"
        />
        <button
          v-if="search"
          type="button"
          @click="clearSearch"
          class="border border-primary bg-[#F6F6F6] absolute inset-y-0 text-gray-500 right-11 flex items-center px-2.5"
        >
          <span v-tooltip="'Clear'" class="material-symbols-outlined text-[22px]">close</span>
        </button>
        <div
          class="pointer-events-none border rounded-r-md border-primary bg-[#F6F6F6] absolute inset-y-0 text-gray-500 right-0 flex items-center px-2.5"
        >
          <span class="material-symbols-outlined text-[22px]"> search </span>
        </div>
      </div>
      <div class="flex items-center gap-5 sm:mt-0">
        <div>
          <button
            @click="fetchTicket(pagination.current_page)"
            :class="[
              textColorClass,
              'bg-[#F6F6F6]  p-2 border border-primary rounded-md flex items-center ',
            ]"
          >
            <span
              :class="[
                refreshing ? 'fa-spin' : '',
                'material-symbols-outlined text-[22px] text-gray-400',
              ]"
            >
              refresh
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="border-b border-gray-200 my-4">
    <nav class="flex sm:gap-8" aria-label="Tabs">
      <div class="border-gray-200 text-sm flex sm:space-x-7 xs:space-x-5 space-x-2">
        <button
          @click="changeStatus(tab)"
          v-for="tab in tabs"
          :key="tab"
          :class="[
            tab.status === status
              ? isLightColor
                ? 'border-b-2 text-custom-700 border-custom-700'
                : 'border-b-2 text-custom-500 border-custom-500'
              : '',
            'whitespace-nowrap text-tiny flex gap-2 px-1.5 py-2 font-medium',
          ]"
        >
          <span>{{ tab.name }}</span>
          <span
            :class="[
              tab.status === status
                ? isLightColor
                  ? 'bg-custom-700 text-black'
                  : 'bg-custom-500 text-white'
                : 'bg-[#CBD5E0] text-white',
              'rounded-full px-2.5 py-[1px]  text-[12px] flex  items-center',
            ]"
            >{{ tab.count }}</span
          >
        </button>
      </div>
    </nav>
  </div>
  <div class="my-5">
    <Table :head="thead" v-if="ticketData.length > 0">
      <tr
        v-for="ticket in ticketData"
        :key="ticket"
        class="border-b border-gray-200 text-[#2c3138]"
      >
        <td class="whitespace-nowrap text-left py-5 px-4 text-sm pl-10">
          #{{ ticket.id }}
        </td>
        <td
          class="whitespace-nowrap py-5 px-4 text-sm truncate max-w-[300px] flex items-center"
        >
          <span v-tooltip="ticket.title" class="truncate pt-1">
            {{ ticket.title }}
          </span>
        </td>
        <td
          class="whitespace-nowrap text-left py-5 px-4 text-sm max-w-44 truncate capitalize"
        >
          <span
            class="capitalize"
            v-tooltip="capitalizedDepartment(ticket.department)"
          >
            {{ ticket.department }}
          </span>
        </td>

        <td class="whitespace-nowrap text-left max-w-44 truncate py-5 px-4 text-sm" v-tooltip=" `${ticket.latest_reply.created_at_human} by ${ticket.latest_reply.reply_by}`">
          {{
            ticket.latest_reply
              ? `${ticket.latest_reply.created_at_human} by ${ticket.latest_reply.reply_by}`
              : "-"
          }}
        </td>
        <td class="whitespace-nowrap text-left py-5 px-4 text-sm">
          {{ ticket.formatted_created_at }}
        </td>
        <td class="whitespace-nowrap text-center py-5 px-4 pr-6 text-sm">
          <div class="flex justify-end items-center gap-2">
            <router-link
              :to="{ name: 'adminTicketShow', params: { id: ticket.id } }"
              v-tooltip="'View'"
              :class="[
                isLightColor
                  ? 'bg-custom-200 text-custom-700'
                  : 'bg-custom-50 text-custom-500',
                ' flex items-center p-1 rounded-md',
              ]"
            >
              <span
                class="material-symbols-outlined text-[20px]"
              >
                visibility
              </span>
            </router-link>

            <button
              @click="openModal(ticket.id, 'closed')"
              class="flex items-center font-bold"
            >
              <span
                v-tooltip="'Closed Ticket'"
                class="material-symbols-outlined text-[20px] bg-red-50 p-1 rounded-md text-red-500"
                v-if="ticket.status === 'open'"
              >
                cancel
              </span>
            </button>

            <button
              @click="openModal(ticket.id, 'open')"
              class="flex items-center font-bold"
            >
              <span
                v-tooltip="'Open Ticket'"
                v-if="ticket.status === 'closed'"
                class="material-symbols-outlined text-[20px] bg-blue-50 p-1 rounded-md text-blue-500"
              >
                cached
              </span>
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
          <div v-if="ticketData.length > 0" class="mt-5 sm:mt-0">
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
            {{ refreshing ? "Please Wait" : "No Tickets Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>
  <Confirmation
    :show="openConfirmation"
    @closeModal="closeModal"
    :showLoader="showLoader"
    :confirmationTitle="
      ticketStatus == 'closed' ? 'Close Ticket' : 'Open Ticket'
    "
    :submitBtnTitle="`Yes I'm Sure`"
    @confirm="updateTicketStatus"
  >
    <template #icon>
      <span class="material-symbols-outlined text-[22px] text-red-500"
        >confirmation_number</span
      >
    </template>
    <template #content>
      <p class="text-tiny text-gray-600">
        Would you like to
        {{ ticketStatus == "closed" ? "close" : "open" }} the ticket?
      </p>
      <p class="my-5 text-tiny font-medium" v-if="ticketStatus === 'closed'">
        Once closed, the ticket will be marked as resolved.
      </p>
    </template>
  </Confirmation>
</template>
  <script>
export default {
  name: "Ticket",
  data: {
    breadcrumb: {
      title: "Tickets",
      icon: "confirmation_number",
      pages: [
        {
            name: "Tickets"
        }
        ],
    },
    status: "open",
    search: "",
    tabs: [
      { name: "Open Tickets", current: true, status: "open", count: 0 },
      { name: "Closed Tickets", current: false, status: "closed", count: 0 },
    ],
    thead: [
      { title: "ID", classes: "pl-10" },
      { title: "Title" },
      { title: "Department" },
      { title: "Last Response", classes: "whitespace-nowrap" },
      { title: "Created At", classes: "whitespace-nowrap" },
      { title: "Actions", classes: "text-end pr-8" },
    ],
    ticketData: [],
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0,
      next_page_url: null,
      prev_page_url: null,
    },
    refreshing: false,
    per_page: 10,
    open: false,
    department: [
      { name: "Account", value: "account" },
      { name: "Billing", value: "billing" },
      { name: "Technical", value: "technical" },
      { name: "Bug", value: "bug" },
    ],
    serverData: [],
    processing: false,
    openConfirmation: false,
    showLoader: false,
    ticketId: null,
    ticketStatus: null,
  },
  computed: {
    capitalizedDepartment() {
      return (department) => {
        return department.charAt(0).toUpperCase() + department.slice(1);
      };
    },
  },
  methods: {
    changeStatus(tab) {
      this.status = tab.status;
      this.per_page = 10;
      this.fetchTicket();
    },

    async fetchTicket(page = "") {
      this.refreshing = true;
      let url = `/admin/tickets?page=${page}&per_page=${this.per_page}&status=${this.status}&search=${this.search}`;
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.tabs = this.tabs.map((tab) => {
            if (tab.status === "open") {
              return { ...tab, count: data.open_tickets_count };
            }
            if (tab.status === "closed") {
              return { ...tab, count: data.closed_tickets_count };
            }
            return tab;
          });

          this.ticketData = data.tickets.data;
          this.pagination = {
            current_page: data.tickets.current_page,
            last_page: data.tickets.last_page,
            per_page: data.tickets.per_page,
            total: data.tickets.total,
            next_page_url: data.tickets.next_page_url,
            prev_page_url: data.tickets.prev_page_url,
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
      this.fetchTicket(1);
    },
    handlePageChange(newPage) {
      this.fetchTicket(newPage);
    },
    openModal(ticketId, status) {
      this.openConfirmation = true;
      this.ticketId = ticketId;
      this.ticketStatus = status;
    },
    closeModal() {
      this.openConfirmation = false;
      this.showLoader = false;
      this.ticketId = null;
      this.ticketStatus = null;
    },
    removeAttachment() {
      this.attachment = null;
      this.fileURL = null;
      this.fileName = "";
      this.ticket.attachment = null;
    },
    async updateTicketStatus() {
      this.showLoader = true;
      await this.$axios
        .patch(`/admin/tickets/${this.ticketId}/${this.ticketStatus}`)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.fetchTicket();
          this.closeModal();
        })
        .catch(({ response }) => {
          this.closeModal();
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.showLoader = false;
        });
    },
    clearSearch() {
      this.search = "";
      this.handleSearch();
    },
  },
  mounted() {
    this.handleSearch = this.$debounce(this.fetchTicket, 1000);
    this.fetchTicket();
  },
};
</script>