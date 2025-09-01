<template>
  <PerfectScrollbar class="h-full min-h-full flex-1 overscroll-x-none">
    <div
      class="h-full min-h-full flex flex-col bg-white shadow-sm border-gray-100 border-r py-5"
    >
      <PerfectScrollbar
        class="flex-1 overflow-y-auto overscroll-x-none h-full"
        aria-label="Sidebar"
      >
        <template v-if="menuList.length">
          <div>
            <span
              class="flex items-center gap-2 pl-7 pt-5 pb-2 cursor-pointer"
              v-if="backButtonText"
            >
              <router-link
                :to="backButtonText.path"
                class="items-center flex gap-2 text-tiny font-medium"
              >
                <span class="material-symbols-outlined text-[20px]">
                  west
                </span>

                {{ backButtonText.text }}</router-link
              >
            </span>
          </div>

          <template v-for="link in menuList" :key="link.id">
            <template v-if="!link.children || !link.children.length">
              <router-link
                @click="toggleDropdown(link.id)"
                :to="link.url"
                :exact-active-class="[
                  showNarrowSidebar
                    ? isLightColor
                      ? ' !text-custom-700 border-custom-700 '
                      : ' !text-custom-500 border-custom-500 '
                    : isLightColor
                    ? ' !text-custom-700 !pr-5 [&_div>p]:bg-custom-700'
                    : ' !text-custom-500  !pr-5 [&_div>p]:bg-custom-500',
                ]"
                :data-id="link.id"
                v-bind="$attrs"
                :class="[
                  isLightColor
                    ? 'hover:text-custom-700 '
                    : 'hover:text-custom-500 ',
                  'text-[#31363F] group w-full flex  justify-between items-center pr-6  text-tiny font-medium my-2',
                ]"
              >
                <div class="flex items-center gap-6">
                  <p class="w-[5px] h-11 rounded-r-full"></p>
                  <div class="flex items-center">
                    <span class="material-symbols-outlined text-[22px] mr-3">
                      {{ link.icon }}
                    </span>
                    {{ link.name }}
                  </div>
                </div>
              </router-link>
            </template>
            <template v-else>
              <Disclosure
                as="div"
                class="space-y-1"
                v-slot="{ open }"
                v-bind="{ ...openDefaultMenu(link) }"
              >
                <DisclosureButton
                  :class="[
                    isLightColor
                      ? 'hover:text-custom-700 '
                      : 'hover:text-custom-500 ',
                    'text-[#292d33] group w-full flex gap-6 my-2 items-center pr-6 text-tiny font-medium ',
                    open &&
                      (showNarrowSidebar
                        ? isLightColor
                          ? ' !text-custom-700 border-custom-700 !px-6'
                          : ' !text-custom-500 border-custom-500 !px-6'
                        : isLightColor
                        ? ' !text-custom-700 [&_div>icon]:text-custom-700   [&_p]:bg-custom-700'
                        : ' !text-custom-500  [&_div>icon]:text-custom-500 [&_p]:bg-custom-500'),
                  ]"
                  :data-id="link.id"
                  v-bind="$attrs"
                >
                  <p class="w-[5px] h-11 rounded-r-full"></p>
                  <div class="flex justify-between w-full items-center">
                    <div class="flex items-center">
                      <span class="material-symbols-outlined text-[22px] mr-3">
                        {{ link.icon }}
                      </span>
                      <span class="flex-1 ml-0.5 text-left">{{
                        link.name
                      }}</span>
                    </div>
                    <span
                      :class="[
                        open ? '] rotate-90' : '',
                        'material-symbols-outlined icon ',
                      ]"
                    >
                      chevron_forward
                    </span>
                  </div>
                </DisclosureButton>
                <DisclosurePanel>
                  <template
                    v-for="subItem in link.children"
                    :key="subItem.name"
                  >
                    <DisclosureButton
                      v-if="subItem.url"
                      as="a"
                      class="group flex w-full items-center rounded-md pl-9 pr-2 text-tiny text-[#31363F]"
                    >
                      <li
                        class="py-2 border-l list-none border-[#CBD5E0] w-full pl-6"
                      >
                        <router-link
                          @click="toggleDropdown(link.id)"
                          :exact-active-class="[
                            isLightColor
                              ? 'text-custom-700'
                              : 'text-custom-500',
                          ]"
                          :class="[
                            'w-full font-medium rounded-md flex  items-center gap-x-5',
                            isLightColor
                              ? 'hover:text-custom-700'
                              : 'hover:text-custom-500',
                          ]"
                          :to="subItem.url"
                        >
                          <i class="fa-sharp fa-solid fa-circle text-[7px]"></i>
                          {{ subItem.name }}
                        </router-link>
                      </li>
                    </DisclosureButton>
                  </template>
                </DisclosurePanel>
              </Disclosure>
            </template>
          </template>
        </template>
        <template v-else>
          <li
            class="list-none p-1"
            v-for="(index, key) in new Array(15)"
            :key="key"
          >
            <Skeleton :count="1" />
          </li>
        </template>
      </PerfectScrollbar>
      <nav class="space-y-3" v-if="!showNarrowSidebar">
        <hr class="text-[#CBD5E0] mx-5 my-5" />
        <div
          :class="[
            'text-[#31363F] group w-64 max-w-64 flex justify-between items-center py-3 px-6  text-tiny font-medium',
          ]"
        >
          <div class="flex items-center max-w-64 gap-3 w-full" v-if="user">
            <img :src="user.avatar" :alt="app_name" class="h-7 rounded-full" />
            <div class="w-40 max-w-40">
              <span class="truncate block" v-tooltip="user.name">{{
                user.name
              }}</span>
              <span
                class="text-sm text-gray-500 block truncate"
                v-tooltip="user.email"
                >{{ user.email }}</span
              >
            </div>
          </div>
        </div>
        <router-link
          :to="{
            name: 'dashboard',
          }"
          :exact-active-class="[
            showNarrowSidebar
              ? isLightColor
                ? ' !text-custom-700 border-r-4 border-custom-700 !px-6'
                : ' !text-custom-500 border-r-4 border-custom-500 !px-6'
              : isLightColor
              ? ' !text-custom-700 !pr-5 [&_div>p]:bg-custom-700'
              : ' !text-custom-500  !pr-5 [&_div>p]:bg-custom-500',
          ]"
          class="text-[#31363F] group w-full flex justify-between items-center pb-3 px-6 text-tiny font-medium"
        >
          <div class="flex items-center gap-3" v-if="user">
            <span
              :class="[
                'material-symbols-outlined text-[24px]',
                isLightColor ? 'text-custom-700' : 'text-custom-500',
              ]"
            >
              step_over
            </span>
            Switch to User
          </div>
        </router-link>
        <button
          @click="logout"
          class="w-full flex items-center gap-3 text-tiny font-medium px-7 text-[#31363F]"
        >
          <span class="material-symbols-outlined text-[22px] text-rose-600">
            logout
          </span>
          Logout
        </button>
      </nav>
    </div>
  </PerfectScrollbar>
