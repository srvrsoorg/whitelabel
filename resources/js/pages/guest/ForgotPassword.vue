<template>
  <div class="w-full flex justify-center items-center min-h-screen p-4 mx-auto">
    <div class="relative bg-white shadowcls my-10 p-4 rounded-lg">
      <div
        class="rounded-full p-3 mx-auto absolute -top-10 left-1/2 transform -translate-x-1/2 bg-white border border-primary"
      >
        <div class="h-12 w-12 mx-auto flex justify-center items-center">
          <img
            v-if="!icon"
            class="h-9 w-auto flex justify-center items-centers"
            src="/logo/logo-sm.png"
            :alt="app_name"
          />
          <img
            v-else
            class="h-9 w-auto flex items-center justify-center"
            :src="icon"
            :alt="app_name"
          />
        </div>
      </div>
      <h2 class="text-xl font-medium mt-7">Forgot Password</h2>
      <p class="text-xs mt-1 font-light text-gray-500">
        Enter your email and we’ll send you a link to reset your password.
      </p>
      <form
        class="space-y-4 mt-5"
        action="javascript:void(0)"
        @submit="forgotPassword()"
      >
        <div>
          <label
            for="email"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Email Address
          </label>
          <div class="mt-1.5">
            <input
              id="email"
              name="email"
              type="email"
              placeholder="Enter Your Email"
              v-model="email"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
            <small
              id="email_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
        <div>
          <Button
            class="w-full mt-2"
            :disabled="processing"
            @click="handleSend"
          >
            <i
              v-if="processing"
              class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
            ></i>
            {{ processing ? "Please Wait" : "Send" }}
          </Button>

          <div class="text-center w-full mt-3">
            <div class="text-sm leading-6 text-gray-600">
              <router-link
                :to="{ name: 'login' }"
                :class="[
                  isLightColor
                    ? 'text-custom-700 hover:text-custom-800'
                    : 'text-custom-500 hover:text-custom-600',
                  'font-medium',
                ]"
              >
                Back to login
              </router-link>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: "",
      processing: false,
      lastClicked: null,
    };
  },
  methods: {
    forgotPassword() {
      this.hideError();
      this.$axios
        .post("/forgot-password", {
          email: this.email,
        })
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.email = "";
        })
        .catch((error) => {
          if (error.response !== undefined) {
            const { status, data } = error.response;
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
<style>
.shadowcls {
  box-shadow: 6px 2px 15px 1px lightgray;
}
</style>


