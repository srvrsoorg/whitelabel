<template>
  <div>
    <Breadcrumb :breadcrumb="breadcrumb" />
    <h1 class="text-xl text-[#31363f] font-medium">Security</h1>
    <div class="rounded-md bg-white pt-5 px-2 sm:px-0 my-4 border">
      <div class="grid grid-cols-12 gap-5">
        <div
          class="xl:col-span-1 hidden sm:col-span-2 col-span-12 justify-center sm:inline-flex items-start sm:pl-[25%] pt-1"
        >
          <img src="/icon/Vector.png" alt="" class="w-[70px]" />
        </div>
        <div class="xl:col-span-11 sm:col-span-10 col-span-12">
          <div class="">
            <div class="flex justify-between items-center gap-2 sm:px-0 px-2">
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
                  'relative inline-flex items-center mr-5 h-5 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0',
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
            <p class="text-gray-500 text-tiny my-2 px-3 sm:px-0">
              When you enable two-factor authentication using the switch
              provided above, it will automatically set up 2FA via email. For
              2FA using an application, go to the 'Authenticator App' tab and
              enable it there.
            </p>
            <div class="my-3 flex justify-between items-center gap-5 w-full">
              <h1 class="text-tiny text-gray-500 font-medium px-3 sm:px-0">
                Two-Factor Authentication is
                <span
                  :class="[
                    'font-medium text-[#22C55E]',
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
      <div class="px-5 pb-5" v-if="tfa">
        <hr class="mb-5 border-dashed border-[#CBD5E0]" />
        <div class="block">
          <nav class="flex flex-wrap sm:space-x-4" aria-label="Tabs">
            <div
              v-for="tab in tabs"
              :key="tab.name"
              :class="[
                currentTab == tab.value
                  ? isLightColor
                    ? 'bg-custom-200 text-custom-700'
                    : 'bg-custom-50 text-custom-500'
                  : 'hover:bg-gray-50 text-gray-800',
                'rounded-md px-3 py-2 text-sm font-medium cursor-pointer',
              ]"
              @click="currentTab = tab.value"
            >
              {{ tab.name }}
            </div>
          </nav>
        </div>
        <template v-if="currentTab == 'recovery_code'">
          <div
            class="rounded-lg px-5 py-3 mt-5 text-[#EB9A49] bg-[#FFFAE5] text-tiny"
          >
            <p>
              <span class="font-medium">Note:</span>
              If you did not receive 2FA authentication on your email, you can
              use one of these backup codes. You can only use each backup code
              once. We recommend keeping a safe record of these backup codes.
            </p>
          </div>
          <div class="grid xl:grid-cols-6 grid-cols-1 gap-5 mt-5">
            <div class="xl:col-span-4 border rounded-lg p-5">
              <template v-if="backupCodes.length > 0">
                <div class="grid sm:grid-cols-3 grid-cols-1 gap-7 w-full">
                  <div
                    class="mb-0 place-self-center"
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
                      <span v-else class="mt-1.5">********</span>
                    </div>
                  </div>
                </div>
              </template>
              <div
                class="grid sm:grid-cols-3 gap-5 my-5 px-5 py-2 bg-[#F6F6F6] rounded-md"
              >
                <button
                  class="w-full flex sm:items-center justify-center gap-1 text-gray-500 text-tiny"
                  @click="saveCodeFile"
                >
                  <span class="material-symbols-outlined text-xl"> note </span>
                  <p>Download Backup Code</p>
                </button>
                <button
                  class="w-full flex sm:items-center justify-center gap-1 text-tiny text-gray-500"
                  @click="copyCodes"
                >
                  <span class="material-symbols-outlined text-xl">
                    content_copy
                  </span>
                  <p>Copy Backup Code</p>
                </button>
                <button
                  class="w-full flex sm:items-center justify-center gap-1 text-tiny text-gray-500 disabled:opacity-50 disabled:pointer-events-none"
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
                      : 'text-custom-500 bg-custom-50'
                  "
                  class="w-full text-tiny py-2 gap-1.5 shadow rounded-md flex items-center justify-center"
                >
                  <span class="material-symbols-outlined text-lg"
                    >visibility
                  </span>
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
              class="xl:col-span-2 grid xl:grid-cols-1 md:grid-cols-2 grid-cols-1 xl:ml-2.5 gap-y-3 gap-x-5"
            >
              <div
                class="border border-[#CBD5E0] bg-[#F8F6F6] p-2.5 rounded-lg"
              >
                <p class="text-tiny font-semibold">Generated On</p>
                <p class="text-gray-500 text-tiny">{{ generated_at.date }}</p>
              </div>
              <div
                class="border border-[#CBD5E0] bg-[#F6F6F6] p-2.5 rounded-lg"
              >
                <p class="text-tiny font-semibold">Time</p>
                <p class="text-gray-500 text-tiny">{{ generated_at.time }}</p>
              </div>
              <div
                class="border border-[#CBD5E0] bg-[#F6F6F6] p-2.5 rounded-lg"
              >
                <p class="text-tiny font-semibold">Codes Used</p>
                <p class="text-gray-500 text-tiny">{{ backup_code.used }}</p>
              </div>
              <div
                class="border border-[#CBD5E0] bg-[#F8F9FA] p-2.5 rounded-lg"
              >
                <p class="text-tiny font-semibold">Codes Available</p>
                <p class="text-gray-500 text-tiny">
                  {{ backup_code.available }}
                </p>
              </div>
            </div>
          </div>
        </template>
        <div v-if="currentTab == 'authenticator_app'">
          <div
            class="rounded-lg px-5 py-3 mt-5 text-[#EB9A49] inline-block bg-[#FFFAE5] text-tiny"
          >
            <p class="">
              <b class="font-medium">Note:</b> If you set up Two-Factor
              Authentication through the app, you won't get OTPs sent to your
              email anymore.
            </p>
          </div>
          <br />
          <template v-if="!authenticatorApp.enable">
            <p class="text-gray-500 text-tiny mt-3 font-medium">
              Two-Factor Authentication adds an additional layer of security to
              the authentication process, making it harder for attackers to gain
              access to a person's devices or online accounts. Even if the
              victim's password is hacked, a password alone is not enough to
              pass the authentication check.
            </p>
            <div class="grid md:grid-cols-2 grid-cols-1 gap-5">
              <div>
                <h5 class="font-bold mt-5">Step: 1</h5>
                <p class="mt-1 text-tiny text-gray-500 font-medium">
                  Scan this barcode with your
                  <span class="text-custom-500 font-medium"
                    >authentication</span
                  >
                  app:
                </p>
                <div class="mt-3" v-html="authenticatorApp.qrCode"></div>
              </div>
              <div>
                <h5 class="font-bold mt-5">Step: 2</h5>
                <p class="text-tiny mt-1 text-gray-500">
                  Enter the 6-digit authentication code to Enable 2FA
                </p>
                <div class="mt-4">
                  <div class="form-group">
                    <v-otp-input
                      ref="otpInput"
                      input-classes="otp-input focus:!ring-0 focus:border-custom-500 bg-[#F8F9FA]"
                      :conditionalClass="['one', 'two', 'three', 'four']"
                      separator=" "
                      inputType="letter-numeric"
                      :num-inputs="6"
                      v-model:value="authenticatorApp.verify_code"
                      :should-auto-focus="true"
                      :should-focus-order="true"
                    />
                    <small
                      class="text-danger error-message"
                      id="verify_code_error"
                    ></small>
                  </div>
                </div>
                <div class="mt-5">
                  <Button
                    @click="toggleGoogle2fa"
                    type="submit"
                    :disabled="authenticatorApp.processing"
                  >
                    <i
                      v-if="authenticatorApp.processing"
                      class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
                    ></i>
                    {{ authenticatorApp.processing ? "Please wait" : "Enable" }}
                  </Button>
                </div>
              </div>
            </div>
          </template>

          <template v-if="authenticatorApp.enable">
            <div
              class="p-4 mt-5 text-sm inline-block text-green-700 bg-green-100 rounded-lg"
            >
              <p class="mb-0">
                <i class="fa-solid fa-circle-check mr-1.5"></i>
                Two-Factor Authentication is currently activated on your
                account.
              </p>
            </div>
            <div class="mt-4">
              <button
                type="submit"
                @click="toggleGoogle2fa"
                class="disabled:opacity-50 disabled:cursor-not-allowed rounded-md p-2 bg-red-500 text-sm text-white"
                :disabled="authenticatorApp.processing"
              >
                <i
                  v-if="authenticatorApp.processing"
                  class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
                ></i>
                {{ authenticatorApp.processing ? "Please wait" : "Disable" }}
              </button>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>

  <div class="my-5 mt-8 xl:pt-4 ml-3 rounded-md bg-white border relative">
    <div
      class="-left-5 p-2 pr-3.5 bg-white -top-5 absolute flex justify-center items-center gap-2"
    >
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'material-symbols-outlined',
        ]"
      >
        account_circle
      </span>
      <p class="font-medium text-lg">Change Password</p>
    </div>

    <div
      class="grid my-5 mt-10 2xl:grid-cols-3 md:grid-cols-2 xl:my-6 grid-cols-1 xl:gap-x-10 md:gap-x-6 gap-y-3 xl:gap-y-5 px-5"
    >
      <div class=" xl:flex gap-x-5 gap-1 items-start">
        <label
          class="block text-tiny whitespace-nowrap xl:pt-2 after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
          >Current Password</label
        >
        <div class="w-full">
          <div class="relative">
            <input
              id="current_password"
              name="current_password"
              :type="oldPassword ? 'text' : 'password'"
              placeholder="Current Password"
              v-model="ChangePassword.current_password"
              :class="{ 'tracking-widest': !oldPassword }"
              class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
            />
            <PasswordVisibility
              :showPassword="oldPassword"
              @toggle="oldPassword = !oldPassword"
            />
          </div>
          <small
            id="current_password_message"
            class="error_message text-red-500"
          ></small>
        </div>
      </div>
      <div class="xl:flex gap-x-5 gap-1 items-start">
        <div class="">
          <label
            class="block xl:pt-2 text-tiny whitespace-nowrap after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
            >New Password</label
          >
        </div>
        <div class="w-full">
          <div class="relative">
            <input
              id="new_password"
              name="new_password"
              :type="showNewPassword ? 'text' : 'password'"
              placeholder="Enter New Password"
              v-model="ChangePassword.new_password"
              :class="{ 'tracking-widest': !showNewPassword }"
              class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
            />
            <PasswordVisibility
              :showPassword="showNewPassword"
              @toggle="showNewPassword = !showNewPassword"
            />
          </div>
          <small
            id="new_password_message"
            class="error_message text-red-500"
          ></small>
        </div>
      </div>

      <div class=" xl:flex gap-x-5 gap-1">
        <div class="">
          <label
            class="block xl:pt-2 text-tiny whitespace-nowrap after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
            >Confirm Password</label
          >
        </div>
        <div class="w-full">
          <div class="relative">
            <input
              id="new_password_confirmation"
              name="new_password_confirmation"
              :type="showNewConfirmPassword ? 'text' : 'password'"
              placeholder="Enter New Confirm Password"
              v-model="ChangePassword.new_password_confirmation"
              :class="{ 'tracking-widest': !showNewConfirmPassword }"
              class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
            />
            <PasswordVisibility
              :showPassword="showNewConfirmPassword"
              @toggle="showNewConfirmPassword = !showNewConfirmPassword"
            />
          </div>
          <small
            id="new_password_confirmation_message"
            class="error_message text-red-500"
          ></small>
        </div>
      </div>
    </div>

    <div class="sm:flex justify-end gap-4 px-5 pb-5">
      <div class="text-end">
        <Button :disabled="process" @click="updatePassword('changePassword')">
          <i v-if="process" class="fa-solid fa-circle-notch fa-spin px-5"></i>
          {{ process ? "Please wait" : "Change Password" }}
        </Button>
      </div>
      <div class="text-end mt-5 sm:mt-0">
        <Button :disabled="changeProcess" @click="updatePassword('logout')">
          <i v-if="changeProcess" class="fa-solid fa-circle-notch fa-spin"></i>
          {{ changeProcess ? "Please wait" : "Change Password & Logout" }}
        </Button>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from "@/store/auth";
