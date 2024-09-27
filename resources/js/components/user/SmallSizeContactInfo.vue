<template>
  <div class="h-full sticky top-0 bg-white ">
    <!-- Sidebar for Tablet and Mobile Screen -->
    <TransitionRoot as="template" :show="showData">
      <Dialog
        as="div"
        class="relative xl:hidden z-[1000]"
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
        <div class="fixed inset-0 justify-end flex">
          <TransitionChild
          as="template"
            enter="transition ease-in-out duration-300 transform"
            enter-from="translate-x-full"
            enter-to="translate-x-0"
            leave="transition ease-in-out duration-300 transform"
            leave-from="translate-x-0"
            leave-to="translate-x-full"
          >
            <DialogPanel class="relative flex justify-end">
              <TransitionChild
              as="template"
                enter="ease-in-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in-out duration-300"
                leave-from="opacity-100"
                leave-to="opacity-0"
              >
                <div class="absolute -top-3 flex justify-center pt-5">
                  <button
                    type="button"
                    class="-m-2.5 p-2.5 z-[1000px]"
                    @click="$emit('toggleMenu')"
                  >
                    <span class="sr-only">Close sidebar</span>
                    <span class="material-symbols-outlined"> close </span>
                  </button>
                </div>
              </TransitionChild>
              <div class="w-80 px-4 pt-10 bg-white">
                <ContactInfo></ContactInfo>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Sidebar for Desktop Screen -->
    <div class="w-80 hidden xl:block">
      <ContactInfo></ContactInfo>
    </div>
  </div>
</template>
  
  <script>
import ContactInfo from "./ContactInfo.vue";
import {
  TransitionChild,
  TransitionRoot,
  Dialog,
  DialogPanel,
} from "@headlessui/vue";
export default {
  components: {
    TransitionChild,
    TransitionRoot,
    Dialog,
    DialogPanel,
    ContactInfo,
  },
  data() {
    return {};
  },
  props: {
    showData: Boolean,
  },
};
</script>
  