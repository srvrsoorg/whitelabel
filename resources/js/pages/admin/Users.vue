<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="sm:flex justify-between items-center gap-4">
    <p class="text-xl font-medium">Users</p>
    <div class="xs:flex justify-end sm:mt-0 mt-3 items-center gap-5">
      <div class="relative xl:min-w-96 w-full">
        <input
          v-model="search"
          @input="handleSearch()"
          type="text"
          name="text"
          id="text"
          class="w-full block rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          placeholder="Search User"
        />
        <button
          v-if="search"
          type="button"
          @click="clearSearch"
          class="border border-primary bg-[#F6F6F6] absolute inset-y-0 text-gray-500 right-10 flex items-center px-2.5"
        >
          <span v-tooltip="'Clear'" class="material-symbols-outlined text-[22px]">close</span>
        </button>
        <div
          :class="[
            textColorClass,
            'pointer-events-none absolute rounded-r-md inset-y-0 bg-[#F6F6F6] border border-primary right-0 items-center justify-center flex px-2',
          ]"
        >
          <span class="material-symbols-outlined text-[22px] text-gray-400">
            search
          </span>
        </div>
      </div>
      <div class="flex justify-end gap-5 items-center mt-5 xs:mt-0">
        <button
          @click="getUsers(pagination.current_page)"
          :class="[
            textColorClass,
            'bg-gray-50 p-1.5 border rounded-md justify-self-end flex items-center ',
          ]"
        >
          <span
            :class="[
              refreshing ? 'fa-spin' : '',
              'material-symbols-outlined text-[22px] text-gray-400',
            ]"
          >
            refresh
          </span>
        </button>
        <Button @click="openModal"> Create </Button>
      </div>
    </div>
  </div>
  <div class="h-full mt-5">
    <Table :head="thead" v-if="users.length > 0">
      <tr
        class="border-b border-primary text-[#2c3138] text-[13px]"
        v-for="user in users"
        :key="user.id"
      >
        <td class="py-4 pl-10 px-4 font-medium">#{{ user.id }}</td>
        <td class="whitespace-nowrap py-4 px-4">
          <div class="flex items-center">
            <div class="">
              <router-link
                :to="{
                  name: 'userProfile',
                  params: {
                    user: user.id,
                  },
                }"
              >
                <div class="flex max-w-52">
                  <span
                    class="font-medium truncate text-tiny"
                    v-tooltip="user.name"
                    >{{ user.name }}</span
                  >
                </div>
              </router-link>
              <div class="flex items-center mt-0.5 gap-0.5">
                <span
                  class="text-gray-500 max-w-44 truncate"
                  v-tooltip="user.email"
                  >{{ user.email }}</span
                >

                <span
                  @click="copyToClipboard(user.email)"
                  class="material-symbols-outlined text-[16px] text-blue-500 cursor-pointer"
                >
                  content_copy
                </span>
              </div>
              <span class="text-sm text-gray-500 block mt-1">
                {{ user.created_at }}
              </span>
            </div>
          </div>
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          {{ formatCurrency(user.credits) }}
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          {{ user.servers_count }}
        </td>
        <td class="whitespace-nowrap py-4 px-4 text-center">
          <span v-if="user.email_verified_at" class="text-green-600">
            Yes
          </span>
          <span v-else class="text-red-600"> No </span>
        </td>

        <td class="whitespace-nowrap py-4 font-medium pl-10">
          <div
            v-if="user.status == 'active'"
            class="flex items-center gap-1 text-green-600"
          >
            <span class="material-symbols-outlined text-[18px]">
              check_circle
            </span>
            <p>Active</p>
          </div>

          <div
            v-if="user.status == 'pending'"
            class="flex items-center gap-1 text-gray-600"
          >
            <span class="material-symbols-outlined text-[18px]">
              schedule
            </span>
            <p>Pending</p>
          </div>
          <div
            v-if="user.status == 'banned'"
            class="flex items-center gap-1 text-red-500"
          >
            <span class="material-symbols-outlined text-[18px]"> block </span>
            <p>Banned</p>
          </div>
        </td>
        <td class="whitespace-nowrap py-4 px-4 h-full text-center">
          <div class="flex items-center gap-4 justify-center">
            <router-link
              :to="{
                name: 'userProfile',
                params: {
                  user: user.id,
                },
              }"
            >
              <span
                v-tooltip="'View'"
                :class="[
                  'material-symbols-outlined p-1.5  rounded-md text-[20px]',
                  isLightColor
                    ? 'text-custom-700 bg-custom-200'
                    : 'bg-custom-50 text-custom-500',
                ]"
              >
                visibility
              </span>
            </router-link>
            <button
                @click="openUserStatus(user)"
                :disabled="user.status === 'pending' || user.id === this.user.id"
                :class="[
                  (user.status === 'pending' || user.id === this.user.id)
                    ? 'opacity-50 cursor-not-allowed'
                    : 'hover:opacity-80'
                ]"
              >
                <span
                  class="material-symbols-outlined text-red-500 bg-red-50 p-1.5 text-[20px] rounded-md"
                  v-if="user.status === 'active'"
                  v-tooltip="user.id === this.user.id ? 'You cannot ban your own account.' : 'Ban'"
                >
                  person_off
                </span>
                <span
                  class="material-symbols-outlined text-[20px] text-green-600 bg-green-400 bg-opacity-10 px-1.5 py-1.5 rounded-md"
                  v-else-if="user.status === 'banned'"
                  v-tooltip="user.id === this.user.id ? 'You cannot ban your own account.' : 'Active'"
                >
                  how_to_reg
                </span>
                <span
                  class="material-symbols-outlined text-gray-500 bg-gray-100 p-1.5 text-[20px] rounded-md"
                  v-else
                  v-tooltip="user.id === this.user.id ? 'You cannot ban your own account.' : ''"
                >
                  person_off
                </span>
              </button>
          </div>
        </td>
      </tr>
      <template #pagination>
        <div
          v-if="pagination.total > 10"
          :class="[
            pagination.total > 10 ? 'justify-between' : 'justify-end',
            'sm:flex gap-3 py-5 px-4',
          ]"
        >
          <div v-if="pagination.total > 10">
            <Perpage v-model="per_page" :initialPerPage="pagination.per_page" @changePage="handlePerPageChange" />
          </div>
          <div v-if="users.length > 0" class="mt-5 sm:mt-0">
            <Pagination
              :pagination="pagination"
              @page-change="handlePageChange"
            />
          </div>
        </div>
      </template>
    </Table>
    <template v-else>
      <TableSkeleton :heads="7" v-if="refreshing" />
      <Table :head="thead" v-else>
        <tr>
          <td colspan="7" class="text-center text-sm px-6 py-5">
            {{ refreshing ? "Please Wait" : "No Users Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>

  <Modal
    :show="open"
    @closeModal="closeModal"
    :modalTitle="'Create User'"
    :modelIcon="'person'"
    :customClass="['md:max-w-2xl text-xl']"
  >
    <div class="grid md:grid-cols-2 grid-cols-1 md:gap-4 gap-2">
      <div class="">
        <label
          for="name"
          class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
          >Name</label
        >
        <input
          v-model="userInfo.name"
          type="text"
          name="name"
          id="name"
          class="border text-gray-900 text-sm mt-2 rounded-lg border-primary focus:border-primary focus:ring-0 block w-full p-2"
          placeholder="Enter Name"
        />
        <small
          id="name_message"
          class="error_message text-red-500 text-xs"
        ></small>
      </div>
      <div class="col-span-1">
        <label
          for="email"
          class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
          >Email</label
        >
        <input
          v-model="userInfo.email"
          type="text"
          name="email"
          id="email"
          class="border text-gray-900 text-sm mt-2 rounded-lg border-primary focus:border-primary focus:ring-0 block w-full p-2"
          placeholder="Enter Email"
        />
        <small
          class="text-red-500 error_message text-xs"
          id="email_message"
        ></small>
      </div>
    </div>
    <div class="grid md:grid-cols-2 grid-cols-1 md:gap-4 gap-2 mt-2.5">
      <div class="">
        <label
          for="password"
          class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
          >Password</label
        >
        <div class="relative">
          <input
            id="password"
            name="password"
            :type="Password ? 'text' : 'password'"
            placeholder="Enter Password"
            v-model="userInfo.password"
            :class="{ 'tracking-widest': !Password }"
            class="block w-full rounded-md border mt-2 border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
          />
          <PasswordVisibility
            :showPassword="Password"
            @toggle="Password = !Password"
          />
        </div>
        <small
          id="password_message"
          class="error_message text-red-500 text-xs"
        ></small>
      </div>
      <div class="">
        <label
          for="password"
          class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
          >Confirm Password</label
        >
        <div class="relative">
          <input
            id="password_confirmation"
            name="password_confirmation"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Enter Confirm Password"
            v-model="userInfo.password_confirmation"
            :class="{ 'tracking-widest': !showPassword }"
            class="block w-full rounded-md border mt-2 border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
          />
          <PasswordVisibility
            :showPassword="showPassword"
            @toggle="showPassword = !showPassword"
          />
        </div>
        <small
          id="password_confirmation_message"
          class="error_message text-red-500 text-xs"
        ></small>
      </div>
    </div>
    <div class="grid md:grid-cols-2 grid-cols-1 md:gap-4 gap-2 mt-2.5">
      <div class="">
        <label
          for="credits"
          class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
          >Credits</label
        >
        <div class="flex">
          <div
            class="pointer-events-none flex items-center mt-2 pl-3 pr-2 py-2 bg-gray-50 text-gray-500 border border-neutral-300 rounded-l-md"
          >
            <span class="text-tiny">{{ siteSettings.currency_symbol }}</span>
          </div>
          <input
            v-model="userInfo.credits"
            type="number"
            name="credits"
            id="credits"
            class="border border-l-0 text-gray-900 text-sm mt-2 rounded-r-lg border-primary focus:border-primary focus:ring-transparent block w-full pr-2 py-2"
            placeholder="0"
          />
        </div>
        <small
          class="text-red-500 error_message text-xs"
          id="credits_message"
        ></small>
      </div>
      <div class="">
        <label
          for="country_name"
          class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
          >Country</label
        >
        <select
          id="country_name"
          v-model="userInfo.country_code"
          @change="updateCountryName()"
          placeholder="Select a Country"
          class="w-full rounded-md shadow-sm border-sm border-primary focus:border-primary py-1.5 mt-2 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
        >
          <option value="" disabled>Select a Country</option>
          <template v-for="country in countries" :key="country">
            <option :value="country.iso2">
              {{ country.country_name }}
            </option>
          </template>
        </select>
        <small
          id="country_name_message"
          class="error_message text-red-500 text-xs"
        ></small>
      </div>
    </div>
    <div class="grid md:grid-cols-2 grid-cols-1 md:gap-4 gap-2 mt-2.5">
      <div class="">
        <label
          for="region_name"
          class="text-tiny font-medium after:content-['*'] after:ml-0.5 after:text-red-500"
          >Region</label
        >
        <select
          id="region_code"
          name="region_code"
          @change="updateRegionName()"
          v-model="userInfo.region_code"
          placeholder="Select a Region"
          class="w-full rounded-md shadow-sm border-sm border-primary focus:border-primary py-1.5  text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
        >
          <option value="" disabled>Select a Region</option>
          <template v-for="(region, key) in getRegions" :key="key">
            <option :value="region.state_iso2">
              {{ region.state_name }}
            </option>
          </template>
        </select>
        <small
          class="text-red-500 error_message text-xs"
          id="region_name_message"
        ></small>
      </div>

      <div class="">
        <label
          for="timezone"
          class="text-tiny  after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
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
          <template #caret="{ toggle }">
            <div class="multiselect__select" @mousedown.prevent.stop="toggle">
              <span class="material-symbols-outlined mt-0.5 text-xl">
                keyboard_arrow_down
              </span>
            </div>
          </template>
        </multiselect>
        <small
          class="text-red-500 error_message text-xs"
          id="timezone_message"
        ></small>
      </div>
    </div>
    <div class="flex justify-end items-center pt-5 gap-4">
      <button @click="closeModal" type="button" class="rounded-md border font-medium px-4 py-2 text-center text-sm">
        Cancel
      </button>
      <Button :disabled="processing" @click="createUser">
        <i
          v-if="processing"
          class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
        ></i>
        {{ processing ? "Please wait" : "Create" }}
      </Button>
    </div>
  </Modal>
  <Confirmation
    :show="userConfirmation"
    :showLoader="showLoader"
    :confirmationTitle="title"
    :btnBgColor="action == 'active' ? 'bg-green-500' : 'bg-red-500'"
    :submitBtnTitle="`Yes, I'm Sure`"
    :disableButton="action !== 'active' && !reason" 
    @confirm="submit"
    @closeModal="closeModal"
    >
    <template #icon>
      <span
        v-if="action === 'banned'"
        class="material-symbols-outlined text-red-500 p-1.5 text-[20px]"
      >
        person_off
      </span>
      <span class="material-symbols-outlined text-green-500 text-2xl" v-else>
        how_to_reg
      </span>
    </template>
    <template #content>
      <span class="text-tiny text-gray-600">{{ message }}</span>
      <div class="my-3" v-if="action !== 'active'">
        <label
          class="font-medium text-tiny text-gray-800 after:content-['*'] after:ml-0.5 after:text-red-500"
          >Reason</label
        >
        <input
          v-model="reason"
          type="text"
          id="reason"
          placeholder="Enter Reason"
          class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 mt-2 focus:ring-0"
        />
        <small
          id="reason_message"
          class="error_message text-red-500 text-xs"
        ></small>
      </div>
      <p class="my-5 text-tiny font-medium" v-if="action !== 'active'">
        This action will prevent the user from accessing the platform.
      </p>
    </template>
  </Confirmation>
</template>

<script>
import Multiselect from "vue-multiselect";
import { useAuthStore } from "@/store/auth";
import { mapState } from "pinia";

export default {
  data() {
    return {
      breadcrumb: {
        icon: "groups",
        pages: [
          {
            name: "User Management",
          },
          {
            name: "Users",
          },
        ],
      },
      users: [],
      userConfirmation: false,
      showLoader: false,
      action: null,
      title: null,
      message: null,
      userId: null,
      reason: "",
      search: "",
      open: false,
      processing: false,
      per_page: 10,
      userInfo: {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        country_name: "",
        country_code: "",
        region_name: "",
        region_code: "",
        timezone: "",
      },
      showPassword: false,
      Password: false,
      userInfo: {
        password: "",
      },
      countries: [],
      timezones: [],
      timezone: null,
      thead: [
        {
          title: "ID",
          classes: " pl-10 font-medium",
        },
        {
          title: "Name",
          classes: "font-medium",
        },
        {
          title: "Credits",
          classes: "text-left font-medium",
        },
        {
          title: "Servers",
          classes: "font-medium",
        },
        {
          title: "Verified",
          classes: "text-center font-medium",
        },
        {
          title: "Status",
          classes: "font-medium pl-10",
        },
        {
          title: "Actions",
          classes: "text-center font-medium",
        },
      ],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      per_page: 10,
      refreshing: false,
      open: false,
    };
  },
  components: {
    Multiselect,
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
    this.handleSearch = this.$debounce(this.getUsers, 1000);
    this.fetchTimezones();
    this.getCountries();
    this.timezone = { name: "(UTC+00:00) Etc/UTC", value: "Etc/UTC" };
  },
  mounted() {
    this.getUsers();
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
    async getUsers(page = 1) {
      this.refreshing = true;
      let url = `/admin/users?page=${page}&per_page=${this.per_page}&search=${this.search}`;
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.users = data.users.data;
          this.pagination = {
            current_page: data.users.current_page,
            last_page: data.users.last_page,
            per_page: data.users.per_page,
            total: data.users.total,
            next_page_url: data.users.next_page_url,
            prev_page_url: data.users.prev_page_url,
          };
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.refreshing = false;
        });
    },

    handlePageChange(newPage) {
      this.getUsers(newPage);
    },
    handlePerPageChange() {
      this.getUsers(1);
    },

    openUserStatus(user) {
      this.userConfirmation = true;
      this.userId = user.id;
      if (user.status === "active") {
        this.title = "Ban User";
        this.action = "banned";
        this.reason = user.ban_lock_reason ? user.ban_lock_reason.value : "";
        this.message = `Are you sure you want to ban the user ${user.email}?`;
      } else {
        this.title = "Active User";
        this.action = "active";
        this.reason = user.ban_lock_reason ? user.ban_lock_reason.value : "";
        this.message = `Are you sure you want to Active the user ${user.email}?`;
      }
    },
    openModal() {
      this.open = true;
      this.userInfo = {
        ...this.userInfo,
        country_name: "",
        country_code: "",
        region_name: "",
        region_code: "",
      };
    },
    closeModal() {
      this.userConfirmation = false;
      this.showLoader = false;
      this.reason = "";
      this.open = false;
      this.processing = false;
      this.timezone = { name: "(UTC+00:00) Etc/UTC", value: "Etc/UTC" };
      this.userInfo = {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        country_name: "",
        country_code: "",
        region_name: "",
        region_code: "",
        timezone: "",
      };
    },
    async submit() {
      this.hideError();
      this.showLoader = true;
      await this.$axios
        .post(`/admin/users/${this.userId}/status-update/${this.action}`, {
          reason: this.reason,
        })
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.getUsers();
          this.closeModal();
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
    async createUser() {
      this.hideError();
      if (this.timezone) {
        this.userInfo.timezone = this.timezone.value;
      } else {
        this.userInfo.timezone = null;
      }
      if (this.processing) return;
      this.processing = true;
      await this.$axios
        .post(`/admin/users`, this.userInfo)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.closeModal();
          this.getUsers();
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
          this.processing = false;
        });
    },
    clearSearch() {
      this.search = "";
      this.handleSearch();
    },
  },
};
</script>


