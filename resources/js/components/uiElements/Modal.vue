<template>
  <TransitionRoot
    :show="openModal"
    as="template"
    enter="duration-300 ease-out"
    enter-from="opacity-0"
    enter-to="opacity-100"
    leave="duration-200 ease-in"
    leave-from="opacity-100"
    leave-to="opacity-0"
  >
    <Dialog as="div" class="relative z-[99999]" @close="$emit('closeModal')">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div
          class="fixed inset-0 backdrop-blur-sm bg-gray-500 bg-opacity-30 transition-opacity"
        />
      </TransitionChild>

      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div
          class="flex min-h-full justify-center p-4 text-center items-center"
        >
          <TransitionChild
            class="modal"
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel
              :class="[
                'relative transform  px-4 py-2 rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-md',
                ...customClass,
              ]"
            >
              <DialogTitle
                :class="removeBorder"
                class="flex items-start justify-between py-3 border-b rounded-t"
              >
                <div class="flex items-center gap-2.5">
                  <span
                    v-if="modelIcon"
                    :class="[
                      isLightColor
                        ? 'bg-custom-200 text-custom-700'
                        : 'bg-custom-50 text-custom-500',
                    ]"
                    class="material-symbols-outlined flex rounded-md text-[20px] px-1.5 py-1.5"
                  >
                    {{ modelIcon }}
                  </span>
                  <div class="flex items-center">
                  <h2
                    v-if="modalTitle"
                    class="font-medium text-gray-900 text-base"
                  >
                    {{ modalTitle }}
                  </h2>
                  <slot name="titleExtra"></slot>
                  </div>
                  <slot name="titleDescription"> </slot>
                </div>
                <button
                  type="button"
                  @click="$emit('closeModal')"
                  class="text-gray-900 bg-transparent absolute right-5 top-5 z-500 hover:bg-gray-100 hover:text-gray-700 bg-white rounded-lg text-sm w-7 h-7 ml-auto inline-flex justify-center items-center"
                  data-modal-hide="defaultModal"
                >
                  <i class="fas fa-times text-lg"></i>
                  <span class="sr-only">Close modal</span>
                </button>
              </DialogTitle>
              <!-- <hr class="mx-5"> -->

              <div class="px-2 pt-3 pb-5">
                <slot />
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { timers } from "jquery";
export default {
  props: {
    customClass: {
      type: Array,
      default: [],
    },
    openModal: {
      type: Boolean,
      default: false,
    },
    modalTitle: {
      type: String,
      default: "",
    },
    modelIcon: {
      type: String,
      default: "",
    },
    titleClass: {
      type: Array,
      default: "",
    },
    removeBorder: {
      type: String,
    },
  },
  components: {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
  },
};
</script>