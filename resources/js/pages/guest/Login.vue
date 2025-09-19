<template>
  <div class="p-4 mx-auto flex justify-center items-center min-h-screen">
    <div class="relative bg-white sm:w-[400px] shadowcls my-10 p-4 rounded-lg">
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
      <h2 class="text-xl font-medium mt-5">Login</h2>
      <p class="text-gray-500 text-sm mt-1">
        Quickly access to {{ app_name }} dashboard
      </p>
      <form class="mt-5" action="javascript:void(0)" @submit="login()">
        <div>
          <label for="email" class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
          >Email</label
        >
          <div class="mt-1">
            <div>
           </div>
            <input
              id="email"
              name="email"
              type="email"
              placeholder="Enter Email"
              v-model="email"
              class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
            <small
              id="email_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
        <div class="mt-5">
          <label
          for="password"
          class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
          >Password</label
        >
          <div class="mt-1">
            <div class="relative">
              <input
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter Password"
                v-model="password"
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
        <div class="text-end mt-6">
          <Button
            class="text-tiny w-full"
            :disabled="processing"
            :class="['!text-tiny !font-semibold']"
          >
            <i
              v-if="processing"
              class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
            ></i>
            {{ processing ? "Please Wait" : " Login" }}
          </Button>
        </div>
      </form>
      <div class="flex items-center justify-center mt-4" v-if="smtpComplete">
        <div class="text-sm leading-6">
          <router-link
            :to="{ name: 'forgotPassword' }"
            :class="[isLightColor ? 'text-custom-700' : 'text-custom-500']"
          >
            Forgot your Password?
          </router-link>
        </div>
      </div>
      <div class="flex items-center justify-center mt-2">
        <div class="text-sm leading-6 text-gray-500">
          <span class="">You don't have an account? </span>
          <router-link
            :to="{ name: 'register' }"
            :class="[
              isLightColor ? 'text-custom-700' : 'text-custom-500',
              'font-medium ',
            ]"
          >
            Register
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/store/auth";
import { useSetupStore } from "@/store/setup";
export default {
  data() {
    return {
      email: "",
      password: "",
      processing: false,
      showPassword: false,
    };
  },
  computed: {
    ...mapState(useSetupStore, ["smtpComplete"]),
  },
  methods: {
    ...mapActions(useAuthStore, [
      "setAuthenticated",
      "setAccessToken",
      "setUser",
      "setIsAdmin",
    ]),
    async login() {
      this.hideError();
      this.processing = true;

      await this.$axios
        .post("/login", {
          email: this.email,
          password: this.password,
        })
        .then(({ data }) => {
          this.setUser(data.user);
          this.setIsAdmin(data.user.is_admin);

          if (data.user.two_fa_enable) {
            this.$router.push({
              name: "Tfa",
            });
          } else {
            this.$toast.success(data.message);
            this.setAccessToken(data.token);
            this.setAuthenticated(true);
            this.$router.push("/");
          }
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