<template>
  <div class="rounded-md bg-white p-5 my-4 border">
    <div class="grid grid-cols-12 gap-2">
      <div
        class="xl:col-span-1 sm:col-span-2 hidden sm:flex justify-center items-start"
      >
        <img src="/icon/Vector.png" alt="" class="w-16" />
      </div>
      <div class="xl:col-span-11 sm:col-span-10 col-span-12">
        <div class="">
          <div class="flex justify-between items-center gap-2">
            <p class="text-lg font-medium">Two Factor Authentication</p>

            <Switch
              v-model="tfa"
              @click="toggleTwoFa"
              :class="[
                tfa
                  ? isLightColor
                    ? 'bg-custom-700'
                    : 'bg-custom-500'
                  : 'bg-gray-300',
                'relative inline-flex items-center h-5 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0',
              ]"
            >
              <span
                aria-hidden="true"
                :class="[
                  tfa ? 'translate-x-6' : 'translate-x-0',
                  'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                ]"
              />
            </Switch>
          </div>
        </div>
        <div class="sm:w-[85%]">
          <div class="my-2.5 flex justify-between items-center gap-5 w-full">
            <h1 class="text-tiny text-gray-500 font-medium">
              Two-Factor Authentication is
              <span
                :class="[
                  isLightColor ? 'text-custom-700' : 'text-custom-500',
                  'font-medium',
                ]"
                v-if="tfa"
                >Enabled.</span
              >
              <span class="text-red-500 font-medium" v-else>Disabled.</span>
            </h1>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-5" v-if="tfa">
      <div class="grid xl:grid-cols-6 grid-cols-1 gap-7">
        <div class="xl:col-span-4 border rounded-lg sm:p-6 p-5">
          <div
            class="grid sm:grid-cols-3 grid-cols-1 xs:grid-cols-2 gap-x-16 gap-y-5 w-full px-5 xl:px-0"
          >
            <template v-if="backupCodes.length > 0">
              <div
                class="flex justify-center items-center"
                v-for="(code, key) in backupCodes"
                :key="key"
              >
                <div
                  :class="{
                    'bg-red-50 text-red-500': code.used,
                    'bg-green-100 text-green-500': !code.used,
                  }"
                  class="w-fit sm:px-7 px-4 py-1 rounded flex items-center justify-center h-full"
                >
                  <p v-if="showCodes" class="text-center text-tiny">
                    {{ code.backup_code }}
                  </p>
                  <span v-else>*********</span>
                </div>
              </div>
            </template>
          </div>
          <div
            class="grid 2xl:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5 lg:px-0 sm:px-4 py-5"
          >
            <button
              class="w-full flex bg-gray-100 px-3 rounded-md py-1.5 items-center justify-center gap-1 text-tiny"
              @click="saveCodeFile"
            >
              <span class="material-symbols-outlined text-xl"> note </span>
              <p>Download Backup Code</p>
            </button>
            <button
              class="w-full flex bg-gray-100 px-3 rounded-md py-1.5 items-center justify-center gap-1 text-tiny"
              @click="copyCodes"
            >
              <span class="material-symbols-outlined text-xl">
                content_copy
              </span>
              <p>Copy Backup Code</p>
            </button>
            <button
              class="w-full flex bg-gray-100 px-3 rounded-md py-1.5 items-center justify-center gap-1 text-tiny disabled:opacity-50 disabled:pointer-events-none"
              :disabled="isSendingBackupCodeMail"
              @click="sendBackupCodesMail"
            >
              <span class="material-symbols-outlined text-xl"> mail </span>
              <p>Send Backup Code</p>
            </button>
          </div>
          <div class="grid md:grid-cols-2 gap-5 w-full">
            <button
              @click="showCodes = !showCodes"
              :class="
                isLightColor
                  ? 'text-custom-700 bg-custom-200'
                  : 'bg-custom-50 text-custom-500'
              "
              class="w-full text-tiny py-2 gap-1.5 shadow rounded-md flex items-center justify-center"
            >
              <span class="material-symbols-outlined text-lg">visibility </span>
              Show Codes
            </button>

            <button
              @click="regenerateBackupCode"
              :disabled="regenerateCode"
              class="w-full text-tiny bg-blue-50 py-2 shadow text-blue-600 gap-1.5 rounded-md flex items-center justify-center disabled:opacity-50 disabled:pointer-events-none"
            >
              <span class="material-symbols-outlined text-xl">
                restart_alt
              </span>
              Regenerate Codes
            </button>
          </div>
        </div>
        <div
          class="xl:col-span-2 grid xl:grid-cols-1 md:grid-cols-2 grid-cols-1 gap-y-3 gap-x-5"
        >
          <div class="border border-[#CBD5E0] bg-[#F8F6F6] p-2.5 rounded-lg">
            <p class="text-tiny font-semibold">Generated On</p>
            <p class="text-gray-500 text-tiny">{{ generated_at.date }}</p>
          </div>
          <div class="border border-[#CBD5E0] bg-[#F6F6F6] p-2.5 rounded-lg">
            <p class="text-tiny font-semibold">Time</p>
            <p class="text-gray-500 text-tiny">{{ generated_at.time }}</p>
          </div>
          <div class="border border-[#CBD5E0] bg-[#F6F6F6] p-2.5 rounded-lg">
            <p class="text-tiny font-semibold">Codes Used</p>
            <p class="text-gray-500 text-tiny">{{ backup_code.used }}</p>
          </div>
          <div class="border border-[#CBD5E0] bg-[#F8F9FA] p-2.5 rounded-lg">
            <p class="text-tiny font-semibold">Codes Available</p>
            <p class="text-gray-500 text-tiny">
              {{ backup_code.available }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Switch } from "@headlessui/vue";

