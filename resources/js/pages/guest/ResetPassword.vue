<template>
  <div class="w-full p-4 mx-auto flex justify-center items-center min-h-screen">
    <div class="relative bg-white sm:w-[400px] shadowcls my-10 p-6 rounded-lg">
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
      <h2 class="text-xl font-medium mt-8">Set New Password</h2>
      <p class="text-xs mt-1 font-light text-gray-500">
        Your new password must be different from old one.
      </p>
      <form class="mt-4" action="javascript:void(0)" @submit="resetPassword()">
        <div>
          <label
            for="password"
            class="block text-tiny text-neutral-800 font-medium"
          >
            New Password
          </label>
          <div class="mt-1.5">
            <div class="relative">
              <input
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter Password"
                v-model="user.password"
                :class="{ 'tracking-widest': !showPassword }"
                class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
              />
              <PasswordVisibility
                :showPassword="showPassword"
                @toggle="showPassword = !showPassword"
              />
            </div>
            <small
              id="password_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
        <div class="mt-4">
          <label
            for="password_confirmation"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Confirm Password
          </label>
          <div class="mt-1.5">
            <div class="relative">
              <input
                id="password_confirmation"
                name="password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="Enter Confirm Password"
                v-model="user.password_confirmation"
                :class="{ 'tracking-widest': !showConfirmPassword }"
                class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
              />
              <PasswordVisibility
                :showPassword="showConfirmPassword"
                @toggle="showConfirmPassword = !showConfirmPassword"
              />
            </div>
            <small
              id="password_confirmation_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
        <Button class="w-full mt-5" :disabled="processing">
          <i
            v-if="processing"
            class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
          ></i>
          {{ processing ? "Please Wait" : "Save Password" }}
        </Button>
        <div class="mt-5 text-center text-sm">
          <router-link
            :to="{ name: 'login' }"
            :class="[isLightColor ? 'text-custom-700' : 'text-custom-500']"
            >Back to login</router-link
          >
        </div>
      </form>
    </div>
  </div>
</template>
  
  <script>
export default {
  data() {
    return {
      user: {
        token: this.$route.params.token,
        password: "",
        password_confirmation: "",
      },
      processing: false,
      showPassword: false,
      showConfirmPassword: false,
    };
  },
  methods: {
    async resetPassword() {
      this.hideError();
      this.processing = true;

      await this.$axios
        .post("/reset-password", this.user)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.$router.push({
            name: "login",
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
  
  <style>
.shadowcls {
  box-shadow: 6px 2px 15px 1px lightgray;
}
</style>