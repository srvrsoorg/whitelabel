<template>
  <div
    class="no-scrollbar h-full sticky top-0 bg-white shadow"
    :class="showNarrowSidebar ? 'w-80' : 'w-64'"
  >
    <TransitionRoot as="template" :show="sidebarOpen">
      <Dialog
        as="div"
        class="relative lg:hidden z-[1000]"
        :open="sidebarOpen"
        @close="$emit('toggleMenu')"
      >
        <TransitionChild
          as="template"
          enter="transition-opacity ease-linear duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="transition-opacity ease-linear duration-300"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-gray-900/40" />
        </TransitionChild>
        <div class="fixed inset-0 flex">
          <TransitionChild
            as="template"
            enter="transition ease-in-out duration-300 transform"
            enter-from="-translate-x-full"
            enter-to="translate-x-0"
            leave="transition ease-in-out duration-300 transform"
            leave-from="translate-x-0"
            leave-to="-translate-x-full"
          >
            <DialogPanel
              class="relative mr-16 flex flex-1"
              :class="
                showNarrowSidebar ? 'w-80 max-w-[320px]' : 'w-64 max-w-[256px]'
              "
            >
              <TransitionChild
                as="template"
                enter="ease-in-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in-out duration-300"
                leave-from="opacity-100"
                leave-to="opacity-0"
              >
                <div
                  class="absolute left-full top-0 flex w-16 justify-center pt-5"
                >
                  <button
                    type="button"
                    class="-m-2.5 p-2.5 z-[1000px]"
                    @click="$emit('toggleMenu')"
                  >
                    <span class="sr-only">Close sidebar</span>
                    <span class="material-symbols-outlined text-white "> close </span>
                  </button>
                </div>
              </TransitionChild>
              <div class="flex flex-grow h-full">
                <NarrowSidebar v-if="showNarrowSidebar" />
                <SidebarItems
                  :menuList="menuList"
                  :showNarrowSidebar="showNarrowSidebar"
                />
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </TransitionRoot>

    <div class="flex flex-grow h-full min-h-full">
      <NarrowSidebar v-if="showNarrowSidebar" />
      <SidebarItems
        :menuList="menuList"
        :showNarrowSidebar="showNarrowSidebar"
      />
    </div>
  </div>
</template>
<script>
import {
  TransitionChild,
  TransitionRoot,
  Dialog,
  DialogPanel,
} from "@headlessui/vue";
import SidebarItems from "./SidebarItems.vue";
import sideMenu from "@/adminSideMenu/sidebarList";
import userMenu from "@/adminSideMenu/userMenu";
import serverMenu from "@/adminSideMenu/servermenu";
import NarrowSidebar from "./NarrowSidebar.vue";

export default {
  name: "Sidenav",
  props: ["sidebarOpen"],
  components: {
    TransitionChild,
    TransitionRoot,
    Dialog,
    DialogPanel,
    SidebarItems,
    NarrowSidebar,
  },
  data() {
    return {
      menuList: [],
      showNarrowSidebar: false,
      dropDownList: [],
    };
  },
  watch: {
    $route() {
      this.changeMenuLinks();
    },
  },
  created() {
    this.menuList = sideMenu;
    this.changeMenuLinks();
  },

  methods: {
    changeMenuLinks() {
      if (this.$route.meta.isAdminServer) {
        let serverLinks = serverMenu.map((link) => ({
          ...link,
          url: link.url.replace("{id}", this.$route.params.server),
        }));
        this.menuList = serverLinks;
        this.showNarrowSidebar = true;
      } else if (this.$route.meta.isAdminAccount) {
        let userLinks = userMenu.map((link) => ({
          ...link,
          url: link.url.replace("{id}", this.$route.params.user),
        }));
        this.menuList = userLinks;
        this.showNarrowSidebar = true;
      } else {
        this.menuList = sideMenu;
        this.showNarrowSidebar = false;
      }
    },
  },
};
</script>
