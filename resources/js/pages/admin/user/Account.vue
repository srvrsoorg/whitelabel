<template>
  <div class="my-5 xl:pt-4 mt-8 ml-3 rounded-md bg-white border relative">
    <div
      class="-left-5 p-2 bg-white -top-5 absolute flex justify-center items-center gap-2"
    >
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'material-symbols-outlined',
        ]"
      >
        account_circle
      </span>
      <p class="font-medium">Update Profile</p>
    </div>

    <div
      class="grid sm:grid-cols-2 grid-cols-1 mt-5 mb-0 xl:px-10 px-5 xl:gap-40 sm:gap-x-14 gap-1"
    >
      <div>
        <div
          class="grid 2xl:grid-cols-8 xl:grid-cols-12 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 md:pt-0 pt-2 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Full Name</label
            >
          </div>
          <div class="xl:col-span-9 2xl:col-span-6 col-span-12">
            <input
              v-model="userInfo.name"
              type="text"
              id="name"
              placeholder="Enter Name"
              class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid 2xl:grid-cols-8 xl:grid-cols-12 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 md:pt-0 pt-2 sm:col-span-3 col-span-12 hidden xl:block"
          ></div>
          <div class="xl:col-span-9 2xl:col-span-6 col-span-12">
            <small
              id="name_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid 2xl:grid-cols-9 xl:grid-cols-12 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 md:pt-0 pt-2 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Email</label
            >
          </div>
          <div class="xl:col-span-9 2xl:col-span-7 col-span-12">
            <input
              v-model="userInfo.email"
              type="text"
              id="email"
              placeholder="Enter Email"
              class="block w-full rounded-md border border-neutral-300 disabled:opacity-50 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid 2xl:grid-cols-9 xl:grid-cols-12 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 md:pt-0 pt-2 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="xl:col-span-9 2xl:col-span-7 col-span-12">
            <small
              id="email_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 my-2.5 xl:my-5 mb-0 xl:px-10 px-5 xl:gap-40 sm:gap-x-14 gap-1"
    >
      <div>
        <div
          class="grid 2xl:grid-cols-8 xl:grid-cols-12 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-3 2xl:col-span-2 sm:col-span-3 col-span-12">
            <label
              for="country_name"
              class="block text-tiny text-nowrap after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
            >
              Credits
            </label>
          </div>
          <div class="xl:col-span-9 2xl:col-span-6 col-span-12">
            <div class="flex">
              <div
                class="pointer-events-none flex items-center pl-3 pr-2 py-2 bg-gray-50 text-gray-500 border border-neutral-300 rounded-l-md"
              >
                <span class="text-tiny pr-0.5">{{
                  siteSettings.currency_symbol
                }}</span>
              </div>
              <input
                v-model="userInfo.credits"
                onwheel="this.blur()"
                type="number"
                id="credits"
                placeholder="0"
                class="w-full block rounded-r-md border border-l-0 border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              />
            </div>
          </div>
        </div>
        <div
          class="grid 2xl:grid-cols-8 xl:grid-cols-12 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="xl:col-span-9 2xl:col-span-6 col-span-12">
            <small
              id="credits_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 2xl:grid-cols-9 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 md:pt-0 pt-2 sm:col-span-3 col-span-12"
          >
            <label
              for="Password"
              class="block text-tiny text-neutral-800 font-medium"
            >
              Password
            </label>
          </div>
          <div class="xl:col-span-9 2xl:col-span-7 col-span-12">
              <div class="relative">
                <input
                  id="password"
                  name="password"
                  :type="ConfirmPassword ? 'text' : 'password'"
                  placeholder="Enter Password"
                  v-model="userInfo.password"
                  :class="{ 'tracking-widest': !ConfirmPassword }"
                  class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                />
                <PasswordVisibility
                  :showPassword="ConfirmPassword"
                  @toggle="ConfirmPassword = !ConfirmPassword"
                />
              </div>
              <small
                id="password_message"
                class="error_message text-red-600 text-xs"
              ></small>
            </div>
          </div>
        <div
          class="grid xl:grid-cols-12 2xl:grid-cols-9 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 md:pt-0 pt-2 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="xl:col-span-9 2xl:col-span-7 col-span-12">
            <small
              id="password_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>

    <div
      class="grid sm:grid-cols-2 grid-cols-1 my-2.5 xl:my-5 mb-0 xl:px-10 px-5 xl:gap-40 sm:gap-x-14 gap-1"
    >
      <div>
        <div
          class="grid 2xl:grid-cols-8 xl:grid-cols-12 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-3 2xl:col-span-2 col-span-3">
            <label
              for="Country"
              class="block text-tiny text-nowrap after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
            >
              Country
            </label>
          </div>
          <div class="xl:col-span-9 2xl:col-span-6 col-span-12">
            <select
              id="country_name"
              v-model="userInfo.country_code"
              @change="updateCountryName()"
              placeholder="Select a Country"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-2 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-00"
            >
              <option value="" disabled>Select a Country</option>
              <template v-for="country in countries" :key="country.iso2">
                <option :value="country.iso2">
                  {{ country.country_name }}
                </option>
              </template>
            </select>
          </div>
        </div>
        <div
          class="grid 2xl:grid-cols-8 xl:grid-cols-12 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 col-span-3 xl:block hidden"
          ></div>
          <div class="xl:col-span-9 2xl:col-span-6 col-span-12">
            <small
              id="country_name_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 2xl:grid-cols-9 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 md:pt-0 pt-2 sm:col-span-3 col-span-12"
          >
            <label
              for="region"
              class="block text-tiny text-nowrap after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
            >
              Region
            </label>
          </div>
          <div class="xl:col-span-9 2xl:col-span-7 col-span-12">
            <select
              id="region_name"
              name="region_name"
              @change="updateRegionName()"
              v-model="userInfo.region_code"
              placeholder="Select a Region"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            >
              <option value="" disabled>Select a Region</option>
              <template v-for="(region, key) in getRegions" :key="key">
                <option :value="region.state_iso2">
                  {{ region.state_name }}
                </option>
              </template>
            </select>
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 2xl:grid-cols-9 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 md:pt-0 pt-2 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="xl:col-span-9 2xl:col-span-7 col-span-12">
            <small
              id="region_name_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 my-2.5 xl:my-5 mb-0 xl:px-10 px-5 xl:gap-40 sm:gap-x-14 gap-1"
    >
      <div>
        <div
          class="grid 2xl:grid-cols-8 xl:grid-cols-12 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-3 2xl:col-span-2 sm:col-span-3 col-span-12">
            <label
              for="status"
              class="block text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
            >
              Status
            </label>
          </div>
          <div class="xl:col-span-9 2xl:col-span-6 col-span-12">
            <select
              v-model="userInfo.status"
              id="status"
              name="status"
              class="block w-full rounded-md capitalize border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            >
              <option
                :value="status.name"
                selected
                v-for="status in filterstatus"
                :key="status"
              >
                {{ status.name }}
              </option>
            </select>
          </div>
        </div>
        <div
          class="grid 2xl:grid-cols-8 xl:grid-cols-12 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="xl:col-span-9 2xl:col-span-6 col-span-12">
            <small
              id="status_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 2xl:grid-cols-9 md:grid-cols-12 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-3 2xl:col-span-2 md:pt-0 pt-2 col-span-4">
            <label
              for="Timezone"
              class="block whitespace-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
            >
              Timezone
            </label>
          </div>
          <div class="xl:col-span-9 2xl:col-span-7 col-span-12">
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
              <template v-slot:caret>
                <div class="multiselect__select">
                  <span class="material-symbols-outlined mt-0.5 text-xl">
                    keyboard_arrow_down
                  </span>
                </div>
              </template>
            </multiselect>
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 2xl:grid-cols-9 md:grid-cols-12 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:col-span-2 md:pt-0 pt-2 col-span-4 xl:block hidden"
          ></div>
          <div class="xl:col-span-9 2xl:col-span-7 col-span-12">
            <small
              id="timezone_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 my-5 mt-4 mb-0 xl:px-10 px-5 xl:gap-x-40 sm:gap-x-14 gap-1"
    >
      <div
        class="grid xl:grid-cols-12 md:grid-cols-4 grid-cols-12 sm:gap-x-3 gap-1"
      >
        <div class="xl:col-span-12 col-span-12">
          <div
            class="grid grid-rows-2 grid-flow-col justify-between xl:col-span-9 2xl:col-span-10 sm:col-span-9 col-span-12 border-[#CBD5E0] border rounded-md p-3.5"
          >
            <div class="col-span-12">
              <span class="block text-tiny font-medium">
                E-mail Verification
              </span>
            </div>
            <div class="row-span-2">
              <span class="block text-tiny text-gray-500"> Verified </span>
            </div>
            <div class="row-span-3 xs:items-center flex">
              <input
                type="checkbox"
                name="verified"
                v-model="userInfo.isVerified"
                :class="[isLightColor ? 'text-custom-700' : 'text-custom-500 ']"
                class="h-5 w-5 items-end rounded border-gray-200 focus:ring-cutext-custom-500"
              />
            </div>
          </div>
        </div>
      </div>
      <div
        class="border-[#CBD5E0] border rounded-md bg-[#F6F6F6] p-3.5 sm:mt-0 mt-5"
      >
        <div class="flex justify-between items-center pb-2">
          <span class="block text-[16px] font-medium">
            Admin Access Control
          </span>
          <input
            type="checkbox"
            name="admin_access"
            v-model="userInfo.is_admin"
            :disabled="user.id == $route.params.user"
            :class="[isLightColor ? 'text-custom-700' : 'text-custom-500 ']"
            class="h-5 w-5 items-end rounded border-gray-300 focus:ring-custom-500"
          />
        </div>
        <div class="xs:max-w-[90%]">
          <span class="text-tiny text-gray-500">
            Check this box to grant full admin rights, allowing the user to
            manage all system settings, tools and features.
          </span>
        </div>
      </div>
    </div>

    <div class="flex flex-row-reverse p-5 xl:px-10">
      <div class="text-end rounded-md">
        <Button :disabled="processing" @click="updateProfile">
          <i
            v-if="processing"
            class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
          ></i>
          {{ processing ? "Please wait" : "Update" }}
        </Button>
      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import { useAuthStore } from "@/store/auth";
