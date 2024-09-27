<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div
    :class="tickets && tickets.status === 'open' ? 'xs:flex' : 'flex'"
    class="justify-between items-center mb-3.5"
  >
    <div class="">
      <router-link
        :to="{ name: 'tickets' }"
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'flex items-center gap-1.5 text-tiny font-medium',
        ]"
      >
        <span class="material-symbols-outlined"> keyboard_backspace </span>
        <span>Back to Tickets</span>
      </router-link>
    </div>
    <div class="flex items-center justify-end mt-2 xs:mt-0 gap-5">
      <Button
        @click="openConfirmation(tickets)"
        class="bg-red-500 text-white text-[15px] rounded-md"
      >
        {{
          tickets && tickets.status === "open" ? " Close Ticket" : "Open Ticket"
        }}
      </Button>
      <button
        @click="fetchMessage()"
        type="button"
        class="bg-[#F6F6F6] items-center flex border-gray-200 border rounded-md text-gray-500 text-sm p-1.5 text-center"
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
      <button class="xl:hidden" @click="toggle">
        <span
          :class="[
            isLightColor
              ? 'text-custom-700 bg-custom-300'
              : 'bg-custom-50 text-custom-500',
            'material-symbols-outlined p-2 text-[22px] rounded-md',
          ]"
        >
          account_circle
        </span>
      </button>
    </div>
  </div>
  <div class="flex xl:gap-5">
    <div class="flex-1 col-span-5 flex flex-col h-screen element">
      <div
        class="flex-1 p-4 py-6 shadow-sm border border-primary rounded-md flex flex-col !min-h-80 h-screen element1"
      >
        <div v-if="tickets !== null" class="space-y-1.5">
          <h1 class="font-semibold text-lg">#{{ tickets.id }}</h1>
          <p class="text-md break-all line-clamp-1 max-w-fit" v-tooltip="tickets.title">
            {{ tickets.title }}
          </p>
        </div>
        <hr class="my-6" />
        <perfectScrollbar class="flex-1" ref="scrollBottom">
          <template v-if="replyData.length > 0">
            <div v-for="reply in replyData" :key="reply" class="text-tiny pr-4">
              <div
                v-if="reply.support_agent_id !== null"
                class="flex items-start gap-3 mb-5"
              >
                <div
                  class="w-9 h-9 min-w-9 min-h-9 rounded-full"
                  v-if="reply.support_agent"
                >
                  <img
                    :src="reply.support_agent.avatar"
                    alt=""
                    class="h-9 w-9 rounded-full"
                  />
                </div>
                <div class="flex sm:max-w-[80%] flex-col space-y-2">
                  <div class="flex gap-3 items-center">
                    <h1 class="font-medium">Admin</h1>
                    <span class="text-sm text-gray-500">{{
                      reply.created_at_human
                    }}</span>
                  </div>
                  <div
                    class="bg-[#F6F6F6] text-sm text-wrap break-all rounded-lg p-3"
                  >
                    <p v-html="formatReplyText(reply.reply)" class=""></p>
                    <button
                      v-if="reply.attachment !== null"
                      @click="openImage(reply)"
                      class="flex items-center whitespace-nowrap gap-1.5 my-1"
                      :class="[
                        isLightColor ? 'text-custom-700' : 'text-custom-500',
                      ]"
                    >
                      <div class="max-w-28 max-h-28 overflow-hidden">
                        <img
                          :src="reply.attachment"
                          alt="no image"
                          class="object-cover w-full h-full"
                        />
                      </div>
                    </button>
                  </div>
                </div>
              </div>

              <div
                v-else-if="reply.support_agent_id === null"
                class="flex justify-end mb-5"
              >
                <div class="flex justify-end items-start gap-3">
                  <div class="flex sm:max-w-[80%] flex-col space-y-2">
                    <div class="flex justify-end items-center gap-3">
                      <span class="text-sm text-gray-500">{{
                        reply.created_at_human
                      }}</span>
                      <h1 class="font-medium text-end">You</h1>
                    </div>
                    <div
                      class="bg-[#E8F6F4] text-sm text-wrap break-all rounded-lg p-3 px-4"
                    >
                      <p v-html="formatReplyText(reply.reply)" class=""></p>
                      <button
                        v-if="reply.attachment !== null"
                        @click="openImage(reply)"
                        class="flex items-center gap-1.5 my-1"
                        :class="[
                          isLightColor ? 'text-custom-700' : 'text-custom-500',
                        ]"
                      >
                        <div class="max-w-28 max-h-28 overflow-hidden">
                          <img
                            :src="reply.attachment"
                            alt="no image"
                            class="object-cover w-full h-full"
                          />
                        </div>
                      </button>
                    </div>
                  </div>
                  <div class="w-9 h-9 min-w-9 min-h-9 rounded-full" v-if="user">
                    <img
                      :src="user.avatar"
                      alt=""
                      class="h-9 w-9 rounded-full"
                    />
                  </div>
                </div>
              </div>
            </div>
          </template>
          <div v-else class="flex justify-center">
            <p class="text-gray-500 text-tiny">No Reply Data</p>
          </div>
        </perfectScrollbar>
      </div>
      <div
        v-if="tickets && tickets.status !== 'closed'"
        class="flex shadow-sm border border-gray-90 rounded-md mt-5 py-4 px-4"
      >
        <div class="flex items-start gap-2">
          <div class="relative cursor-pointer">
            <input
              @change="handleFileUpload"
              type="file"
              ref="fileInput"
              accept=".jpeg,.jpg,.png,.webp"
              id="attachment"
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            />
            <button
              @click.prevent="triggerFileInput"
              :class="isLightColor ? 'text-custom-700' : 'text-custom-500'"
              class="flex p-1.5 rounded-full justify-center -rotate-45 text-sm items-center cursor-pointer"
            >
              <span class="material-symbols-outlined text-[22px]">
                attachment
              </span>
            </button>
          </div>
          <div v-if="fileURL" class="flex flex-col ml-2">
            <div class="relative max-w-24">
              <button
                @click="removeAttachment"
                class="absolute -top-3 -right-3"
              >
                <span
                  class="material-symbols-outlined text-sm text-red-500 font-bold"
                >
                  close
                </span>
              </button>
              <img
                :src="fileURL"
                alt="Selected Image"
                class="h-16 border rounded-lg p-2"
              />
            </div>
          </div>
        </div>
        <div class="flex-1">
          <textarea
            id="attachment"
            name="attachment"
            ref="sendReply"
            @input="adjustTextareaHeight"
            v-model="sendReply.reply"
            cols="30"
            rows="1"
            @keydown="checkCtrlEnter"
            placeholder="Type your message..."
            class="w-full block py-1.5 placeholder:text-gray-500 placeholder:text-tiny text-sm leading-6 focus:ring-0 auto-resize"
            style="border: none; outline: none"
          ></textarea>
        </div>
        <div class="flex justify-center items-end">
          <Button
            :class="[
              textColorClass,
              'rounded-md !py-1.5 !px-3',
              'flex  gap-1 items-center !text-tiny',
            ]"
            :disabled="!isFormValid || processing"
            @click="sendMessage"
          >
            <div
              :class="[
                isLightColor ? 'text-custom-700' : 'text-white',
                'flex justify-between items-center gap-1',
              ]"
            >
              <template v-if="!processing">
                Send
                <span class="material-symbols-outlined text-[20px]">send</span>
              </template>
              <template v-else>
                Please wait
                <i
                  class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
                ></i>
              </template>
            </div>
          </Button>
        </div>
      </div>
      <div v-if="tickets && tickets.status == 'closed'" class="mt-5">
        <span
          class="text-red-500 bg-red-50 p-2.5 flex gap-1.5 rounded-md text-xs font-medium"
          ><p class="font-medium">Note:</p>
          If you want to send a message, you should first open a ticket to get
          started.
        </span>
      </div>

      <div class="flex items-center">
        <small
          id="attachment_message"
          class="text-red-500 error_message text-xs block"
        ></small>
      </div>
    </div>
    <div class="">
      <SmallSizeContactInfo
        :showData="open"
        @toggleMenu="open = false"
      ></SmallSizeContactInfo>
    </div>
  </div>
  <Confirmation
    :show="openConfirmModal"
    @closeModal="closeModal"
    :showLoader="showLoader"
    :confirmationTitle="confirmationTitle"
    :submitBtnTitle="`Yes I'm Sure`"
    @confirm="updateTicketSatus"
  >
    <template #icon>
      <span class="material-symbols-outlined text-[22px] text-red-500"
        >confirmation_number</span
      >
    </template>
    <template #content>
      <p class="text-tiny text-gray-600">
        Would you like to
        {{ tickets && tickets.status !== "closed" ? "close" : "open" }} your
        ticket?
      </p>
      <p
        class="my-5 text-tiny font-medium"
        v-if="tickets && tickets.status !== 'closed'"
      >
        If your query is answered or issue has been resolved you can close your
        ticket.
      </p>
    </template>
  </Confirmation>
  <Modal
    :show="openModal"
    @closeModal="closeModal"
    :removeBorder="['border-b-0 ']"
    :customClass="['md:max-w-3xl md:max-h-[550px] overflow-scroll']"
  >
    <div class="">
      <img :src="imageUrl" alt="" class="mx-auto w-full" />
    </div>
  </Modal>
