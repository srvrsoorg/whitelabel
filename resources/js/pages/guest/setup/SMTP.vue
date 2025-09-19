<template>
  <InstallationInfo />
  <div
    class="w-full max-w-md mx-auto 2xl:mt-36 xl:mt-16 sm:mt-12 sm:mb-0 my-5 p-4"
  >
    <div class="">
      <form
        class="mt-5"
        action="javascript:void(0)"
        @submit="saveSmtpDetails()"
      >
        <div class="my-4">
          <label for="host" class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >Host</label
          >
          <div class="mt-1.5">
            <input
              type="text"
              name="host"
              v-model="smtp.host"
              id="host"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="Enter Host"
            />
            <small class="text-red-500 error_message" id="host_message"></small>
          </div>
        </div>
        <div class="grid md:grid-cols-2 grid-cols-1 gap-x-4">
          <div>
            <label
              for="username"
              class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
              >Username</label
            >
            <div class="mt-1.5">
              <input
                type="text"
                name="username"
                v-model="smtp.username"
                id="username"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                placeholder="Enter Username"
              />
              <small
                class="text-red-500 error_message"
                id="username_message"
              ></small>
            </div>
          </div>
          <div class="md:mt-0 mt-4">
            <label
              for="password"
              class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
              >Password</label
            >
            <div class="mt-1.5">
              <input
                type="password"
                name="password"
                v-model="smtp.password"
                id="password"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal tracking-widest text-sm leading-6 focus:ring-0"
                placeholder="Enter Password"
              />
              <small
                class="text-red-500 error_message"
                id="password_message"
              ></small>
            </div>
          </div>
        </div>
        <div class="grid md:grid-cols-2 grid-cols-1 gap-x-4 mt-4">
          <div class="">
            <label
              for="from_name"
              class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
              >From Name</label
            >
            <div class="mt-1.5">
              <input
                type="text"
                name="from_name"
                v-model="smtp.from_name"
                id="from_name"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                placeholder="Enter From Name"
              />
              <small
                class="text-red-500 error_message"
                id="from_name_message"
              ></small>
            </div>
          </div>
          <div class="md:mt-0 mt-4">
            <label
              for="from_email"
              class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
              >From Email</label
            >
            <div class="mt-1.5">
              <input
                type="text"
                name="from_email"
                v-model="smtp.from_email"
                id="from_email"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                placeholder="Enter From Email"
              />
              <small
                class="text-red-500 error_message"
                id="from_email_message"
              ></small>
            </div>
          </div>
        </div>
        <div class="grid md:grid-cols-2 grid-cols-1 gap-x-4 mt-4">
          <div>
            <label
              for="port"
              class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
              >Port</label
            >
            <div class="mt-1.5">
              <input
                type="text"
                name="port"
                v-model="smtp.port"
                id="port"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                placeholder="Enter Port"
              />
              <small
                class="text-red-500 error_message"
                id="port_message"
              ></small>
            </div>
          </div>
          <div class="md:mt-0 mt-4">
            <label
              for="encryption"
              class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
              >Encryption</label
            >
            <div class="mt-1.5">
              <input
                type="text"
                name="encryption"
                v-model="smtp.encryption"
                id="encryption"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                placeholder="Enter Encryption"
              />
              <small
                class="text-red-500 error_message"
                id="encryption_message"
              ></small>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap justify-between gap-3 mt-6">
          <router-link
            :class="[
              isLightColor
                ? 'text-custom-700 border-custom-700'
                : 'text-custom-500 border-custom-500',
              'border rounded-md px-5 py-1.5 text-sm font-medium flex items-center',
            ]"
            :to="{
              name: 'keyVerification',
            }"
          >
            Back
          </router-link>
          <Button :disabled="processing" :class="['px-5']">
            <i
              v-if="processing"
              class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
            ></i>
            {{ processing ? "Please Wait" : "Next" }}
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useSetupStore } from "@/store/setup.js";
import { defineAsyncComponent } from "vue";

export default {
  data() {
    return {
      smtp: {
        host: "",
        port: "",
        username: "",
        password: "",
        from_name: "",
        from_email: "",
        encryption: "",
      },
      processing: false,
    };
  },
  computed: {
    ...mapState(useSetupStore, ["keyVerificationComplete"]),
  },
  components: {
    InstallationInfo: defineAsyncComponent(() =>
      import("@/components/InstallationInfo.vue")
    ),
  },
  created() {
    if (!this.keyVerificationComplete) {
      this.$router.push({
        name: "keyVerification",
      });
    } else {
      this.getSmtpDetails();
    }
  },
  methods: {
    async getSmtpDetails() {
      this.$axios
        .get(`/setup/smtp`)
        .then(({ data }) => {
          if (data.smtp) {
            this.smtp = data.smtp;
          }
        })
        .catch(({ response: { data } }) => {});
    },
    async saveSmtpDetails() {
      this.processing = true;
      this.hideError();

      this.$axios
        .post(`/setup/smtp`, this.smtp)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.$router.push({
            name: "setupRegister",
          });
        })
        .catch(({ response }) => {
          if (response !== undefined) {
            const { status, data } = response;
            if (status === 422) {
              this.displayError(data);
            } else {
              this.$toast.error(data.message);
            }
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },
};
</script>

