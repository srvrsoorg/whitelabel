<template>
  <div class="relative flex min-h-screen h-full flex-col bg-[#FDFDFD]">
    <!-- Mobile Menu Button -->
    <div
      class="bg-white border-gray-100 border-b shadow-sm flex-shrink-0 h-16 flex justify-between items-center gap-p fixed top-0 inset-x-0 z-[999]"
    >
      <div class="flex items-center gap-5">
        <div class="pl-5 visible lg:hidden">
          <button @click="toggleMenu()" class="w-fit flex items-center">
            <span class="material-symbols-outlined text-3xl">
              menu
            </span>
          </button>
        </div>
        <div class="lg:px-10 sm:block hidden">
          <router-link to="/admin">
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
    </div>
    <div class="flex flex-1 h-full">
      <!-- Sidebar -->
      <div
        :class="
          isOpen ? 'visible translate-x-0' : 'invisible -translate-x-full'
        "
        class="fixed inset-y-0 left-0 top-16 flex z-50 transition duration-200 ease-in-out lg:visible lg:left-auto lg:mt-0 lg:translate-x-0 transform"
      >
        <Sidenav :sidebarOpen="isOpen" @toggleMenu="toggleMenu()"></Sidenav>
      </div>

      <!-- Main wrapper -->
      <div
        class=" w-full h-full flex flex-col flex-1"
        :class="showNarrowSidebar ? 'lg:pl-80' : 'lg:pl-64'"
      >
        <div class="w-full lg:px-8 p-5 mt-16 h-full flex-1">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { useAuthStore } from "@/store/auth";
import { mapActions, mapState } from "pinia";
import Sidenav from "@/components/admin/Sidenav.vue";
import NarrowSidebar from "@/components/admin/NarrowSidebar.vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";

export default {
  name: "User",
  setup() {
    const authStore = useAuthStore();
    return { authStore };
  },
  components: {
    Sidenav,
    NarrowSidebar,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
  },
  data() {
    return {
      isOpen: false,
      showNarrowSidebar: false,
      profile: [
        {
          name: "User",
          path: "/",
          icon: "las la-user"
        },
      ],
    };
  },
  computed: {
    user() {
      return this.authStore.user;
    },
  },
  methods: {
    async logout() {
    },

    toggleMenu() {
      this.isOpen = !this.isOpen;
    },

    // Reset mobile menu visibility based on window size
    initializeSidebar() {
      this.isOpen = window.screen.width > 1023;
    },
  },

  watch: {
    $route(val) {
      this.showNarrowSidebar = (val.meta.isAdminAccount || false) || (val.meta.isAdminServer || false);
      if (window.screen.width <= 1023 && this.isOpen) {
        this.toggleMenu();
      }
    },
  },
  created() {
    this.initializeSidebar();
    window.addEventListener("resize", this.initializeSidebar);
    this.showNarrowSidebar = (this.$route.meta.isAdminAccount || false) || (this.$route.meta.isAdminServer || false) ;
  },
  onBeforeUnmount() {
    window.removeEventListener("resize", this.initializeSidebar);
  },
};
</script>
