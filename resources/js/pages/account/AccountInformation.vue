<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <h1 class="text-xl text-[#31363f] font-medium">Profile</h1>
  <div class="my-6 mt-8 ml-3 rounded-md bg-white border relative">
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
      <p class="font-medium text-lg">Update Profile</p>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:mt-10 mt-8 xl:my-6 my-3 xl:px-14 px-5 xl:gap-x-40 sm:gap-x-14 gap-1"
    >
      <div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 md:pt-0 pt-2 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Full Name</label
            >
          </div>
          <div class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12">
            <input
              v-model="userInfo.name"
              type="text"
              id="name"
              placeholder="Enter Name"
              class="w-full block rounded-md border border-primary focus:border-primary py-1.5 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 md:pt-0 pt-2 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12">
            <small
              id="name_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>

      <div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Email</label
            >
          </div>
          <div class="xl:col-span-8 2xl:col-span-9 sm:col-span-8 col-span-12">
            <input
              v-model="userInfo.email"
              readonly
              type="text"
              name="email"
              id="email"
              placeholder="Enter Email"
              class="block w-full rounded-md border border-primary read-only:bg-gray-50 focus:border-primary py-1.5 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12 hidden xl:block"
          ></div>
          <div class="xl:col-span-8 2xl:grid-cols-10 sm:col-span-8 col-span-12">
            <small
              id="email_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:my-6 my-3 xl:px-14 px-5 xl:gap-x-40 sm:gap-x-14 gap-1 "
    >
      <div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 md:pt-0 sm:col-span-3 col-span-12"
          >
            <label
              for="country_name"
              class="block text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
            >
              Country
            </label>
          </div>
          <div class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12">
            <select
              id="country_name"
              v-model="userInfo.country_code"
              @change="updateCountryName()"
              placeholder="Select a Country"
              class="block w-full rounded-md border border-primary focus:border-primary py-2 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
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
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 md:pt-0 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12">
            <small
              id="country_name_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12"
          >
            <label
              for="region_name"
              class="block text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
            >
              Region
            </label>
          </div>
          <div class="xl:col-span-8 2xl:col-span-9 sm:col-span-8 col-span-12">
            <select
              id="region_name"
              name="region_name"
              @change="updateRegionName()"
              v-model="userInfo.region_code"
              placeholder="Select a Region"
              class="block w-full rounded-md border border-primary focus:border-primary placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
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
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 "
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>

          <div class="xl:col-span-8 2xl:grid-cols-10 sm:col-span-8 col-span-12">
            <small
              id="region_name_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:mt-6 xl:px-14 px-5 xl:gap-x-40 sm:gap-x-14 gap-1 "
    >
      <div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 md:pt-0 sm:col-span-3 col-span-12"
          >
            <label
              for="region_name"
              class="block text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
              >Timezone</label
            >
          </div>
          <div class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12">
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
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 md:pt-0 sm:col-span-3 col-span-12"
          ></div>

          <div class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12">
            <small
              id="timezone_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-row-reverse p-5 xl:px-14">
      <div class="text-end">
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
  <div
    v-if="userInfo.credits > 0"
    class="grid xl:grid-cols-1 grid-cols-1 gap-40 xl:gap-40 md:gap-20"
  >
    <div class="pl-3">
      <p class="text-xl text-[#31363f] font-medium">Delete Account</p>
      <div class="mt-3 rounded-lg bg-[#F8F9FA] border">
        <div class="sm:flex justify-between items-start gap-8 p-5">
          <p class="text-gray-500 text-tiny">
            Deleting your account will instantly revoke all access to servers,
            and your data will be removed from our current database. If you have
            servers in your account, the management panel and support for those
            servers will also be revoked. Please consider your decision wisely.
          </p>
          <div class="flex justify-end mt-4 sm:mt-0">
            <Button
              @click="accountdelete"
              :class="[
                '!bg-[#DC2626] !flex !items-center gap-1 whitespace-nowrap !py-1 text-white  ',
              ]"
            >
              <span class="material-symbols-outlined text-[20px]">
                delete
              </span>
              <span class="py-1.5">Delete Account</span>
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <Confirmation
    :show="openConfirmation"
    :showLoader="showLoader"
    :confirmationTitle="'Delete Account'"
    :submitBtnTitle="`Yes I'm sure`"
    @confirm="deleteAccount"
    @closeModal="closeModal"
  >
    <template #icon>
      <span class="material-symbols-outlined text-[22px] text-red-500"
        >account_box</span
      >
    </template>
    <template v-slot:content>
      <span class="text-tiny text-gray-600">{{ message }}</span>
      <div class="my-5">
        <span
          class="text-gray-900 font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
          >Password</span
        >
        <div class="relative">
          <input
            id="password"
            name="password"
            :type="ConfirmPassword ? 'text' : 'password'"
            placeholder="Enter Password"
            v-model="password"
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

      <div class="my-5">
        <p v-if="user" class="font-medium text-tiny">
          {{
            `Once you delete your account your all data on ${user.email}  will be lost and you won't be able to recover it back.`
          }}
        </p>
      </div>
    </template>
  </Confirmation>
</template>

<script>
import { useAuthStore } from "@/store/auth";
import { mapState, mapActions } from "pinia";
import Multiselect from "vue-multiselect";

export default {
  name: "AccountInformation",
  components: {
    Multiselect,
  },

  data() {
    return {
      breadcrumb: {
        // title: "Account",
        icon: "account_box",
        pages: [{ name: "Account" },{ name: "Profile" }],
      },
      processing: false,
      id: "",
      openConfirmation: false,
      showLoader: false,
      message: "",
      userInfo: {
        name: "",
        email: "",
        country_name: "",
        country_code: "",
        region_name: "",
        region_code: "",
        timezone: "",
      },
      ConfirmPassword: false,
      countries: [],
      timezones: [],
      timezone: null,
    };
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
  async created() {
    await this.fetchTimezones();
    await this.getCountries();
    await this.getUser();
    this.userInfo = { ...this.user };
    if (this.userInfo.timezone) {
      this.timezone = this.timezones.find(
        (timezone) => timezone.value === this.userInfo.timezone
      );
    }
  },
  methods: {
    ...mapActions(useAuthStore, ["getUser", "authLogout"]),
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
    async deleteAccount() {
      this.showLoader = true;
      this.hideError();
      await this.$axios
        .post("user/delete", { password: this.password })
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.closeModal();
          this.authLogout();
          this.$router.push({
            name: "login",
          });
        })
        .catch(({ response }) => {
          if (response.status === 422) {
            this.displayError(response.data);
          } else {
            this.$toast.error(response.data.message);
            this.closeModal();
          }
        })
        .finally(() => {
          this.showLoader = false;
        });
    },
    accountdelete() {
      this.openConfirmation = true;
      if (this.user) {
        this.message = `Are you sure you want to delete the account linked to ${this.user.email}?`;
      }
    },
    closeModal() {
      this.openConfirmation = false;
      this.password = "";
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
    async updateProfile() {
      this.hideError();
      this.processing = true;
      if (this.timezone) {
        this.userInfo.timezone = this.timezone.value;
      } else {
        this.userInfo.timezone = null;
      }
      this.$axios
        .patch("/user/update", this.userInfo)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.getUser();
        })
        .catch(({ response }) => {
          if (response.status === 422) {
            this.displayError(response.data);
          } else {
            this.$toast.error(response.data.message);
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },
};
</script>
