<template>
  <div class="no-scrollbar sticky top-0 bg-custom-500">
    <PerfectScrollbar class="h-full">
      <div class="flex-1 h-full flex flex-col bg-custom-500 py-7">
        <nav
          class="flex flex-col 2xl:gap-y-60 gap-y-24 flex-shrink-0 h-full w-20"
        >
          <div class="relative flex flex-col space-y-3 px-4">
            <template v-for="link in menuList" :key="link.id">
              <router-link
                :to="link.url"
                v-tooltip.right="`${link.name}`"
                :class="[
                  textColorClass,
                  sidebarHoverLinks,
                  'hover:bg-custom-100 group w-full flex items-center justify-center px-2 py-3 text-tiny font-medium rounded-md',
                ]"
              >
                <span class="material-symbols-outlined text-[22px]">
                  {{ link.icon }}
                </span>
              </router-link>
            </template>
          </div>

          <div :class="['px-4 pt-5']">
            <div
              :class="[
                'flex items-center mb-5',
                isLightColor
                  ? 'border-t border-gray-700'
                  : 'border-t-2 border-white',
              ]"
              v-if="user"
            ></div>
            <router-link
              v-if="user"
              v-tooltip.right="user && user.name"
              :to="{
                name: 'dashboard',
              }"
              :class="[
                'w-full py-3 flex items-center',
                isLightColor
                  ? 'hover:text-custom-700'
                  : 'hover:text-custom-500',
              ]"
            >
              <img
                :src="user.avatar"
                :alt="app_name"
                class="h-7 rounded-full mx-auto"
              />
            </router-link>

            <button
              v-tooltip.right="'Logout'"
              @click="logout"
              exact-active-class="text-gray-500"
              :class="[
                'w-full py-5 flex items-center justify-center text-tiny',
                isLightColor ? 'text-gray-800' : 'text-white',
              ]"
            >
              <span class="material-symbols-outlined text-[22px] ml-1">
                logout
              </span>
            </button>
          </div>
        </nav>
      </div>
    </PerfectScrollbar>
  </div>
</template>

<script>
import { useAuthStore } from "@/store/auth";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import sideMenu from "@/adminSideMenu/sidebarList";
export default {
  name: "NarrowSidebar",
  setup() {
    const authStore = useAuthStore();
    return { authStore };
  },
  components: {
    DisclosureButton,
    Disclosure,
    DisclosurePanel,
  },
  data() {
    return {
      menuList: [],
    };
  },
  created() {
    this.menuList = sideMenu;
  },
  computed: {
    user() {
      return this.authStore.user;
    },
  },
  methods: {
    async logout() {
      await this.$axios
        .get("/user/logout")
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.authStore.authLogout();
          this.$router.push("/login");
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
  },
};
</script>
