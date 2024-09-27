<template>
  <div class="relative flex min-h-screen h-full flex-col bg-[#FDFDFD]">
    <VerifyEmailNotice
      v-if="user && user.email_verified_at === null"
    ></VerifyEmailNotice>
    <!-- Mobile Menu Button -->
    <div
      class="bg-white border-gray-100 shadow-sm border-b flex-shrink-0 h-16 flex justify-between items-center gap-p fixed top-0 inset-x-0 z-[999]"
      :class="[
        user && user.email_verified_at === null ? 'top-16 sm:top-10' : '',
      ]"
    >
      <div class="flex gap-5 items-center">
        <div class="pl-5 visible lg:hidden">
          <button @click="toggleMenu()" class="w-fit flex items-center">
            <span class="material-symbols-outlined text-3xl text-gray-800">
              menu
            </span>
          </button>
        </div>
        <div class="px-10 sm:block hidden">
          <router-link to="/">
            <img
              v-if="!logo"
              class="h-8 w-auto"
              src="/logo/whitelabel-logo.png"
              :alt="app_name"
            />
            <img v-else class="h-7 w-auto" :src="logo" :alt="app_name" />
          </router-link>
        </div>
      </div>
      <div class="xl:px-10 px-6 flex justify-center items-center gap-5">
        <router-link
          :to="{
            name: 'wallet',
          }"
          :class="[' text-xs flex gap-2 items-center py-1.5 rounded-lg']"
          v-if="user"
        >
          <div
            :class="[isLightColor ? 'bg-custom-700' : 'bg-custom-500']"
            class="border-4 border-custom-50 p-0.5 rounded-full flex justify-center items-center"
          >
            <span
              class="material-symbols-outlined text-[23px] flex justify-center items-center text-white font-medium"
            >
              add
            </span>
          </div>

          <div class="flex flex-col text-md font-medium gap-0.5">
            <span class="text-[14px] font-semibold">Credits</span>
            <span
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                ' font-semibold text-[14px]',
              ]"
            >
              {{ formatCurrency(user.credits) }}
            </span>
          </div>
        </router-link>
      </div>
    </div>
    <div class="flex flex-col flex-1 h-full">
      <!-- Sidebar -->
      <div
        :class="[
          isOpen ? 'visible translate-x-0' : 'invisible -translate-x-full',
          user && user.email_verified_at === null ? 'top-24' : 'top-16',
        ]"
        class="fixed inset-y-0 left-0 flex z-50 transition duration-200 ease-in-out lg:visible lg:left-auto lg:mt-0 lg:translate-x-0 transform"
      >
        <Sidenav :sidebarOpen="isOpen" @toggleMenu="toggleMenu()"></Sidenav>
      </div>

      <!-- Main wrapper -->
      <div
        class="w-full h-full flex flex-col flex-1 mt-16"
        :class="[
          showNarrowSidebar ? 'lg:pl-80' : 'lg:pl-64',
          user && user.email_verified_at === null ? 'sm:mt-24 mt-32' : '',
        ]"
      >
        <div class="w-full h-full flex-1 xl:px-8 lg:px-6 px-6 py-5">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/store/auth";
import Sidenav from "@/components/user/Sidenav.vue";
import VerifyEmailNotice from "@/components/VerifyEmailNotice.vue";

export default {
  name: "User",
  setup() {
    const authStore = useAuthStore();
    return { authStore };
  },
  components: {
    Sidenav,
    VerifyEmailNotice,
  },
  data() {
    return {
      isOpen: false,
      showNarrowSidebar: false,
    };
  },
  methods: {
    toggleMenu() {
      this.isOpen = !this.isOpen;
    },

    // Reset mobile menu visibility based on window size
    initializeSidebar() {
      this.isOpen = window.screen.width > 1023;
    },
  },
  computed: {
    user() {
      return this.authStore.user;
    },
  },
  watch: {
    $route(val) {
      if (window.screen.width <= 1023 && this.isOpen) {
        this.toggleMenu();
      }
    },
  },
  created() {
    this.initializeSidebar();
    window.addEventListener("resize", this.initializeSidebar);
      false;
  },
  onBeforeUnmount() {
    window.removeEventListener("resize", this.initializeSidebar);
  },
};
</script>


