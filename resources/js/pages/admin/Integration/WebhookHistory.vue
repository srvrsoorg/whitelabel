<template>
    <Breadcrumb :breadcrumb="breadcrumb" />
    <div class="sm:flex justify-between items-center gap-5">
        <div class="flex flex-col gap-1">
            <h1 class="text-xl font-medium text-[#31363f]">
                Webhook Detail
            </h1>
        </div>
        <div class="flex gap-5 items-center mt-4 sm:mt-0">
            <div class="flex items-center gap-5 sm:mt-0">
                <button
                    @click="fetchLogs(pagination.current_page)"
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
    <div class="h-full border rounded-md border-gray-200 w-full p-4 px-5 space-y-4 my-4">
        <div class="grid grid-cols-1 sm:grid-cols-12 xl:gap-5 gap-x-14">
            <label class="text-gray-500 text-sm font-medium">Name</label>
              <div class="sm:col-span-11 text-sm font-medium break-words">
                <span v-tooltip="webhook.name">{{ webhook.name }}</span>
              </div>
          </div>
        <div class="grid grid-cols-1 sm:grid-cols-12 xl:gap-5 gap-x-14">
          <label class="text-gray-500 text-sm font-medium">URL</label>
        <div class="sm:col-span-10 text-sm font-medium break-all">
          <span v-tooltip="webhook.url">{{ webhook.url }}</span>
        </div>
    </div>
        <div class="grid grid-cols-1 sm:grid-cols-12 xl:gap-5 gap-x-14">
          <label class="text-gray-500 text-sm font-medium sm:mb-0 mb-2">Events</label>
            <div class="sm:col-span-11 text-sm font-medium flex flex-wrap gap-2">
              <span
                v-for="event in displayedEvents"
                :key="event.id"
                class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition-colors"
              >
                {{ event.name }}
              </span>
          
              <button
                v-if="webhook.events.length > (windowWidth < 640 ? 5 : 8)"
                @click="handleMoreClick"
                class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 text-sm font-medium hover:bg-gray-200 cursor-pointer transition-colors"
              >
                {{ showAllEvents && windowWidth >= 640 
                    ? '- Show less' 
                    : `+${moreCount} more` }}
              </button>
            </div>
        </div>
    </div>
    <div class="flex flex-col gap-1">
            <h1 class="text-xl font-medium text-[#31363f]">
                Webhook History
            </h1>
        </div>
    <div class="h-full mt-5">
        <Table :head="thead" v-if="logs.length > 0">
            <tr
                class="border-b border-primary text-[#2c3138] text-[13px]"
                v-for="log in logs"
                :key="log.id"
            >
                <td class="whitespace-nowrap py-4 px-4 ">
                    {{ log?.payload?.event?.name || '-' }}
                </td>
                <td class="whitespace-nowrap py-4 px-4 ">
                    {{ log.delivered_at }}
                </td>
                <td class="whitespace-nowrap py-4 px-4 flex gap-2">
                    {{ log.response_code }}
                </td>
                <td class="whitespace-nowrap py-4 px-4 capitalize">
                    <span :class="log.status == 'success' ? 'text-green-500' : 'text-red-500'">{{ log.status }}</span>
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-center">
                    <button class="underline text-blue-600" @click="openLogDetailsModal(log)">View Details</button>
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
                    <div v-if="logs.length > 0" class="mt-5 sm:mt-0">
                        <Pagination
                            :pagination="pagination"
                            @page-change="handlePageChange"
                        />
                    </div>
                </div>
            </template>
        </Table>
        <template v-else>
            <TableSkeleton :heads="4" v-if="refreshing" />
            <Table :head="thead" v-else>
                <tr>
                    <td colspan="4" class="text-center text-sm px-6 py-5">
                        {{ refreshing ? "Please Wait" : "No History Found" }}
                    </td>
                </tr>
            </Table>
        </template>
    </div>

    <Modal
      :show="showEventsModal"
      @closeModal="showEventsModal = false"
      :modalTitle="'All Events'"
      :customClass="['md:max-w-lg']"
    >
      <div class="flex flex-wrap gap-2">
        <span
          v-for="event in webhook.events"
          :key="event.id"
          class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-sm font-medium"
        >
          {{ event.name }}
        </span>
      </div>
    </Modal>
    <Modal 
        :show="showLogDetailsModal"
        @closeModal="closeLogDetailsModal"
        :modalTitle="'Delivery Details'"
        :modelIcon="'integration_instructions'"
        :customClass="['md:max-w-2xl']"
    >
        <template #titleExtra>
            <div class="flex items-center capitalize sm:gap-3">
              <span 
                v-if="currentLog"
                :class="[
                  currentLog.status === 'success' 
                    ? 'sm:bg-green-100 sm:text-green-500 sm:border-green-200'
                    : 'sm:bg-red-100 sm:text-red-500 sm:border-red-200',
                  'px-2.5 py-0.5 rounded-full font-medium flex items-start sm:items-center gap-1 sm:border'
                ]"
              >
                <span 
                  class="sm:hidden material-symbols-outlined text-[12px] rounded-full"
                :class="currentLog.status === 'success' ? 'bg-green-500 text-green-500' : 'bg-red-500 text-red-500'"
                >
                  circle
                </span>
                <span class="relative top-px text-[12px] hidden sm:inline">
                  {{ currentLog.status }}
                </span>
              </span>
            </div>
        </template>
        <div>
            <div class="bg-gray-50 rounded-lg px-4 py-3 my-1.5 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-x-14">
                  <label class="text-gray-500 text-sm font-medium md:col-span-2">
                    Name
                  </label>
                  <div class="sm:col-span-9 text-sm font-medium break-words">
                    <span v-tooltip="webhook.name">
                      {{ webhook.name }}
                    </span>
                  </div>
                </div>
              
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-x-14">
                  <label class="text-gray-500 text-sm font-medium md:col-span-2">
                    URL
                  </label>
                  <div class="md:col-span-9 text-sm font-medium break-all">
                    <span v-tooltip="webhook.url">
                      {{ webhook.url }}
                    </span>
                  </div>
                </div>
              
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-x-14">
                  <label class="text-gray-500 text-sm font-medium md:col-span-2">
                    Event
                  </label>
                  <div class="md:col-span-9 text-sm font-medium break-all">
                    <span v-tooltip="currentLog?.payload?.event?.name">
                      {{ currentLog?.payload?.event?.name || '-' }}
                    </span>
                  </div>
                </div>
            </div>
            <nav class="flex gap-8 w-full" aria-label="Tabs">
                <div class="border-b border-gray-200 text-sm flex space-x-5 w-full">
                    <button
                        @click="changeCurrentTab(tab)"
                        v-for="tab in tabs"
                        :key="tab"
                        :class="[
                            tab.status === currentTab
                            ? isLightColor
                                ? 'border-b-2 text-custom-700 border-custom-700'
                                : 'border-b-2 text-custom-500 border-custom-500'
                            : '',
                            'whitespace-nowrap text-tiny flex gap-2 px-1.5 py-2 font-medium',
                        ]"
                    >
                        <span>{{ tab.name }}</span>
                    </button>
                </div>
            </nav>
            <div class="mt-5" v-if="currentLog">
                <div v-if="currentTab==='response'">
                    <div class="bg-slate-900 text-slate-100 rounded-lg p-4 overflow-auto max-h-[500px] text-sm">
                        <template v-if="wrapValue(currentLog.response_body).type === 'pre'">
                            <pre>{{ wrapValue(currentLog.response_body).content }}</pre>
                        </template>
                        <template v-else-if="wrapValue(currentLog.response_body).type === 'html'">
                            <div v-html="wrapValue(currentLog.response_body).content"></div>
                        </template>
                    </div>
                </div>
                <div v-else-if="currentTab==='request_headers'">
                    <div class="bg-slate-900 text-slate-100 rounded-lg p-4 overflow-auto max-h-[500px] text-sm">
                        <template v-if="wrapValue(currentLog.request_headers).type === 'pre'">
                            <pre>{{ wrapValue(currentLog.request_headers).content }}</pre>
                        </template>
                        <template v-else-if="wrapValue(currentLog.request_headers).type === 'html'">
                            <div v-html="wrapValue(currentLog.request_headers).content"></div>
                        </template>
                    </div>
                </div>

                <div v-else>
                    <div class="bg-slate-900 text-slate-100 rounded-lg p-4 overflow-auto max-h-[500px] text-sm">
                        <template v-if="wrapValue(currentLog.payload).type === 'pre'">
                            <pre>{{ wrapValue(currentLog.payload).content }}</pre>
                        </template>
                        <template v-else-if="wrapValue(currentLog.payload).type === 'html'">
                            <div v-html="wrapValue(currentLog.payload).content"></div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script>

