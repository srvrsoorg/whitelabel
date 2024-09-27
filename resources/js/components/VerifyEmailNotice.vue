<template>
  <div
    class="verify-notice fixed top-0 inset-x-0 bg-orange-600 sm:h-10 h-16 z-[999] flex items-center justify-center"
  >
    <div class="w-full text-center">
      <p class="font-medium md:text-tiny sm:text-sm text-xs text-white">
        <span class="inline"
          >Your email address has not been verified yet. Please verify your
          email address.</span
        >
        <span class="block sm:ml-2 sm:inline-block">
          <button
            type="button"
            :disabled="processing"
            @click="sendLink"
            class="font-bold tracking-wide text-white underline"
          >
            <i
              v-if="processing"
              class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
            ></i>
            {{ processing ? "Please wait" : "Resend Verification Link" }}
          </button>
        </span>
      </p>
    </div>
  </div>
</template>
<script>
import { useAuthStore } from "@/store/auth.js";
import { mapState } from "pinia";
export default {
  name: "VerifyEmailNotice",
  setup(){
    const authStore = useAuthStore()
    return {authStore};
  },
  data() {
    return {
      processing: false,
    };
  },
  computed: {
    user(){
      return this.authStore.user
    },
  },
  methods: {
    async sendLink() {
      this.processing = true;
      await this.$axios
        .get(`/user/resend-verification-link`)
        .then(({ data }) => {
          this.$toast.success(data.message);
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },
};
</script>