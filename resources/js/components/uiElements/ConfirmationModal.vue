<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="relative z-[9999999]" @close="closeModal">
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
        <div class="flex min-h-full justify-center text-center items-center">
          <TransitionChild
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
                'relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all  p-6  w-full max-w-lg',
                ...customClass,
              ]"
            >
              <div class="sm:mt-0 sm:text-left flex-1">
                <DialogTitle
                  as="h3"
                  class="text-lg font-medium border-b pb-2.5 flex items-center gap-2 leading-6 text-gray-900"
                >
                  <div
                    :class="
                      btnBgColor ? `${btnBgColor} bg-opacity-20` : 'bg-red-100'
                    "
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md sm:mx-0 sm:h-9 sm:w-9"
                  >
                    <slot name="icon"></slot>
                  </div>
                  {{ confirmationTitle }}

                  <div
                    class="absolute right-0 top-1.5 hidden pr-6 pt-6 sm:block"
                  >
                    <span
                      @click="closeModal"
                      class="material-symbols-outlined cursor-pointer justify-items-end text-[22px]"
                    >
                      close
                    </span>
                  </div>
                </DialogTitle>
                <div class="my-3">
                  <slot name="content"></slot>
                </div>
              </div>
              <div class="gap-4 flex-row-reverse flex flex-wrap">
                <button
                  :disabled="isDisabled"
                  :class="[
                    'rounded-md border border-transparent px-4 py-1.5 text-white shadow-sm focus:outline-none focus:ring-0 sm:text-sm',
                    btnBgColor ? btnBgColor : 'bg-red-600 hover:bg-red-700',
                    isDisabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''
                  ]"
                  @click="submit"
                  type="button"
                >
                  <i
                    v-if="showBtnLoader"
                    class="fa-solid fa-circle-notch fa-spin mr-1 self-center"
                  ></i>
                  {{ showBtnLoader ? "Processing" : submitBtnTitle }}
                  <span class="ml-1" v-if="timer > 0 && !disableButton">
                    ({{ timer }})
                  </span>
                </button>
                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-0 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="closeModal" ref="cancelButtonRef">
                  {{ cancelBtnTitle }}
                </button>
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
export default {
  components: {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
  },
  props: {
    customClass: {
      type: Array,
      default: [],
    },
    showLoader: {
      type: Boolean,
      default: false,
    },
    submitBtnTitle: {
      type: String,
      required: true,
    },
    cancelBtnTitle: {
        type: String,
        required: false,
        default: 'Cancel'
      },
    confirmationTitle: {
      type: String,
      required: true,
    },
    show: {
      type: Boolean,
      default: false,
    },
    btnBgColor: {
      type: String,
    },
    disableButton: {
      type: Boolean,
      default: false,
      required: false,
    },
  },
  computed: {
    isDisabled() {
      return this.timer > 0 || this.showBtnLoader || this.disableButton;
    },
  },
  watch: {
    show(val) {
      this.init(val);
    },
    timer(val) {
      if (val === 0) {
        this.resetTimer();
      }
    },
    showLoader(val) {
      this.showBtnLoader = val;
    },
  },
  data() {
    return {
      timer: 5,
      open: false,
      showBtnLoader: false,
      interval: null,
    };
  },
  methods: {
    init(val) {
      this.open = val;
      if (val) {
        this.timer = 5;
        this.interval = setInterval(() => {
          this.timer = this.timer - 1;
        }, 1000);
      }
    },
    resetTimer() {
      this.timer = 0;
      clearInterval(this.interval);
    },
    closeModal() {
      this.showBtnLoader = false;
      this.open = false;
      this.resetTimer();
      this.$emit("closeModal", false);
    },
    submit() {
      this.$emit("confirm");
    },
  },
};
</script>