export default {
    data(){
        return{
            breadcrumb: {
                icon: "webhook",
                pages: [
                    { name: "Integrations"},
                    { name: "Webhook", path: { name: "Webhooks" } },
                    { name: "History" }
                ]
            },
            tabs: [
                { name: "Payload", current: false, status: "payload", count: 0 },
                { name: "Headers", current: false, status: "request_headers", count: 0 },
                { name: "Response", current: true, status: "response", count: 0 },
            ],
            currentTab: 'response',
            webhook: {
              events: []
            },
            thead: ['Event','Attempted Time', 'Response Code', 'Status', {title: 'Delivery Details', classes: 'text-center'}],
            logs: [],
            pagination: null,
            per_page: 10,
            refreshing: false,
            currentLog: null,
            showLogDetailsModal: false,
            showAllEvents: false,
            showEventsModal: false,
            windowWidth: window.innerWidth
        }
    },
    computed: {
        responseBodyDisplay() {
            const raw = this.currentLog?.response_body ?? '';
            return raw.replace(/^\s*\n/, ''); 
        },
        payloadDisplay() {
            try {
                return JSON.stringify(this.currentLog?.payload ?? {}, null, 2);
            } catch {
                return String(this.currentLog?.payload ?? '');
            }
        },
        displayedEvents() {
            if (this.windowWidth < 640) {
              return this.webhook.events.slice(0, 5);
            }
            return this.showAllEvents
              ? this.webhook.events
              : this.webhook.events.slice(0, 8);
          },
          moreCount() {
            if (this.windowWidth < 640) {
              return this.webhook.events.length - 5;
            }
            return this.webhook.events.length - 8;
          }
    },
    created(){
        this.fetchLogs();
        this.fetchWebhook();
        window.addEventListener('resize', this.updateWindowWidth);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.updateWindowWidth);
    },
    methods: {
        isObject(val) {
            return val && typeof val === 'object' && !Array.isArray(val);
        },
        isArray(val) {
            return Array.isArray(val);
        },
        isBoolean(val) {
            return typeof val === 'boolean';
        },
        isNumber(val) {
            return typeof val === 'number';
        },
        isString(val) {
            return typeof val === 'string';
        },
        isHtml(val) {
            // minimal HTML structure detection
            return this.isString(val) && /<\/?[a-z][\s\S]*>/i.test(val);
        },
        pretty(val) {
            try {
                return JSON.stringify(val, null, 2);
            } catch {
                return String(val);
            }
        },
        updateWindowWidth() {
            this.windowWidth = window.innerWidth;
            if (this.windowWidth >= 640 && this.showEventsModal) {
            this.showEventsModal = false;
          }
        },
        handleMoreClick() {
            if (this.windowWidth < 640) {
              this.showEventsModal = true;
            } else {
              this.showAllEvents = !this.showAllEvents;
            }
        },
        wrapValue(val) {
            if (this.isObject(val) || this.isArray(val)) {
                return { type: 'pre', content: this.pretty(val) };
            }
            if (this.isHtml(val)) {
                return { type: 'html', content: val };
            }
            if (this.isBoolean(val) || this.isNumber(val)) {
                return { type: 'pre', content: String(val) };
            }
            if (this.isString(val)) {
                return { type: 'pre', content: val };
            }
            return { type: 'pre', content: String(val) };
        },
        openLogDetailsModal(log){
            this.currentLog = log
            this.showLogDetailsModal = true
        },
        closeLogDetailsModal(){
            this.showLogDetailsModal = false
            this.currentLog = null
            this.currentTab = 'response'
        },
        changeCurrentTab(tab){
            this.currentTab = tab.status
        },
        handlePageChange(newPage) {
            this.fetchLogs(newPage);
        },
        handlePerPageChange() {
            this.fetchLogs(1);
        },
        async fetchLogs(page = 1){
            this.refreshing = true
            await this.$axios.get(`/admin/webhooks/${this.$route.params.id}/logs?per_page=${this.per_page}&page=${page}`).then(({data}) => {
                this.logs = data.logs.data
                this.pagination = data.logs
            }).catch(({ response }) => {
                this.$toast.error(response.data.message);
            }).finally(() => {
                this.refreshing = false;
            });
        },
        async fetchWebhook() {
          await this.$axios.get(`/admin/webhooks/${this.$route.params.id}`)
            .then(({data}) => {
              this.webhook = {
                ...data.webhook,
                events: data.webhook?.events || []
              };
            })
            .catch(error => {
              this.$toast.error(error.response?.data?.message);
            });
        }
    }
}
</script>