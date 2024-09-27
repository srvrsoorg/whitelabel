<template>
  <div>
    <InstallationInfo />
    <div
      class="w-full max-w-[25rem] mx-auto 2xl:mt-36 xl:mt-24 sm:mt-16 mt-10 mb-4 p-4"
    >
      <div class="">
        <form action="javascript:void(0)" @submit="saveKeys()">
          <p class="block text-tiny text-neutral-800 font-medium">
            To obtain your license key, follow these steps:
          </p>
          <div class="mt-4">
            <div class="relative">
              <div
                class="absolute bg-gray-300 left-1 top-5 h-full w-[1px]"
                aria-hidden="true"
              />
              <div class="flex items-center gap-7">
                <span
                  :class="[
                    isLightColor ? 'text-custom-700' : 'text-custom-500',
                    'flex items-center z-10 justify-center  pl-[0.5px] py-4  rounded-full tabular-nums',
                  ]"
                >
                  <i class="fa-solid fa-circle text-[8px]"></i>
                </span>
                <span class="text-sm text-gray-500"
                  >Log in to your ServerAvatar account.</span
                >
              </div>
            </div>
            <div class="mt-1">
              <div class="relative">
                <div
                  class="absolute bg-gray-300 left-1 top-5 h-full w-[1px]"
                  aria-hidden="true"
                />
                <div class="flex items-center gap-7">
                  <span
                    :class="[
                      isLightColor ? 'text-custom-700' : 'text-custom-500',
                      'flex items-center z-10 justify-center  pl-[0.5px] py-4  rounded-full tabular-nums',
                    ]"
                  >
                    <i class="fa-solid fa-circle text-[8px]"></i>
                  </span>
                  <span class="text-sm text-gray-500"
                    >Click on the Add-ons option.</span
                  >
                </div>
              </div>
            </div>
            <div class="mt-1">
              <div class="relative">
                <div
                  class="absolute bg-gray-300 left-1 top-5 h-full w-[1px]"
                  aria-hidden="true"
                />
                <div class="flex items-center gap-7">
                  <span
                    :class="[
                      isLightColor ? 'text-custom-700' : 'text-custom-500',
                      'flex items-center z-10 justify-center  pl-[0.5px] py-4  rounded-full tabular-nums',
                    ]"
                  >
                    <i class="fa-solid fa-circle text-[8px]"></i>
                  </span>
                  <span class="text-sm text-gray-500"
                    >Navigate to the Self Hosted section.</span
                  >
                </div>
              </div>
            </div>
            <div class="mt-1">
              <div class="relative">
                <div
                  class="absolute bg-gray-300 left-1 top-5 h-full w-[1px]"
                  aria-hidden="true"
                />
                <div class="flex items-center gap-7">
                  <span
                    :class="[
                      isLightColor ? 'text-custom-700' : 'text-custom-500',
                      'flex items-center z-10 justify-center  pl-[0.5px] py-4  rounded-full tabular-nums',
                    ]"
                  >
                    <i class="fa-solid fa-circle text-[8px]"></i>
                  </span>
                  <span class="text-sm text-gray-500"
                    >Find your License Key listed in the Self Hosted tab.</span
                  >
                </div>
              </div>
            </div>
            <div class="">
              <div class="relative">
                <div class="flex items-center gap-7">
                  <span
                    :class="[
                      isLightColor ? 'text-custom-700' : 'text-custom-500',
                      'flex items-center justify-center   py-5  rounded-full tabular-nums',
                    ]"
                  >
                    <i class="fa-solid fa-circle text-[8px]"></i>
                  </span>
                  <span class="text-sm text-gray-500"
                    >Copy the key and paste it below.</span
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="mt-4">
            <label
              for="license_key"
              class="block text-tiny text-neutral-800 font-medium"
            >
              License Key
            </label>
            <div class="mt-1.5">
              <div class="relative">
                <span
                  class="material-symbols-outlined absolute -rotate-45 top-1/2 transform -translate-y-1/2 left-3 text-gray-400 text-[18px]"
                  >key</span
                >
                <input
                  id="license_key"
                  name="license_key"
                  type="password"
                  placeholder="TEM8S2-2ET83-CGKP1-DPSI2-EPZO1"
                  v-model="license_key"
                  class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 pl-10 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                />
              </div>
              <small
                id="license_key_message"
                class="error_message text-red-500"
              ></small>
            </div>
          </div>
          <div class="flex flex-wrap justify-between gap-7 mt-6">
            <router-link
              :class="[
                isLightColor
                  ? 'text-custom-700 border-custom-700'
                  : 'text-custom-500 border-custom-500',
                'border rounded-md px-5 py-1.5 text-sm font-medium flex items-center',
              ]"
              :to="{
                name: 'setupDatabase',
              }"
            >
              Back
            </router-link>
            <Button :disabled="processing" :class="['px-5']">
              <i
                v-if="processing"
                class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
              ></i>
              {{ processing ? "Please Wait" : "Verify" }}
            </Button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useSetupStore } from "@/store/setup.js";
import { defineAsyncComponent } from "vue";

export default {
  data() {
    return {
      domain: window.location.host,
      license_key: "",
      processing: false,
      showKey: false,
    };
  },
  created() {
    if (!this.databaseComplete) {
      this.$router.push({
        name: "setupDatabase",
      });
    }
  },
  components: {
    InstallationInfo: defineAsyncComponent(() =>
      import("@/components/InstallationInfo.vue")
    ),
  },
  computed: {
    ...mapState(useSetupStore, ["databaseComplete"]),
  },
  methods: {
    async saveKeys() {
      this.hideError();
      this.processing = true;

      await this.$axios
        .post("/setup/verify", {
          domain: this.domain,
          license_key: this.license_key,
        })
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.$router.push({
            name: "setupSmtp",
          });
        })
        .catch(({ response }) => {
          if (response !== undefined) {
            const { status, data } = response;
            if (status === 422) {
              this.displayError(data);
            } else {
              this.$toast.error(data.message);
            }
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },
};
</script>

