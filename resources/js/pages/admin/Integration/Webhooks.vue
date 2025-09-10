<template>
    <Breadcrumb :breadcrumb="breadcrumb" />
    <div class="sm:flex justify-between items-center gap-5">
        <h1 class="text-xl font-medium text-[#31363f] flex items-center">
            Webhooks
        </h1>
        <div class="flex gap-5 items-center mt-4 sm:mt-0">
            <Button @click="openCreateWebhookModal"> Create </Button>
            <div class="flex items-center gap-5 sm:mt-0">
                <button
                    @click="fetchWebhooks(pagination.current_page)"
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
    <div class="h-full mt-5">
        <Table :head="thead" v-if="webhooks.length > 0">
            <tr
                class="border-b border-primary text-[#2c3138] text-[13px]"
                v-for="webhook in webhooks"
                :key="webhook.id"
            >
                <td class="py-4 px-4 font-medium">#{{ webhook.id }}</td>
                <td class="whitespace-nowrap py-4 px-4 max-w-44 truncate" v-tooltip="webhook.name">
                    {{ webhook.name }}
                </td>
                <td class="whitespace-nowrap py-4 px-4 max-w-60 truncate" >
                    <span v-tooltip="webhook.url">{{ webhook.url }}</span>
                </td>
                <td class="whitespace-nowrap py-4 px-4 flex gap-2">
                    <template v-if="webhook.events.length > 0">
                        <template v-for="(event,key) in webhook.events.slice(0, 2)" :key="key">
                            <Badge :badgeTitle="event.name" variant="dark" :rounded="true"/>
                        </template>
                        <button
                            v-if="webhook.events.length > 2"
                            @click="openShowEventsModal(webhook)"
                            class="bg-gray-50 text-gray-500 rounded-full px-2.5 py-1 ring-1 ring-gray-200 text-xs"
                        >
                            +{{ webhook.events.length - 2 }} more
                        </button>
                    </template>
                    <template v-else>
                        <span>-</span>
                    </template>
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-center">
                    <Switch
                        @update:modelValue="toggleWebhook(webhook.id, $event)"
                        v-model="webhook.status"
                        :class="[
                            webhook.status
                                ? isLightColor
                                ? 'bg-custom-700'
                                : 'bg-custom-500'
                                : 'bg-gray-200',
                            'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0',
                        ]"
                    >
                        <span class="sr-only">Use setting</span>
                        <span
                        aria-hidden="true"
                        :class="[
                            webhook.status ? 'translate-x-5' : 'translate-x-0',
                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                        ]"
                        />
                    </Switch>
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-center">
                    <div class="flex items-center gap-2 justify-center">
                        <router-link :to="{
                            name: 'WebhookHistory',
                            params: {
                                id: webhook.id
                            }
                         }" v-tooltip="'Webhook History'">
                            <span class="material-symbols-outlined bg-orange-100 text-orange-500 rounded-md p-1.5 text-[20px]">
                                history
                            </span>
                        </router-link>
                        <button
                            v-tooltip="webhook.status === 1 ? 'Send Test' : 'Enable webhook to trigger.'"
                            @click="sendTest(webhook.id)"
                            :disabled="webhook.status !== 1"
                            :class="[
                              'rounded-md p-1.5 text-[20px] material-symbols-outlined',
                                webhook.status === 1
                                ? 'bg-blue-100 text-blue-500 cursor-pointer'
                                : 'bg-blue-100 text-blue-400 opacity-50 cursor-not-allowed'
                            ]"
                          >
                          arrow_outward
                        </button>
                        <button v-tooltip="'Update'" @click="openUpdateWebhookModal(webhook)">
                            <span class="material-symbols-outlined bg-custom-50 text-custom-500 rounded-md p-1.5 text-[20px]">
                                edit_square
                            </span>
                        </button>
                         <button v-tooltip="'Delete'" @click="openWebhookDeleteConfirmation(webhook)"> 
                            <span class="material-symbols-outlined bg-red-100 text-red-500 rounded-md p-1.5 text-[20px]">
                                delete
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
                        'sm:flex gap-3 py-5 px-4',
                    ]"
                >
                    <div v-if="pagination.total > 10">
                        <Perpage v-model="per_page" :initialPerPage="pagination.per_page" @changePage="handlePerPageChange" />
                    </div>
                    <div v-if="webhooks.length > 0" class="mt-5 sm:mt-0">
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
                        {{ refreshing ? "Please Wait" : "No Webhooks Found" }}
                    </td>
                </tr>
            </Table>
        </template>
    </div>

    <Confirmation
        :show="showWebhookDeleteConfirmation"
        @closeModal="closeWebhookDeleteConfirmation"
        :showLoader="showLoader"
        :confirmationTitle="'Delete Webhook'"
        :submitBtnTitle="`Yes I'm Sure`"
        @confirm="deleteWebhook"
    >
        <template #icon>
            <span class="material-symbols-outlined text-[22px] text-red-500">delete</span>
        </template>
        <template #content>
            <p class="my-5 text-tiny font-medium" v-if="this.currentWebhook"> 
                Are you sure you want to delete this webhook {{ this.currentWebhook.name }}?
            </p>
        </template>
    </Confirmation>

    <Modal
        :show="showEventsModal"
        @closeModal="closeShowEventsModal"
        :modalTitle="'Events'"
        :modelIcon="'bolt'"
    >
        <div class="flex flex-wrap gap-x-2 gap-y-3 mt-1" v-if="currentWebhook">
            <template v-for="(event,key) in currentWebhook.events" :key="key">
                <Badge :badgeTitle="event.name" variant="dark" :rounded="true"/>
            </template>
        </div>
    </Modal>

    <Modal
        :show="showCreateOrUpdateWebhookModal"
        @closeModal="closeCreateOrUpdateWebhookModal"
        :modalTitle="isUpdate ? 'Update Webhook' : 'Create Webhook'"
        :modelIcon="'webhook'"
        :customClass="['overflow-visible md:max-w-2xl']"
    >
        <div class="md:max-h-[550px] overflow-auto">
            <div class="">
                <label
                    for="name"
                    class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
                >
                    Name
                </label>
                <input
                    v-model="webhook.name"
                    type="text"
                    name="name"
                    id="name"
                    class="border text-gray-900 text-sm mt-1 rounded-lg border-primary focus:border-primary focus:ring-0 block w-full p-2"
                    placeholder="e.g. Billing Update Hook"
                />
                <small
                    id="name_message"
                    class="error_message text-red-500 text-xs"
                ></small>
            </div>
            <div class="mt-2.5">
                <label
                    for="name"
                    class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
                >
                    URL
                </label>
                <input
                    v-model="webhook.url"
                    type="text"
                    name="url"
                    id="url"
                    class="border text-gray-900 text-sm mt-1 rounded-lg border-primary focus:border-primary focus:ring-0 block w-full p-2"
                    placeholder="Enter URL"
                />
                <small
                    id="url_message"
                    class="error_message text-red-500 text-xs"
                ></small>
            </div>
            <div class="mt-2.5">
                <label
                    for="name"
                    class="text-tiny font-medium"
                >
                    Secret
                </label>
                <input
                    v-model="webhook.secret"
                    type="text"
                    name="secret"
                    id="secret"
                    class="border text-gray-900 text-sm mt-1 rounded-lg border-primary focus:border-primary focus:ring-0 block w-full p-2"
                    placeholder="Enter Secret"
                />
                <small
                    id="Secret_message"
                    class="error_message text-red-500 text-xs"
                ></small>
            </div>
            <div class="mt-2.5">
                <label
                    for="events"
                    class="text-tiny  after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
                >
                    Events
                </label>
                <multiselect
                    v-model="selectedEvents"
                    :options="events"
                    :multiple="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    :searchable="true"
                    :hideSelected="false"
                    placeholder="Select an events"
                    label="name"
                    track-by="id"
                    class="mt-1"
                    :value="event => event.id"
                    ref="multiselect"
                >
                    <template #caret="{ toggle }">
                        <div class="multiselect__select"  @mousedown.prevent.stop="toggle">
                            <span class="material-symbols-outlined mt-0.5 text-xl">
                                keyboard_arrow_down
                            </span>
                        </div>
                    </template>
                </multiselect>
                <small
                    class="text-red-500 error_message text-xs"
                    id="event_ids_message"
                ></small>
            </div>
            <div class="mt-2.5">
                <label
                    for="status"
                    class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
                >
                    Status
                </label>
                <select
                    id="status"
                    v-model="webhook.status"
                    placeholder="Select a Status"
                    class="w-full rounded-md shadow-sm border-sm border-primary focus:border-primary py-1.5 mt-1 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                >
                    <option :value="true">Enable</option>
                    <option :value="false">Disable</option>
                </select>
                <small
                    id="status_message"
                    class="error_message text-red-500 text-xs"
                ></small>
            </div>
        </div>
        <div class="flex justify-end items-center pt-5 gap-4">
            <button @click="closeCreateOrUpdateWebhookModal" type="button" class="rounded-md border font-medium px-4 py-2 text-center text-sm">
                Cancel
            </button>
            <Button :disabled="savingWebhook" @click="createOrUpdateWebhook">
                <i
                    v-if="savingWebhook"
                    class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
                ></i>
                {{ savingWebhook ? "Please wait" : isUpdate ? "Update" : "Create" }}
            </Button>
        </div>
    </Modal>
