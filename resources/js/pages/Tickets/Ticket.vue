<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="sm:flex justify-between items-center gap-5">
    <h1 class="text-xl font-medium text-[#31363f] flex items-center">
      Tickets
    </h1>
    <div class="sm:flex gap-5 items-center mt-4 sm:mt-0">
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
              <span v-tooltip="'Clear'" class="material-symbols-outlined text-[20px]">close</span>
            </button>
        <div
          class="pointer-events-none border rounded-r-md border-primary bg-[#F6F6F6] absolute inset-y-0 text-gray-500 right-0 flex items-center px-2.5"
        >
          <span class="material-symbols-outlined text-[22px]"> search </span>
        </div>
      </div>
      <div class="flex items-center justify-end gap-5 mt-5 sm:mt-0">
        <button
          @click="fetchTicket(pagination.current_page)"
          :class="[
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
        <Button @click="openModal"> Create </Button>
      </div>
    </div>
  </div>
  <div class="border-b border-gray-200 my-4">
    <nav class="flex gap-8" aria-label="Tabs">
      <div class="border-gray-200 text-sm flex xs:space-x-4 space-x-2 xl:space-x-7">
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
              'rounded-full  px-2.5 py-[1px]  text-[12px] flex items-center',
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
          class="whitespace-nowrap py-5 px-4 text-sm max-w-[300px] flex truncate"
        >
          <span class="truncate pt-1" v-tooltip="ticket.title">
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
        <td
          class="whitespace-nowrap text-left max-w-44 truncate py-5 px-4 text-sm"
        >
         <span v-tooltip="ticket?.latest_reply ? `${ticket.latest_reply.created_at_human} by ${ticket.latest_reply.reply_by}` : '-'">
           {{
            ticket?.latest_reply
              ? `${ticket.latest_reply.created_at_human} by ${ticket.latest_reply.reply_by}`
              : "-"
          }}
         </span>
        </td>
        <td class="whitespace-nowrap text-left py-5 px-4 text-sm">
          {{ ticket.formatted_created_at }}
        </td>
        <td class="whitespace-nowrap text-center py-5 px-4 text-sm">
          <div class="flex items-center gap-2">
            <router-link
              :to="{ name: 'ticketshow', params: { id: ticket.id } }"
              :class="[
                isLightColor
                  ? 'bg-custom-200 text-custom-700'
                  : 'bg-custom-50 text-custom-500',
                ' flex items-center p-1 rounded-md',
              ]"
            >
              <span
                class="material-symbols-outlined text-[20px]"
                v-tooltip="'View'"
              >
                visibility
              </span>
            </router-link>
            <button
              @click="openConfirmationModal(ticket.id, 'closed')"
              class="flex items-center font-bold"
            >
              <span
                v-tooltip="'Close Ticket'"
                class="material-symbols-outlined text-[20px] bg-red-50 p-1 rounded-md text-red-500"
                v-if="ticket.status === 'open'"
              >
                cancel
              </span>
            </button>

            <button
              @click="openConfirmationModal(ticket.id, 'open')"
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
  <Modal
    :customClass="['md:max-w-3xl']"
    :modelIcon="'confirmation_number'"
    :modalTitle="'Create Ticket'"
    :openModal="open"
    @closeModal="closeModal"
  >
    <div class="grid md:grid-cols-3 grid-cols-1 sm:gap-5 gap-3 mb-5">
      <div>
        <label
          class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
          >Title</label
        >
        <div class="mt-2">
          <input
            v-model="ticket.title"
            type="text"
            id="title"
            placeholder="Enter Title"
            class="w-full block rounded-md border border-primary focus:border-primary py-1.5 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          />
        </div>
        <small
          id="title_message"
          class="text-red-500 error_message text-xs"
        ></small>
      </div>
      <div>
        <label
          class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
          >Department</label
        >
        <select
          v-model="ticket.department"
          id="department"
          class="w-full mt-2 block rounded-md border border-primary focus:border-primary py-1.5 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
        >
          <option value="" disabled>Select a Department</option>
          <option
            v-for="department in department"
            :key="department"
            :value="department.value"
          >
            {{ department.name }}
          </option>
        </select>
        <small
          id="department_message"
          class="text-red-500 error_message text-xs"
        ></small>
      </div>
      <div>
        <label class="font-medium text-tiny">Server(optional)</label>
        <select
          v-model="ticket.server_id"
          id="server_id"
          class="w-full mt-2 block rounded-md border border-primary focus:border-primary py-1.5 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
        >
          <option value="">Select Servers</option>
          <option
            v-for="server in serverData"
            :key="server.id"
            :value="server.id"
            v-tooltip="`${server.name} (${server.ip})`"
          >
            {{ truncateServerName(server.name) }} ({{
              truncateServerIP(server.ip)
            }})
          </option>
        </select>
        <small
          id="server_id_message"
          class="text-red-500 error_message text-xs"
        ></small>
      </div>
    </div>
    <div class="my-5">
      <label
        class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
        >Description</label
      >
      <textarea
        v-model="ticket.description"
        id="description"
        cols="30"
        rows="6"
        placeholder="Describe the query/question/issue in detail"
        class="w-full mt-2 block rounded-md border border-primary focus:border-primary py-1.5 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
      ></textarea>
      <small
        id="description_message"
        class="text-red-500 error_message text-xs"
      ></small>
    </div>
    <div class="mt-5 flex justify-between items-start gap-5">
      <div>
        <div class="relative inline-block cursor-pointer">
          <input
            @change="handleFileUpload"
            type="file"
            id="attachment"
            accept=".jpeg,.jpg,.png,.webp"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
          />
          <button
            :class="[
              'flex justify-center text-sm items-center gap-1 cursor-pointer',
              isLightColor ? 'text-custom-700' : 'text-custom-500',
            ]"
          >
            <span class="material-symbols-outlined text-lg"> upload </span>
            <span class="">Add Attchment</span>
          </button>
        </div>
        <div v-if="fileURL" class="flex flex-col">
          <div class="flex items-center">
            <p class="text-sm inline">Selected file: {{ fileName }}</p>
            <button @click="removeAttachment">
              <span
                class="material-symbols-outlined text-tiny text-red-500 font-bold"
              >
                close
              </span>
            </button>
          </div>
        </div>
        <small
          id="attachment_message"
          class="text-red-500 error_message text-xs block"
        ></small>
      </div>
      <div class="flex items-center gap-4">
      <button @click="closeModal" type="button" class="rounded-md border font-medium px-4 py-2 text-center text-sm">
        Cancel
      </button>
      <Button :disabled="processing" @click="createTicket">
        <i
          v-if="processing"
          class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
        ></i>
        {{ processing ? "Please wait" : "Create" }}
      </Button>
      </div>
    </div>
  </Modal>
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
        {{ ticketStatus == "closed" ? "close" : "open" }} your ticket?
      </p>
      <div class="my-5" v-if="ticketStatus == 'closed'">
        <p class="text-tiny font-medium">
          If your query is answered or issue has been resolved you can close
          your ticket.
        </p>
      </div>
    </template>
  </Confirmation>
