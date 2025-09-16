<template>
  <div
    class="w-full max-w-xl flex justify-center items-center min-h-screen p-4 mx-auto"
  >
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
      <h2 class="text-xl font-medium mt-7 px-2.5">Register</h2>
      <p class="text-gray-500 text-sm mt-1 font-normal px-2.5">
        Quickly access to  {{ app_name }}  dashboard
      </p>
      <form
        action="javascript:void(0)"
        @submit="register()"
        class="mt-5 px-2.5"
      >
        <div>
          <label
            for="name"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Name
          </label>
          <div class="mt-1.5">
            <input
              id="name"
              name="name"
              type="text"
              placeholder="Enter Name"
              v-model="admin.name"
              class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
            />
            <small id="name_message" class="error_message text-red-500"></small>
          </div>
        </div>
        <div class="mt-4">
          <label
            for="email"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Email
          </label>
          <div class="mt-1.5">
            <input
              id="email"
              name="email"
              type="email"
              placeholder="Enter Email"
              v-model="admin.email"
              class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
            />
            <small
              id="email_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
        <div class="mt-4">
          <label
            for="password"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Password
          </label>
          <div class="mt-1.5">
            <div class="relative">
              <input
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter Password"
                v-model="admin.password"
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

        <div class="grid md:grid-cols-2 grid-cols-1 gap-x-4">
          <div class="mt-4">
            <label
              for="country_name"
              class="block text-tiny text-neutral-800 font-medium"
            >
              Country
            </label>
            <div class="mt-1.5">
              <select
                id="country_name"
                v-model="admin.country_code"
                @change="updateCountryName()"
                placeholder="Select a Country"
                class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
              >
                <option value="" disabled>Select a Country</option>
                <template v-for="country in countries" :key="country.iso2">
                  <option
                    :value="country.iso2"
                  >
                    {{ country.country_name }}
                  </option>
                </template>
              </select>
              <small
                id="country_name_message"
                class="error_message text-red-500"
              ></small>
            </div>
          </div>
          <div class="mt-4">
            <label
              for="region_name"
              class="block text-tiny text-neutral-800 font-medium"
            >
              Region
            </label>
            <div class="mt-1.5">
              <select
                id="region_name"
                name="region_name"
                @change="updateRegionName()"
                v-model="admin.region_code"
                placeholder="Select a Region"
                class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
              >
                <option value="" disabled>Select a Region</option>
                <template
                  v-for="(region, key) in getRegions"
                  :key="key"
                >
                  <option :value="region.state_iso2">
                    {{ region.state_name }}
                  </option>
                </template>
              </select>
              <small
                id="region_name_message"
                class="error_message text-red-500"
              ></small>
            </div>
          </div>
        </div>
        <div class="mt-4 text-sm text-gray-600" v-if="siteSettings && (siteSettings.privacy_policy || siteSettings.terms_condition)">
          By registering, you agree to
          <template v-if="siteSettings.terms_condition">
            <a
              :href="siteSettings.terms_condition"
              target="_blank"
              class="text-custom-500 hover:underline"
            >Terms & Conditions</a>
          </template>
          <template v-if="siteSettings.privacy_policy && siteSettings.terms_condition"> and </template>
          <template v-if="siteSettings.privacy_policy">
            <a
              :href="siteSettings.privacy_policy"
              target="_blank"
              class="text-custom-500 hover:underline"
            >Privacy Policy</a>
          </template>.
        </div>
        <div class="text-end mt-3">
          <Button class="text-tiny w-full" :disabled="processing">
            <i
              v-if="processing"
              class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
            ></i>
            {{ processing ? "Please Wait" : " Register" }}
          </Button>
        </div>
      </form>
      <div class="flex items-center justify-center mt-3">
        <div class="text-sm leading-6 text-gray-600">
          <span class="font-normal">Already have an account? </span>
          <router-link
            :to="{ name: 'login' }"
            :class="[
              isLightColor
                ? 'text-custom-700 '
                : 'text-custom-500 hover:text-custom-600',
              'font-medium',
            ]"
          >
            Login
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/store/auth";
import { useSetupStore } from "@/store/setup.js";
import { defineAsyncComponent } from "vue";
import Multiselect from "vue-multiselect";

export default {
  data() {
    return {
      admin: {
        name: "",
        email: "",
        password: "",
        country_name: "",
        country_code: "",
        region_name: "",
        region_code: "",
      },
      processing: false,
      showPassword: false,
      showConfirmPassword: false,
      countries: [],
    };
  },
  created() {
    this.getCountries();
  },
  components: {
    InstallationInfo: defineAsyncComponent(() =>
      import("@/components/InstallationInfo.vue")
    ),
    Multiselect,
  },
  computed: {
    ...mapState(useSetupStore, ["smtpComplete", "registerComplete"]),
    getRegions() {
      let country = this.countries.find(
        (country) => country.country_name === this.admin.country_name
      );
      return country ? country.states : [];
    },
  },
  methods: {
    ...mapActions(useAuthStore, [
      "setAuthenticated",
      "setAccessToken",
      "setUser",
      "setIsAdmin",
    ]),
    async getCountries() {
      await this.$axios
        .get(`/countries`)
        .then(({ data }) => {
          this.countries = data.countries;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    updateCountryName() {
      this.admin.country_name = "";
      this.admin.region_code = ""
      this.admin.region_name = ""
      this.admin.country_name = this.countries.find(
        (country) => country.iso2 === this.admin.country_code
      ).country_name;
    },
    updateRegionName() {
      this.admin.region_name = "";
      let country_data = this.countries.find(
        (country_data) => country_data.iso2 === this.admin.country_code
      );
      let region_data = country_data.states.find(region => region.state_iso2 === this.admin.region_code)
      this.admin.region_name =  region_data ? region_data.state_name : ''
    },
    async register() {
      this.hideError();
      this.processing = true;

      await this.$axios
        .post("/register", this.admin)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.setAccessToken(data.token);
          this.setUser(data.user);
          this.setIsAdmin(data.is_admin);
          this.setAuthenticated(true);
          location.reload();
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