import { mapState, mapActions } from "pinia";
import { Switch } from "@headlessui/vue";
import VOtpInput from "vue3-otp-input";

export default {
  name: "TwoFactorAuthentication",
  data() {
    return {
      breadcrumb: {
        // title: "Account",
        icon: "account_box",
        pages: [{ name: "Account" },{ name: "Security" }],
      },
      tabs: [
        { name: "Recovery Codes", value: "recovery_code" },
        { name: "Authenticator App", value: "authenticator_app" },
      ],
      currentTab: "",
      processing: false,
      process: false,
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
      deleting: false,
      password: "",
      ChangePassword: {
        current_password: "",
        new_password: "",
        new_password_confirmation: "",
      },
      oldPassword: false,
      showNewConfirmPassword: false,
      showNewPassword: false,
      changeProcess: false,
      process: false,
      authenticatorApp: {
        verify_code: "",
        processing: false,
        enable: false,
        qrCode: "",
      },
    };
  },
  computed: {
    ...mapState(useAuthStore, ["user"]),
  },
  components: {
    Switch,
    VOtpInput,
  },
  created() {
    // this.getUser();
    this.tfa = this.user && this.user.two_fa_enable;
    this.authenticatorApp.enable = this.user && this.user.google2fa_enable;
    this.currentTab =
      this.user && this.user.google2fa_enable
        ? "authenticator_app"
        : "recovery_code";
    if (this.tfa) {
      this.getBackupCode();
      this.getGoogle2FaQrCode();
    }
  },
  methods: {
    ...mapActions(useAuthStore, ["getUser", "authLogout"]),
    async toggleTwoFa() {
      await this.$axios
        .patch(`/toggle-two-fa`)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.getUser();

          if (data.two_fa) {
            this.getBackupCode();
            this.getGoogle2FaQrCode();
          }
        })
        .catch(({ response: data }) => {
          this.$toast.error(data.message);
        });
    },
    async getBackupCode() {
      await this.$axios
        .get(`/backup-codes`)
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
    async getGoogle2FaQrCode() {
      await this.$axios
        .get(`/qr-code`)
        .then(({ data }) => {
          this.authenticatorApp.qrCode = data.google2fa_url;
        })
        .catch(({ response: data }) => {
          this.$toast.error(data.message);
        });
    },
    async toggleGoogle2fa() {
      this.authenticatorApp.processing = true;
      await this.$axios
        .post(`toggle-google-twofa`, {
          verify_code: this.authenticatorApp.verify_code,
        })
        .then(({ data }) => {
          this.authenticatorApp.verify_code = "";
          this.authenticatorApp.enable = !this.authenticatorApp.enable;
          this.$toast.success(data.message);
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
          this.authenticatorApp.processing = false;
        });
    },
    async regenerateBackupCode() {
      this.regenerateCode = true;
      await this.$axios
        .get(`/regenerate-backup-codes`)
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
        .get(`/send-backup-codes`)
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
    updatePassword(apiType) {
      this.hideError();
      if (apiType === "changePassword") {
        this.process = true;
        this.$axios
          .patch(`/user/change-password`, this.ChangePassword)
          .then((response) => {
            this.$toast.success(response.data.message);
            this.ChangePassword.current_password = "";
            this.ChangePassword.new_password = "";
            this.ChangePassword.new_password_confirmation = "";
          })
          .catch((error) => {
            if (error.response) {
              if (error.response.status !== 422) {
                this.$toast.error(error.response.data.message);
              } else {
                this.displayError(error.response.data);
              }
            }
          })
          .finally(() => {
            this.process = false;
          });
      } else if (apiType === "logout") {
        this.changeProcess = true;
        this.$axios
          .patch("/user/change-password", this.ChangePassword)
          .then(({ data }) => {
            this.$toast.success(data.message);
            this.authLogout();
            this.$router.push({ name: "login" });
          })
          .catch((error) => {
            if (error.response) {
              if (error.response.status !== 422) {
                this.$toast.error(response.data.message);
              } else {
                this.displayError(error.response.data);
              }
            }
          })
          .finally(() => {
            this.changeProcess = false;
          });
      }
    },
  },
};
</script>
<style>
.otp-input.is-complete {
  background-color: #f8f9fa !important;
  border-color: #e4e4e4 !important;
}
</style>