</template>
<script>
export default {
  name: "Ticket",
  data: {
    breadcrumb: {
      icon: "confirmation_number",
      pages: [{ name: "Tickets" }],
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
      { title: "Actions", classes: "text-end" },
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
    ticket: {
      title: "",
      department: "",
      server_id: "",
      description: "",
      attachment: null,
    },
    attachment: null,
    fileURL: null,
    fileName: "",
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
    truncateServerName(name) {
      const maxLength = 10; // Set the desired maximum length for truncation
      if (name.length > maxLength) {
        return name.substring(0, maxLength) + "...";
      }
      return name;
    },
    truncateServerIP(ip) {
      const maxLength = 12; // Set the desired maximum length for truncation
      if (ip.length > maxLength) {
        return ip.substring(0, maxLength) + "...";
      }
      return ip;
    },

    changeStatus(tab) {
      this.status = tab.status;
      this.per_page = 10;
      this.fetchTicket();
    },

    async fetchTicket(page = "") {
      this.refreshing = true;
      let url = `/user/tickets?page=${page}&per_page=${this.per_page}&status=${this.status}&search=${this.search}`;
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
    openConfirmationModal(ticketId, status) {
      this.openConfirmation = true;
      this.ticketId = ticketId;
      this.ticketStatus = status;
    },
    openModal() {
      this.open = true;
    },
    closeModal() {
      this.open = false;
      this.ticket.title = "";
      this.ticket.department = "";
      this.ticket.server_id = "";
      this.ticket.description = "";
      this.ticket.attachment = null;
      this.attachment = null;
      this.fileURL = null;
      this.fileName = "";
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
    handleFileUpload(event) {
      const selectedFile = event.target.files[0];
      if (selectedFile) {
        this.ticket.attachment = selectedFile;
        this.fileName = selectedFile.name;
        this.fileURL = URL.createObjectURL(selectedFile);
      }
    },
    async createTicket() {
      if (this.processing) return;
      this.processing = true;
      const formdata = new FormData();
      formdata.append("title", this.ticket.title);
      formdata.append("department", this.ticket.department);
      formdata.append("server_id", this.ticket.server_id);
      formdata.append("description", this.ticket.description);
      if (this.ticket.attachment) {
        formdata.append("attachment", this.ticket.attachment);
      }
      this.hideError();
      await this.$axios
        .post(`/user/tickets`, formdata)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.closeModal();
          this.fetchTicket();
        })
        .catch(({ response }) => {
          if (response.status !== 422) {
            this.$toast.error(response.data.message);
            this.closeModal();
          }
          this.displayError(response.data);
        })
        .finally(() => {
          this.processing = false;
        });
    },
    async updateTicketStatus() {
      this.showLoader = true;
      await this.$axios
        .patch(`/user/tickets/${this.ticketId}/${this.ticketStatus}`)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.fetchTicket();
          this.closeModal();
        })
        .catch(({ response }) => {
          this.closeModal();
          this.$toast.error(response.data.message);
        });
    },
    async fetchServerData() {
      await this.$axios
        .get("/user/server-ids")
        .then(({ data }) => {
          this.serverData = data.server_ids;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
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
    this.fetchServerData();
  },
};
</script>