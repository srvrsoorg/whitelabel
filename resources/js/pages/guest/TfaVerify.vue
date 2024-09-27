<template>
  <div class="min-h-screen flex justify-center items-center">
    <div
      class="w-full max-w-lg mx-auto bg-white sm:px-8 px-6 py-6 shadowcls rounded-lg"
    >
      <div class="xs:flex items-center gap-5 mb-4">
        <div>
          <span
            :class="[
              isLightColor
                ? 'text-custom-700 bg-custom-300'
                : 'text-custom-500 bg-custom-50',
              'material-symbols-outlined p-1.5 text-[24px] inline-flex items-center rounded-md',
            ]"
          >
            verified_user
          </span>
        </div>
        <h2 class="text-xl mt-2 xs:mt-0 font-medium">
          Two Factor Authentication
        </h2>
      </div>
      <hr />
      <p class="text-gray-500 text-sm mt-3 font-normal" v-if="user">
        You're Signing as
      </p>
      <p class="text-gray-500 text-tiny font-bold mt-0.5">
        {{ user ? user.email : "" }}
      </p>
      <form class="mt-5" action="javascript:void(0)" @submit="verifyCode()">
        <div>
          <div class="flex justify-between">
            <label
              for="code"
              class="block text-tiny text-neutral-800 font-medium"
            >
              Enter {{ useBackupcode ? "Backup" : "Verification" }} Code
            </label>
            <button
              v-if="!useBackupcode && user && !user.google2fa_enable"
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'text-tiny disabled:cursor-not-allowed disabled:opacity-50',
              ]"
              :disabled="resendProcessing || timer != 60"
              @click="resendCode()"
            >
              {{
                resendProcessing
                  ? "Please wait"
                  : `Resend Code ${timer != 60 ? `(${timer})` : ``}`
              }}
            </button>
          </div>
          <div class="form-group mt-3">
            <v-otp-input
              v-if="!useBackupcode"
              ref="otpInput"
              input-classes="otp-input"
              :conditionalClass="['one', 'two', 'three', 'four']"
              separator=" "
              inputType="letter-numeric"
              :num-inputs="6"
              v-model:value="code"
              @on-complete="verifyCode()"
              :should-auto-focus="true"
              :should-focus-order="true"
            />
            <input
              v-else
              type="text"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              v-model="code"
            />
            <small class="text-red-500 error-message" id="code_error"></small>
          </div>
        </div>
        <p
          class="text-xs text-gray-500 mt-2 inline-block"
          v-if="!useBackupcode && user && !user.google2fa_enable"
        >
          Please enter the 6 digit code sent to {{ user.email }}
        </p>
        <p
          class="text-xs text-gray-500 mt-2"
          v-else-if="!useBackupcode && user && user.google2fa_enable"
        >
          You will receive verification code from the Authenticator App.
        </p>
        <p class="text-xs text-gray-500 mt-2" v-else>
          Enter one of the backup codes
        </p>
        <Button class="w-full mt-3" :disabled="processing || code == ''">
          <i
            v-if="processing"
            class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
          ></i>
          {{ processing ? "Please wait" : "Confirm" }}
        </Button>
        <div class="mt-2 text-xs text-gray-500" v-if="!useBackupcode">
          <label class="inline-block"
            >If you're unable to receive a security code, use one of your</label
          >
          <div class="inline">
            <button
              type="button"
              @click="
                useBackupcode = true;
                code = '';
              "
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                ' inline pl-1',
              ]"
            >
              backup codes.
            </button>
          </div>
        </div>
        <p class="mt-2 text-xs text-gray-500" v-else>
          <label
            >Do you have a
            <button
              :class="[isLightColor ? 'text-custom-700' : 'text-custom-500']"
              @click="useBackupcode = false"
            >
              verification code</button
            >?</label
          >
        </p>
      </form>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from "@/store/auth";
import { mapState, mapActions } from "pinia";
import VOtpInput from "vue3-otp-input";

export default {
  data() {
    return {
      timer: 60,
      code: "",
      useBackupcode: false,
      resendProcessing: false,
      processing: false,
    };
  },
  components: {
    VOtpInput,
  },
  computed: {
    ...mapState(useAuthStore, ["user"]),
  },
  mounted() {
    this.startTimer();
  },
  methods: {
    ...mapActions(useAuthStore, ["setAccessToken", "setAuthenticated"]),
    complete(e) {
      if (e) {
        this.verifyCode();
      }
    },
    startTimer() {
      let app = this;
      var interval = setInterval(function () {
        app.timer = app.timer - 1;
        if (app.timer === 0) {
          app.timer = 60;
          clearInterval(interval);
        }
      }, 1000);
    },
    async verifyCode() {
      this.processing = true;
      await this.$axios
        .post(`/two-fa/verify`, {
          google_auth: this.useBackupcode ? false : this.user.google2fa_enable,
          code: this.code,
          email: this.user.email,
        })
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.setAccessToken(data.token);
          this.setAuthenticated(true);
          this.$router.push("/");
        })
        .catch(({ response: { data } }) => {
          this.processing = false;
          this.$toast.error(data.message);
        });
    },
    async resendCode() {
      this.resendProcessing = true;
      await this.$axios
        .post(`/two-fa/resend`, {
          email: this.user.email,
        })
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.startTimer();
        })
        .catch(({ response: { data } }) => {
          this.$toast.error(data.message);
        })
        .finally(() => {
          this.resendProcessing = false;
        });
    },
  },
};
</script>
<style>
.shadowcls {
  box-shadow: 6px 2px 15px 1px lightgray;
}
</style>