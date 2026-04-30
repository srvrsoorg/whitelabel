<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <template v-if="user">
    <div class="bg-white shadow flex-1 rounded-md w-full p-5 xl:py-5">
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-center">
        <div class="xl:col-span-9 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-8 xl:pr-6">
          <div class="flex items-center gap-2">
            <div class="relative">
              <div
                :class="[
                  isLightColor
                    ? 'bg-custom-200 text-custom-700'
                    : 'bg-custom-50 text-custom-500',
                  'rounded-md flex items-center justify-center',
                ]"
              >
                <span class="material-symbols-outlined p-1 font-medium text-[26px]">
                  person
                </span>
              </div>
              <span
                :class="{
                  'absolute -right-1 -top-1 block h-2.5 w-2.5 rounded-full ring-2 ring-white': true,
                  'bg-green-600': user.status === 'active',
                  'bg-gray-600': user.status === 'pending',
                  'bg-red-500': user.status === 'banned',
                }"
              ></span>
            </div>
            <span
              :class="{
                'font-medium': true,
                'text-green-600': user.status === 'active',
                'text-gray-600': user.status === 'pending',
                'text-red-500': user.status === 'banned',
              }"
            >
              {{
                user.status === "active"
                  ? "Active"
                  : user.status === "pending"
                  ? "Pending"
                  : "Banned"
              }}
            </span>
          </div>

          <div class="min-w-0 sm:max-w-[220px]">
            <label class="font-medium">Name</label>
            <div class="flex min-w-0">
              <span
                class="font-medium text-gray-500 truncate text-tiny"
                v-tooltip="user.name"
                >{{ user.name }}</span
              >
            </div>
          </div>

          <div class="min-w-0 sm:max-w-[280px]">
            <label class="font-medium">Email</label>
            <div class="flex items-center min-w-0">
              <span
                class="font-medium text-gray-500 truncate text-tiny"
                v-tooltip="user.email"
                >{{ user.email }}</span
              >
              <span
                @click="copyToClipboard(user.email)"
                class="material-symbols-outlined text-[16px] text-blue-500 cursor-pointer ml-2 shrink-0"
              >
                content_copy
              </span>
            </div>
          </div>
        </div>

        <div class="xl:col-span-3 flex flex-wrap items-center gap-2 justify-start xl:justify-end xl:pl-4 min-w-0">
          <div
            v-if="!isOwnAccount"
            class="inline-block"
            :class="{ 'cursor-not-allowed': isLoginDisabled }"
            v-tooltip="loginDisabledTooltip"
          >
            <button
              @click="switchAccount()"
              :disabled="isLoginDisabled || switchingAccount"
              type="button"
              class="disabled:pointer-events-none disabled:opacity-50 bg-custom-600 gap-1.5 flex items-center text-white text-tiny rounded-md focus:outline-none focus:ring-0 px-2.5 py-2 text-center dark:focus:ring-0"
            >
              <span class="material-symbols-outlined text-[20px]"> switch_account </span>
              <p class="text-tiny whitespace-nowrap">{{ switchingAccount ? "Logging in..." : "Login" }}</p>
            </button>
          </div>
          <div
            class="inline-block"
            :class="{ 'cursor-not-allowed': isDeleteDisabled }"
            v-tooltip="deleteDisabledTooltip"
          >
            <button
              @click="deleteAccounts()"
              :disabled="isDeleteDisabled"
              type="submit"
              class="disabled:pointer-events-none disabled:opacity-50 bg-red-600 gap-0.5 flex items-center text-white text-tiny rounded-md focus:outline-none focus:ring-0 px-2.5 py-2 text-center dark:focus:ring-0"
            >
              <span class="material-symbols-outlined text-[22px]"> delete </span>
              <p class="text-tiny whitespace-nowrap">Delete Account</p>
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  <router-view
    :userData="user"
    @refresh-user="fetchUser()"
    @pass-breadcrumb="handleObj"
  >
  </router-view>
  <Confirmation
    @closeModal="openConfirmation = false"
    :show="openConfirmation"
    :showLoader="showLoader"
    :confirmationTitle="'Delete Account'"
    :submitBtnTitle="`Yes I'm sure`"
    @confirm="deleteAccount"
  >
    <template #icon>
      <span
        class="material-symbols-outlined text-red-500 font-medium text-[22px]"
      >
        person
      </span>
    </template>
    <template v-slot:content
      ><span class="text-tiny text-gray-600">{{
        `Are you sure you want to delete the account ${user && user.email}?`
      }}</span>
      <p class="my-5 font-medium text-tiny">
        {{
          `This action is permanent and it will remove all data associated with the
        account ${user && user.email} and you will not be able to recover it
        again.`
        }}
      </p>
    </template>
  </Confirmation>
