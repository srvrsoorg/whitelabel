<template>
  <div
    class="no-scrollbar h-full sticky top-0 bg-white shadow"
    :class="showNarrowSidebar ? 'w-80' : 'w-64'"
  >
    <!-- Sidebar for Tablet and Mobile Screen -->
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
                    <span class="material-symbols-outlined text-white"> close </span>
                  </button>
                </div>
              </TransitionChild>
              <div class="flex flex-grow bg-white h-full">
                <!-- <NarrowSidebar v-if="showNarrowSidebar" /> -->
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

    <!-- Sidebar for Desktop Screen -->
    <div class="flex flex-grow h-full min-h-full">
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
import sideMenu from "@/sideMenu/sidebarList";
export default {
  name: "Sidenav",
  props: ["sidebarOpen"],
  components: {
    TransitionChild,
    TransitionRoot,
    Dialog,
    DialogPanel,
    SidebarItems,
  },
  data() {
    return {
      menuList: [],
      showNarrowSidebar: false,
      enabledPaymentProviders: [],
    };
  },
  watch: {
    $route() {
      this.changeMenuLinks();
    },
  },
  created() {
    this.fetchEnabledPaymentProviders();
  },
  methods: {
    fetchEnabledPaymentProviders() {
      this.$axios
        .get("/enable-providers")
        .then(({ data }) => {
          this.enabledPaymentProviders = data.payment || [];
          this.changeMenuLinks();
        })
        .catch(() => {
          this.enabledPaymentProviders = [];
          this.changeMenuLinks();
        });
    },
    changeMenuLinks() {
      const links = JSON.parse(JSON.stringify(sideMenu));
      const isStripeEnabled = this.enabledPaymentProviders.includes("Stripe");

      this.menuList = links.map((item) => {
        if (item.name === "Billing" && Array.isArray(item.children) && !isStripeEnabled) {
          item.children = item.children.filter((child) => child.url !== "/billing/auto-recharge");
        }
        return item;
      });

      this.showNarrowSidebar = false;
    },
  },
};
</script>