</template>

<script>
import { Switch } from "@headlessui/vue";
import Multiselect from "vue-multiselect";

export default {
    name: "Webhooks",
    components: { Switch, Multiselect },
    data() {
        return {
            breadcrumb: {
                icon: "rule_settings",
                pages: [
                    { name: "Integrations" },
                    { name: "Webhooks" }
                ]
            },
            refreshing: false,
            thead: ['ID', 'Name', 'URL', 'Events', {title: 'Status', classes: 'text-center'}, {title: 'Actions', classes: 'text-center'}],
            webhooks: [],
            pagination: null,
            per_page: 10,
            showEventsModal: false,
            currentWebhook: null,
            fetchingEvents: false,
            showCreateOrUpdateWebhookModal: false,
            events: [],
            webhook: {
                name: '',
                url: '',
                secret: '',
                event_ids: [],
                status: true
            },
            savingWebhook: false,
            showWebhookDeleteConfirmation: false,
            showLoader: false,
            isUpdate: false
        }
    },
    mounted(){
        this.fetchWebhooks()
        this.fetchEvents()
    },
    computed: {
        selectedEvents: {
            get() {
                return this.events.filter(event => this.webhook.event_ids.includes(event.id));
            },
            set(selected) {
                this.webhook.event_ids = selected.map(event => event.id);
            }
        }
    },
    methods: {
        openCreateWebhookModal(){
            this.showCreateOrUpdateWebhookModal = true
        },
        closeCreateOrUpdateWebhookModal(){
            this.showCreateOrUpdateWebhookModal = false
            this.isUpdate = false
            this.resetFormData()
        },
        openUpdateWebhookModal(webhook){
            this.setWebhookData(webhook)
            this.showCreateOrUpdateWebhookModal = true
            this.isUpdate = true
        },
        openShowEventsModal(webhook){
            this.currentWebhook = webhook
            this.showEventsModal = true
        },
        closeShowEventsModal(){
            this.showEventsModal = false
            this.currentWebhook = null
        },
        openWebhookDeleteConfirmation(webhook){
            this.currentWebhook = webhook
            this.showWebhookDeleteConfirmation = true
        },
        closeWebhookDeleteConfirmation(){
            this.showWebhookDeleteConfirmation = false
            this.showLoader = false
            this.currentWebhook = null
        },
        handlePageChange(newPage) {
            this.fetchWebhooks(newPage);
        },
        handlePerPageChange() {
            this.fetchWebhooks(1);
        },
        setWebhookData(webhook){
            this.webhook.id = webhook.id
            this.webhook.name = webhook.name
            this.webhook.secret = webhook.secret
            this.webhook.url = webhook.url
            this.webhook.status = webhook.status ? true : false
            this.webhook.event_ids = webhook.events ? webhook.events.map(event => event.id) : []
        },
        resetFormData(){
            this.webhook.name = ''
            this.webhook.url = ''
            this.webhook.secret = ''
            this.webhook.event_ids = ''
            this.webhook.status = true
            delete this.webhook.id
        },
        async fetchWebhooks(page = 1){
            this.refreshing = true
            await this.$axios.get(`/admin/webhooks?per_page=${this.per_page}&page=${page}`).then(({data}) => {
                this.webhooks = data.webhooks.data
                this.pagination = data.webhooks
            }).catch(({ response }) => {
                this.$toast.error(response.data.message);
            }).finally(() => {
                this.refreshing = false;
            });
        },
        async fetchEvents(){
            await this.$axios.get(`/admin/webhooks/events`).then(({data}) => {
                this.events = data.events
            }).catch(({ response }) => {
                this.$toast.error(response.data.message);
            })
        },
        async toggleWebhook(id){
            await this.$axios.patch(`/admin/webhooks/${id}/toggle`).then(({data}) => {
                this.$toast.success(data.message)
            }).catch(({ response }) => {
                this.$toast.error(response.data.message);
            }).finally(() => {
                this.fetchWebhooks()
            })
        },
        async createOrUpdateWebhook(){
            this.savingWebhook = true
            this.hideError()
            let payload = {...this.webhook}
            let url = `/admin/webhooks`
            if(this.isUpdate){
                url = `/admin/webhooks/${this.webhook.id}`
                payload._method = 'PATCH'
            }
            await this.$axios.post(url, payload).then(({data}) => {
                this.$toast.success(data.message)
                this.fetchWebhooks()
                this.closeCreateOrUpdateWebhookModal();
            }).catch(({ response }) => {
                if (response.status === 422) {
                    this.displayError(response.data);
                } else {
                    this.$toast.error(response.data.message);
                    this.closeCreateOrUpdateWebhookModal();
                }
            }).finally(() => {
                this.savingWebhook = false
            })
        },
        async deleteWebhook(){
            this.showLoader = true
            await this.$axios.delete(`/admin/webhooks/${this.currentWebhook.id}`).then(({data}) => {
                this.$toast.success(data.message)
                this.fetchWebhooks()
            }).catch(({ response }) => {
                this.$toast.error(response.data.message);
            }).finally(() => {
                this.closeWebhookDeleteConfirmation()
            })
        },
        async sendTest(id){
            await this.$axios.get(`/admin/webhooks/${id}/test`).then(({data}) => {
                this.$toast.success(data.message)
            }).catch(({ response }) => {
                this.$toast.error(response.data.message);
            })
        }   
    }
}
</script>

<style scoped>
:deep(.multiselect__content-wrapper) {
  max-height: 300px !important;
  overflow-y: auto !important;
}

.md\:max-h-\[550px\] {
  max-height: 90vh;
  overflow: visible;
}
</style>