export default {
  props: ["userData"],
  data() {
    return {
      breadcrumb: {
        title: "User",
        icon: "groups",
        pages: [{ name: "Security" }],
      },
      tfa: false,
      backupCodes: [],
      generated_at: {},
      regenerateCode: false,
      isSendingBackupCodeMail: false,
      showCodes: false,
      backup_code: {
        used: 0,
        available: 0,
      },
    };
  },
  components: {
    Switch,
  },
  watch: {
    userData: {
      handler(val) {
        this.tfa = val.two_fa_enable;
        if (this.tfa) {
          this.getBackupCode();
        }
      },
    },
  },
  created() {
    if (this.userData) {
      this.tfa = this.userData.two_fa_enable;
      if (this.tfa) {
        this.getBackupCode();
      }
    }
    this.$emit("pass-breadcrumb", this.breadcrumb);
  },
  methods: {
    async toggleTwoFa() {
      await this.$axios
        .patch(`/admin/users/${this.$route.params.user}/toggle-two-fa`)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.$emit("refresh-user");

          if (data.two_fa) {
            this.getBackupCode();
          }
        })
        .catch(({ response: data }) => {
          this.$toast.error(data.message);
        });
    },
    async getBackupCode() {
      await this.$axios
        .get(`/admin/users/${this.$route.params.user}/backup-codes`)
        .then(({ data }) => {
          this.setBackupData(data);
        })
        .catch(({ response: data }) => {
          this.$toast.error(data.message);
        });
    },
    setBackupData(data) {
      this.backupCodes = data.backup_codes;
      this.generated_at.date = data.generated_at.date;
      this.generated_at.time = data.generated_at.time;
      this.backup_code.used = data.used;
      this.backup_code.available = data.available;
    },
    async regenerateBackupCode() {
      this.regenerateCode = true;
      await this.$axios
        .get(`/admin/users/${this.$route.params.user}/regenerate-backup-codes`)
        .then(({ data }) => {
          this.setBackupData(data.data);
          this.$toast.success(data.message);
        })
        .catch(({ response: data }) => {
          this.$toast.error(data.message);
        })
        .finally(() => {
          this.regenerateCode = false;
        });
    },
    copyCodes() {
      var $temp = $("<textarea>");
      $("body").append($temp);

      var suffix = "\n";
      var code = "";
      var codeLength = this.backupCodes.length;
      $.each(this.backupCodes, function (index, codeRow) {
        if (index + 1 == codeLength) {
          suffix = "";
        }
        code = code + `${codeRow.backup_code}${suffix}`;
      });

      $temp.val(code).select();
      document.execCommand("copy");
      $temp.remove();
      this.$toast.success("Copied to clipboard!");
    },
    saveCodeFile() {
      var code = "";
      $.each(this.backupCodes, function (row, codeRow) {
        code = code + `${codeRow.backup_code} \n`;
      });
      var blob = new Blob([code], { type: "text/plain;charset=utf-8" });
      var atag = document.createElement("a");
      atag.href = URL.createObjectURL(blob);
      atag.download = "backup-codes.txt";
      atag.click();
    },
    async sendBackupCodesMail() {
      this.isSendingBackupCodeMail = true;
      await this.$axios
        .get(`/admin/users/${this.$route.params.user}/send-backup-codes`)
        .then(({ data }) => {
          this.$toast.success(data.message);
        })
        .catch(({ response: { data } }) => {
          this.$toast.error(data.message);
        })
        .finally(() => {
          this.isSendingBackupCodeMail = false;
        });
    },
  },
};
</script>