</template>

<script>
import SmallSizeContactInfo from "@/components/user/SmallSizeContactInfo.vue";
import { useAuthStore } from "@/store/auth";
import { mapState } from "pinia";
export default {
  name: "TicketShow",
  components: {
    SmallSizeContactInfo,
  },
  data() {
    return {
      breadcrumb: {
        title: "Tickets",
        icon: "confirmation_number",
        pages: [{ name: "View" }],
      },
      openModal: false,
      imageUrl: null,
      tickets: null,
      status: null,
      replyData: [],
      open: false,
      sendReply: {
        reply: "",
        attachment: null,
      },
      fileURL: null,
      fileName: "",
      processing: false,
      openConfirmModal: false,
      showLoader: false,
      confirmationTitle: "",
      processing: false,
      interval: null,
      refreshing: false,
    };
  },
  computed: {
    ...mapState(useAuthStore, ["user"]),
    isFormValid() {
      return this.sendReply.reply.trim() !== "" || this.fileURL !== null;
    },
  },

  methods: {
    toggle() {
      this.open = !this.open;
    },
    async fetchTicketShow() {
      await this.$axios
        .get(`/user/tickets/${this.$route.params.id}`)
        .then(({ data }) => {
          this.tickets = data.ticket;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    async fetchMessage() {
      this.refreshing = true;
      await this.$axios
        .get(`/user/tickets/${this.$route.params.id}/replies`)
        .then(({ data }) => {
          this.replyData = data.replies.replies;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.refreshing = false;
        });
    },
    async sendMessage() {
      this.hideError();
      this.processing = true;
      const formData = new FormData();
      formData.append("reply", this.sendReply.reply);
      if (this.sendReply.attachment) {
        formData.append("attachment", this.sendReply.attachment);
      }
      await this.$axios
        .post(`/user/tickets/${this.$route.params.id}/reply`, formData)
        .then(({ data }) => {
          this.removeAttachment();
          this.sendReply.reply = "";
          this.fetchMessage();
          this.scrollbottom();
          this.resetTextareaHeight();
        })
        .catch(({ response }) => {
          if (response.status !== 422) {
            this.$toast.error(response.data.message);
          }
          this.displayError(response.data);
        })
        .finally(() => {
          this.processing = false;
        });
    },
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
    removeAttachment() {
      this.sendReply.attachment = null;
      this.fileName = null;
      this.fileURL = null;
      this.$refs.fileInput.value = null;
    },
    handleFileUpload(event) {
      const selectedFile = event.target.files[0];
      const validTypes = ["image/jpeg", "image/jpg", "image/png", "image/webp"];
      if (selectedFile && validTypes.includes(selectedFile.type)) {
        this.sendReply.attachment = selectedFile;
        this.fileName = selectedFile.name;
        this.fileURL = URL.createObjectURL(selectedFile);
        this.$refs.sendReply.focus();
      }
    },
    checkCtrlEnter(event) {
      if (event.ctrlKey && event.key === "Enter") {
        if (this.isFormValid && !this.processing) {
          this.sendMessage();
        }
      }
    },
    formatReplyText(text) {
      if (!text) return ""; // Return an empty string if text is null or undefined

      // Function to escape HTML characters
      const escapeHTML = (str) => {
        return str
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#39;");
      };
      let escapedText = escapeHTML(text);
      escapedText = escapedText.replace(/(\r\n|\n|\r|\s){2,}/g, "\n").trim();
      return escapedText.replace(/\n/g, "<br>");
    },
    scrollbottom() {
      this.$nextTick(() => {
        if (this.$refs.scrollBottom && this.$refs.scrollBottom.$el) {
          this.$refs.scrollBottom.$el.scrollTop =
            this.$refs.scrollBottom.$el.scrollHeight;
        }
      });
    },
    adjustTextareaHeight(event) {
      const textarea = event.target;
      textarea.style.height = "auto"; // Reset the height to auto
      textarea.style.height = `${textarea.scrollHeight}px`; // Set the height to the scroll height
    },
    resetTextareaHeight() {
      const textarea = this.$refs.sendReply;
      if (textarea) {
        textarea.style.height = "auto"; // Reset the height to auto
      }
    },
    openConfirmation(ticket) {
      this.openConfirmModal = true;
      if (ticket.status === "closed") {
        this.confirmationTitle = "Open Ticket";
        this.status = "open";
      } else {
        this.confirmationTitle = "Close Ticket";
        this.status = "closed";
      }
    },
    closeModal() {
      this.openConfirmModal = false;
      this.showLoader = false;
      this.confirmationMessage = "";
      this.confirmationTitle = "";
      this.imageUrl = null;
      this.openModal = false;
    },
    async updateTicketSatus() {
      this.showLoader = true;
      await this.$axios
        .patch(`/user/tickets/${this.$route.params.id}/${this.status}`)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.closeModal();
          this.$router.push("/tickets");
        })
        .catch(({ response }) => {
          this.closeModal();
          this.$toast.error(response.data.message);
        });
    },
    openImage(reply) {
      this.openModal = true;
      this.imageUrl = reply.attachment;
    },
    clearIntervalIfNeeded() {
      if (this.interval) {
        clearInterval(this.interval);
        this.interval = null;
      }
    },
  },
  mounted() {
    this.fetchTicketShow();
    this.fetchMessage();
    setTimeout(() => {
      this.scrollbottom();
    }, 500);
    this.interval = setInterval(this.fetchMessage, 60000);
  },
  beforeDestroy() {
    this.clearIntervalIfNeeded();
  },
  beforeRouteLeave(to, from, next) {
    this.clearIntervalIfNeeded();
    next();
  },
};
</script>

<style>
.element {
  height: calc(90vh - 140px);
}
textarea.auto-resize {
  height: auto;
  max-height: 8rem; /* Adjust this value to set the maximum height for 5 rows */
  overflow-y: auto;
  resize: none; /* Prevent manual resizing */
}
</style>
