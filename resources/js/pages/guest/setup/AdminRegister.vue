<template>
  <InstallationInfo />
  <div class="w-full max-w-md mx-auto 2xl:mt-32 sm:mt-10 mt-5 mb-5 p-4">
    <div class="pt-5">
      <form action="javascript:void(0)" @submit="register()" class="mt-3">
        <div>
          <label
            for="name"
            class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
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
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
            <small id="name_message" class="error_message text-red-500"></small>
          </div>
        </div>
        <div class="mt-4">
          <label
            for="email"
            class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
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
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
            <small
              id="email_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
        <div class="grid grid-cols-1 gap-x-4">
          <div class="mt-4">
            <label
              for="password"
              class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
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
                  class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
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
        </div>
        <div class="grid md:grid-cols-2 grid-cols-1 gap-x-4">
          <div class="mt-4">
            <label
              for="country_name"
              class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >
              Country
            </label>
            <div class="mt-1.5">
              <select
                id="country_name"
                v-model="admin.country_code"
                @change="updateCountryName()"
                placeholder="Select a Country"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
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
              class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
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
                class="border text-gray-900 text-sm rounded-lg border-gray-300 focus:border-sa-500 focus:ring-0 block w-full p-2"
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
        <div class="mt-4">
          <label
            for="region_name"
            class="block text-tiny text-neutral-800 after:content-['*'] after:ml-0.5 after:text-red-500 font-medium mb-1.5"
            >Timezone</label
          >
          <multiselect
            v-model="timezone"
            :options="timezones"
            :close-on-select="false"
            :clear-on-select="false"
            :searchable="true"
            :hideSelected="true"
            :closeOnSelect="true"
            placeholder="Select Timezone"
            label="name"
            track-by="value"
          >
          </multiselect>
          <small
            class="text-red-500 error_message"
            id="timezone_message"
          ></small>
        </div>
        <div class="flex flex-wrap justify-between gap-x-5 mt-6">
          <div class="flex justify-between items-center">
            <router-link
              :class="[
                isLightColor
                  ? 'text-custom-700 border-custom-700'
                  : 'text-custom-500 border-custom-500',
                ' border rounded-md px-5 py-1.5 text-sm font-medium flex items-center',
              ]"
              :to="{
                name: 'setupSmtp',
              }"
            >
              Back
            </router-link>
          </div>
          <Button :disabled="processing" :class="['px-5']">
            <i
              v-if="processing"
              class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
            ></i>
            {{ processing ? "Please Wait" : "Register" }}
          </Button>
        </div>
      </form>
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
        timezone: "",
      },
      timezones: [],
      timezone: null,
      processing: false,
      showPassword: false,
      countries: [],
    };
  },
  created() {
    this.fetchTimezones();
    this.getCountries();

    this.timezone = { name: "(UTC+00:00) Etc/UTC", value: "Etc/UTC" };

    if (this.registerComplete) {
      this.$router.push({
        name: "setupSiteSettings",
      });
    }
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
    async fetchTimezones() {
      await this.$axios
        .get("/timezone")
        .then(({ data }) => {
          Object.keys(data).forEach((key) => {
            let obj = {
              name: `${data[key]}`,
              value: key,
            };
            this.timezones.push(obj);
          });
        })
        .catch(() => {
          this.timezones = [];
        });
    },
    updateLocation() {
      this.admin.country_name = "";
      this.admin.region_name = "";

      const countryName = $("#country_name option:selected").html();
      if (countryName !== "Select Country") {
        this.admin.country_name = countryName;
      }

      setTimeout(() => {
        const regionName = $("#region_name option:selected").html();
        if (regionName !== "Select Region") {
          this.admin.region_name = regionName;
        }
      }, 500);
    },
    async register() {
      this.hideError();
      this.processing = true;
      if (this.timezone) {
        this.admin.timezone = this.timezone.value;
      } else {
        this.admin.timezone = null;
      }

      await this.$axios
        .post("/setup/register", this.admin)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.setAccessToken(data.token);
          this.setUser(data.user);
          this.setIsAdmin(data.user.is_admin);
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