import { mapState } from "pinia";
export default {
  name: "Account",
  components: {
    Multiselect,
  },
  data() {
    return {
      breadcrumb: {
        // title: "User",
        icon: "groups",
        pages: [{ name: "Profile" }],
      },
      processing: false,
      userInfo: {
        email_verified_at: false,
        is_admin: false,
        name: "",
        email: "",
        country_name: "",
        country_code: "",
        region_name: "",
        region_code: "",
        timezone: "",
        password: "",
        status: "",
        credits: "",
        isVerified: false,
      },
      filterstatus: [
        {
          name: "banned",
        },
        {
          name: "active",
        },
        {
          name: "pending",
        },
      ],
      ConfirmPassword: false,
      countries: [],
      timezones: [],
      timezone: null,
    };
  },
  watch: {
    "userInfo.timezone": {
      handler(val) {
        if (val) {
          setTimeout(() => {
            this.timezone = this.timezones.find((timezone) => {
              return timezone.value === val;
            });
          }, 200);
        }
      },
    },
  },
  computed: {
    ...mapState(useAuthStore, ["user"]),
    getRegions() {
      let country = this.countries.find(
        (country) => country.country_name === this.userInfo.country_name
      );
      return country ? country.states : [];
    },
  },
  created() {
    this.fetchTimezones();
    this.getCountries();
    this.$emit("pass-breadcrumb", this.breadcrumb);
    this.fetchUser();
  },
  methods: {
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
      this.userInfo.country_name = "";
      this.userInfo.region_code = "";
      this.userInfo.region_name = "";
      this.userInfo.country_name = this.countries.find(
        (country) => country.iso2 === this.userInfo.country_code
      ).country_name;
    },
    updateRegionName() {
      this.userInfo.region_name = "";
      let country_data = this.countries.find(
        (country_data) => country_data.iso2 === this.userInfo.country_code
      );
      let region_data = country_data.states.find(
        (region) => region.state_iso2 === this.userInfo.region_code
      );
      this.userInfo.region_name = region_data ? region_data.state_name : "";
    },
    async fetchUser() {
      const loader = this.$loading.show();
      await this.$axios
        .get(`/admin/users/${this.$route.params.user}`)
        .then(({ data }) => {
          this.userInfo = {
            email_verified_at:
              data.user && data.user.email_verified_at !== null ? true : false,
            is_admin: data.user.is_admin || false,
            name: data.user.name || "",
            email: data.user.email || "",
            country_name: data.user.country_name || "",
            country_code: data.user.country_code || "",
            region_name: data.user.region_name || "",
            region_code: data.user.region_code || "",
            timezone: data.user.timezone || "",
            password: "", // Avoid setting password from the API response
            status: data.user.status || "",
            credits: data.user.credits || "",
            isVerified:
              data.user && data.user.email_verified_at !== null ? true : false,
          };
        })
        .catch(({ response: data }) => {
          this.$toast.error(data.message);
        }).finally(()=>{
          if (loader) {
            loader.hide()
          }
        });
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
    async updateProfile() {
      this.hideError();
      this.processing = true;
      this.userInfo.email_verified_at = this.userInfo.isVerified;
      const id = this.$route.params.user;
      if (this.timezone) {
        this.userInfo.timezone = this.timezone.value;
      } else {
        this.userInfo.timezone = null;
      }
      this.$axios
        .patch(`/admin/users/${id}`, this.userInfo)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.fetchUser();
        })
        .catch(({ response }) => {
          if (response.status !== 422) {
            this.$toast.error(response.data.message);
          } else {
            this.displayError(response.data);
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },
};
</script>

