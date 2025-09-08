<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <template v-if="user">
    <div class="bg-white shadow flex-1 rounded-md w-full p-5 xl:py-5">
      <div class="grid md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4 xs:grid-cols-2 gap-5 items-center grid-cols-1">
        <div class="flex items-center space-x-2">
          <div class="relative">
            <div
              :class="[
                isLightColor
                  ? 'bg-custom-200 text-custom-700'
                  : 'bg-custom-50 text-custom-500',
                ' rounded-md flex items-center justify-center',
              ]"
            >
              <span
                class="material-symbols-outlined p-1 font-medium text-[26px]"
              >
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
        <div class="">
          <label class="font-medium">Name</label>
          <div class="flex max-w-[280px]">
            <span
              class="font-medium text-gray-500 truncate text-tiny"
              v-tooltip="user.name"
              >{{ user.name }}</span
            >
          </div>
        </div>
        <div class="">
          <label class="font-medium">Email</label>

          <div class="flex max-w-[280px] items-center">
            <span
              class="font-medium text-gray-500 truncate text-tiny"
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
        </div>
        <div class="md:flex lg:justify-start xl:justify-end justify-end items-center gap-5">
          <button
            @click="deleteAccounts()"
            :disabled="user.is_admin"
            type="submit"
            class="disabled:pointer-events-none disabled:opacity-50 bg-red-600 gap-0.5 flex items-center text-white text-tiny rounded-md focus:outline-none focus:ring-0 px-2.5 py-2 text-center dark:focus:ring-0"
          >
            <span class="material-symbols-outlined text-[22px]"> delete </span>
            <p class="text-tiny">Delete Account</p>
          </button>
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
export default {
  data() {
    return {
      breadcrumb: null,
      openConfirmation: false,
      showLoader: false,
      user: null,
      breadcrumbUpdated: false,
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
