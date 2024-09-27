<template>
  <div class="no-scrollbar sticky top-0 bg-custom-500">
    <PerfectScrollbar class="h-full">
      <div class="flex-1 h-full g flex flex-col bg-custom-500 py-7">
        <nav
          class="flex flex-col 2xl:gap-y-60 gap-y-24 flex-shrink-0 h-full w-20"
        >
          <div class="relative flex flex-col space-y-3 px-4">
            <template v-for="link in menuList" :key="link.id">
              <router-link
                :to="link.url"
                v-tooltip.right="`${link.name}`"
                :exact-active-class="'bg-white !text-custom-500 '"
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
          <div class="relative flex w-20 flex-col space-y-6 px-4 py-5">
            <hr
              :class="[
                isLightColor ? 'bg-gray-800' : 'bg-gray-100',
                '!mt-5 border-0 h-px',
              ]"
            />
            <Disclosure>
              <DisclosureButton class="flex justify-center" v-if="user">
                <img
                  v-tooltip.right="user && user.name"
                  :src="user.avatar"
                  :alt="app_name"
                  class="h-7 rounded-full"
                />
              </DisclosureButton>
              <DisclosurePanel class="space-y-3">
                <DisclosureButton
                  v-if="is_admin"
                  as="a"
                  class="group flex w-full justify-center items-center rounded-md text-tiny font-medium text-gray-600"
                >
                  <router-link
                    v-tooltip.right="'Admin'"
                    :to="{
                      name: 'adminDashboard',
                    }"
                    :class="[
                      textColorClass,
                      sidebarHoverLinks,
                      'hover:bg-custom-100 group w-full flex items-center justify-center px-2 py-3 text-tiny font-medium rounded-md',
                    ]"
                  >
                    <span class="material-symbols-outlined text-[22px]">
                      person_4
                    </span>
                  </router-link>
                </DisclosureButton>
                <DisclosureButton
                  as="a"
                  class="group flex w-full justify-center items-center rounded-md text-tiny font-medium text-gray-600"
                >
                  <button
                    @click="logout"
                    v-tooltip.right="'Logout'"
                    :class="[
                      textColorClass,
                      sidebarHoverLinks,
                      'hover:bg-custom-100 group w-full flex items-center justify-center px-2 py-3 text-tiny font-medium rounded-md',
                    ]"
                  >
                    <span class="material-symbols-outlined text-[22px]">
                      logout
                    </span>
                  </button>
                </DisclosureButton>
              </DisclosurePanel>
            </Disclosure>
          </div>
        </nav>
      </div>
    </PerfectScrollbar>
  </div>
</template>

<script>
import { useAuthStore } from "@/store/auth";
import { mapState, mapActions } from "pinia";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import sideMenu from "@/sideMenu/sidebarList";
export default {
  name: "NarrowSidebar",
  components: {
    DisclosureButton,
    Disclosure,
    DisclosurePanel,
  },
  data() {
    return {
      profile: [
        {
          name: "Account",
          icon: "account_circle",
          url: "/account",
        },
        {
          name: "Billing",
          icon: "lab_profile",
          url: "/billing/wallet",
        },
      ],
      menuList: [],
    };
  },
  created() {
    this.menuList = sideMenu;
  },
  computed: {
    ...mapState(useAuthStore, ["user", "is_admin"]),
  },
  methods: {
    ...mapActions(useAuthStore, ["authLogout"]),
    async logout() {
      await this.$axios
        .get("/user/logout")
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.authLogout();
          this.$router.push("/login");
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
  },
};
</script>