</template>

<script>
import { useAuthStore } from "@/store/auth";

export default {
  setup() {
    const authStore = useAuthStore();
    return { authStore };
  },
  data() {
    return {
      breadcrumb: null,
      openConfirmation: false,
      showLoader: false,
      user: null,
      breadcrumbUpdated: false,
      switchingAccount: false,
    };
  },
  created() {
    this.fetchUser();
  },
  watch: {
    user(newUser) {
      if (newUser && this.breadcrumb && !this.breadcrumbUpdated) {
        this.breadcrumb.pages.unshift({ name: "User", path: { name: "adminUsers" } }, { name: newUser.name });
        this.breadcrumbUpdated = true; // Set flag to true after updating breadcrumb
      }
    },
  },
  computed: {
    isOwnAccount() {
      return this.user && this.authStore.user && this.user.id === this.authStore.user.id;
    },
    isLoginDisabled() {
      if (!this.user) return true;
      return this.user.is_admin || ["banned", "locked"].includes(this.user.status);
    },
    loginDisabledTooltip() {
      if (!this.user) return "";
      if (this.user.is_admin) return "You cannot login to another admin account.";
      if (this.user.status === "banned") return "You cannot login to a banned user account.";
      if (this.user.status === "locked") return "You cannot login to a locked user account.";
      return "";
    },
    isDeleteDisabled() {
      if (!this.user) return true;
      return this.user.is_admin;
    },
    deleteDisabledTooltip() {
      if (!this.user) return "";
      if (!this.isDeleteDisabled) return "";
      if (this.isOwnAccount) return "You cannot delete your own account.";
      return "You cannot delete an admin account.";
    },
  },
  methods: {
    handleObj(data) {
      this.breadcrumb = data;
      if (this.user) {
        this.breadcrumb.pages.unshift({ name: "User", path: { name: "adminUsers" } }, { name: this.user.name });
        this.breadcrumbUpdated = true; // Set flag to true
      }
    },
    async fetchUser() {
      await this.$axios
        .get(`/admin/users/${this.$route.params.user}`)
        .then(({ data }) => {
          this.user = data.user;
        })
        .catch(({ response: data }) => {
          this.$toast.error(data.message);
        });
    },
    async switchAccount() {
      this.switchingAccount = true;

      await this.$axios
        .post(`/admin/users/${this.$route.params.user}/switch-account`)
        .then(({ data }) => {
          this.authStore.applySession({ token: data.token, user: data.user });
          this.$toast.success(data.message);
          this.$router.push({ name: "dashboard" });
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.switchingAccount = false;
        });
    },
    deleteAccounts() {
      this.openConfirmation = true;
    },
    async deleteAccount() {
      this.showLoader = true;
      const id = this.$route.params.user;
      await this.$axios
        .delete(`/admin/users/${id} `)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.$router.push({ name: "adminUsers" });
          this.openConfirmation = false;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
          this.openConfirmation = false;
        })
        .finally(() => {
          this.showLoader = false;
        });
    },
  },
};
</script>