</template>
<script>
import { useAuthStore } from "@/store/auth";
import { mapState, mapActions } from "pinia";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";

export default {
  name: "SidebarItems",
  props: ["menuList", "showNarrowSidebar"],
  components: {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
  },
  data() {
    return {};
  },
  setup() {
    const authStore = useAuthStore();
    return { authStore };
  },

  computed: {
    user() {
      return this.authStore.user;
    },
    backButtonText() {
      if (this.$route.meta.isAdminServer) {
        return { text: "Back to Servers", path: "/admin/servers" };
      } else if (this.$route.meta.isAdminAccount) {
        return { text: "Back to Users", path: "/admin/users" };
      }
      return null;
    },
  },
  methods: {
    toggleDropdown(id) {
      const elements = document.querySelectorAll("[data-id]");
      elements.forEach((elm) => {
        const isExpanded = elm.getAttribute("aria-expanded") === "true";
        if (elm.getAttribute("data-id") != id && isExpanded) {
          elm.click();
        }
      });
    },
    openDefaultMenu(row) {
      let allLinks = [row.url];

      if (row.children) {
        const childLinks = row.children.map((row) => row.url);
        allLinks = [...childLinks, ...allLinks];
      }

      if (allLinks.includes(this.$route.path)) {
        return {
          defaultOpen: true,
        };
      } else {
        return {
          defaultOpen: false,
        };
      }
    },
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
