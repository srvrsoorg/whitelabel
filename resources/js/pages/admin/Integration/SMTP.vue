<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <p class="text-xl font-medium text-[#31363f]">SMTP</p>

  <div class="my-5 mt-8 sm:py-4 ml-3 rounded-md bg-white border relative">
    <div
      class="-left-5 p-2 bg-white -top-5 absolute flex justify-center items-center gap-2"
    >
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'material-symbols-outlined',
        ]"
      >
        mail
      </span>
      <p class="font-medium">SMTP</p>
    </div>

    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:my-6 my-3 mt-5 xl:px-14 px-5 xl:gap-x-30 2xl:gap-x-40 sm:gap-x-14 gap-x-5 gap-2 "
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12">
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Username</label
            >
          </div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <input
              v-model="smtp.username"
              type="text"
              name="Username"
              id="username"
              placeholder="cyseFKDCsueSKJFHRfjrfk"
              class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 "
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <small
              id="username_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12">
            <label
              class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Password</label
            >
          </div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <input
              v-model="smtp.password"
              type="password"
              name="password"
              id="password"
              placeholder="********"
              class="block w-full rounded-md border border-neutral-300 disabled:opacity-50 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 "
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12 hidden xl:block"
          ></div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <small
              id="password_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:my-6 xl:px-14 px-5 xl:gap-x-30 2xl:gap-x-40 sm:gap-x-14 gap-x-5 gap-3 "
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12">
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Host</label
            >
          </div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <input
              v-model="smtp.host"
              type="text"
              name="host"
              id="host"
              placeholder="sandbox.smtp.mailtrap.io"
              class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1 "
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <small
              id="host_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12">
            <label
              class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Port</label
            >
          </div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <input
              v-model="smtp.port"
              onwheel="this.blur()"
              type="number"
              name="Port"
              id="port"
              placeholder="2525"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 "
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <small
              id="port_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:px-14 xl:my-6 my-3 px-5 xl:gap-x-30 2xl:gap-x-40 sm:gap-x-14 gap-x-5 gap-3 "
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12">
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >From Name</label
            >
          </div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <input
              v-model="smtp.from_name"
              type="text"
              name="fromname"
              id="from_name"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="ServerAvatar Cloud Technologies PVT LTD"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 pb-2 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <small
              id="from_name_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12">
            <label
              class="font-medium whitespace-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >From Email</label
            >
          </div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <input
              v-model="smtp.from_email"
              type="text"
              name="email"
              id="from_email"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="noreply@serveravatar.com"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <small
              id="from_email_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>

    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:my-6 my-3 xl:px-14 px-5 xl:gap-x-30 2xl:gap-x-40 sm:gap-x-14 gap-x-5 gap-3 mb-6 "
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12">
            <label
              class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Encryption
            </label>
          </div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <input
              v-model="smtp.encryption"
              type="text"
              name="encryption"
              id="encryption"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="tls"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <small
              id="encryption_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>

    <div
      class="flex justify-between items-center gap-4 my-5 sm:my-10 float-end"
    >
      <Button class="block" @click="openUpdateSMTP"> Save Settings </Button>
      <Button :disabled="process" class="block" @click="sendMail">
        <i
          v-if="process"
          class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
        ></i>
        {{ process ? "Please wait" : "Send Test Mail" }}
      </Button>
    </div>
  </div>

  <Confirmation
    :show="openConfirmation"
    :showLoader="showLoader"
    :confirmationTitle="'Update SMTP'"
    :btnBgColor="'bg-[#FFB74D]'"
    :submitBtnTitle="`Yes, I'm Sure`"
    @confirm="saveData"
    @closeModal="closeModal"
  >
    <template #icon>
      <span class="material-symbols-outlined text-[#FFB74D] text-[22px]">
        mail
      </span>
    </template>
    <template #content>
      <span class="text-gray-600 text-tiny"
        >Are you sure you want to update the SMTP settings?
      </span>
      <div class="my-5 text-tiny font-medium flex gap-3">
        <div>
          <span
            class="material-symbols-outlined bg-[#FFB74D] text-white rounded-full font-semibold text-[20px] p-0.5 flex items-center"
          >
            check
          </span>
        </div>
        <span>
          The Changes will be effected immediatley. Please review the details
          carefully.
        </span>
      </div>
      <div class="my-5 text-tiny font-medium flex gap-3">
        <div>
          <span
            class="material-symbols-outlined bg-[#FFB74D] text-white rounded-full font-semibold text-[20px] p-0.5 flex items-center"
          >
            check
          </span>
        </div>
        <span>
          Before updating the details, We recommend you to send a test email to
          verify that the settings are correctly configured.
        </span>
      </div>
    </template>
  </Confirmation>
</template>

<script>
export default {
  name: "SMTP",
  data() {
    return {
      breadcrumb: {
        title: "Integrations",
        icon: "rule_settings",
        pages: [{ name: "SMTP" }],
      },
      processing: false,
      openConfirmation: false,
      showLoader: false,
      process: false,
      smtp: {
        username: "",
        password: "",
        from_name: "",
        from_email: "",
        host: "",
        port: "",
        encryption: "",
      },
    };
  },
  methods: {
    openUpdateSMTP() {
      this.openConfirmation = true;
    },
    closeModal() {
      this.openConfirmation = false;
      this.showLoader = false;
    },

    async fetchSMTP() {
      await this.$axios
        .get("/admin/smtp")
        .then(({ data }) => {
          if (data.smtp !== null) {
            this.smtp = data.smtp;
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    async saveData() {
      this.showLoader = true;
      this.hideError();
      await this.$axios
        .post("/admin/smtp", this.smtp)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.fetchSMTP();
          this.closeModal();
        })
        .catch(({ response }) => {
          if (response.status === 422) {
            this.displayError(response.data);
          } else {
            this.$toast.error(data.message);
          }
          this.closeModal();
        });
    },
    async sendMail() {
      this.process = true;
      await this.$axios
        .get("/admin/smtp/testMail")
        .then(({ data }) => {
          this.$toast.success(data.message);
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.process = false;
        });
    },
  },
  mounted() {
    this.fetchSMTP();
  },
};
</script>
