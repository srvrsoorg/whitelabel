<template>
  <div class="bg-white border rounded-md shadow-sm p-5" v-if="payment">
    <div class="xl:grid-cols-12">
      <div class="mb-2.5">
        <img
          v-if="payment.provider == 'Razorpay'"
          src="/logo/Razorpay.png"
          :alt="app_name"
          class="w-32 h-auto"
        />
        <img
          v-if="payment.provider == 'Paypal'"
          src="/logo/paypal.png"
          :alt="app_name"
          class="w-28 h-auto"
        />
        <img
        v-if="payment.provider == 'Stripe'"
        src="/logo/stripe-logo.png"
        :alt="app_name"
        class="w-auto h-10"
        />
        <img
          v-if="payment.provider == 'Cashfree'"
          src="/logo/cashfree.svg"
          :alt="app_name"
          class="w-28 h-auto"
        />
      </div>
    </div>
    <div class="xl:col-span-10">
      <div class="">
        <div>
          <div
            class="bg-[#F6F6F6] border border-primary shadow-sm rounded-md flex justify-between sm:items-center items-start py-2 px-3"
          >
            <span class="text-tiny"
              >Enable
              <span class="capitalize">{{ payment.provider }}</span> Payment
              Gateway</span
            >
            <Switch
              @update:modelValue="toggle()"
              v-model="payment.enabled"
              :class="[
                payment.enabled
                  ? isLightColor
                    ? 'bg-custom-700'
                    : 'bg-custom-500'
                  : 'bg-gray-200',
                'relative inline-flex h-6 w-11 my-1 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0',
              ]"
            >
              <span class="sr-only">Use setting</span>
              <span
                aria-hidden="true"
                :class="[
                  payment.enabled ? 'translate-x-5' : 'translate-x-0',
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                ]"
              />
            </Switch>
          </div>
        </div>
        <template v-if="payment.enabled">
          <div
            class="text-tiny p-3 rounded-md my-5 text-[#308DEA] bg-[#EFF6FF]"
            v-if="payment.provider === 'Stripe'"
          >
            <p>
              <b class="font-medium">Note:</b> Your Stripe payment gateway
              environment isn't static; it's configured based on your account
              settings. Ensure your settings are adjusted correctly to match the
              intended environment for your account.
            </p>
          </div>
          <div
            class="grid sm:grid-cols-2 grid-cols-1 my-5 mb-5 xl:gap-40 sm:gap-14 gap-1 "
          >
            <div>
              <div
                class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1 items-center"
              >
                <div
                  class="xl:col-span-3 2xl:grid-cols-4 md:pt-0 pt-2 sm:col-span-3 col-span-12"
                >
                  <label
                    class="font-medium whitespace-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
                  >
                    {{ payment.provider === 'Stripe' ? 'Publishable Key' : 'Client ID' }}
                  </label>
                </div>
                <div
                  class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12"
                >
                  <input
                    v-model="payment.client_id"
                    type="text"
                    name="client_id"
                    :id="`${payment.provider}_client_id`"
                    class="block w-full rounded-md border border-primary focus:border-primary py-2 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                    placeholder="Enter Client ID"
                  />
                </div>
              </div>
              <div
                class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1 items-center"
              >
                <div
                  class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12 hidden xl:block"
                ></div>
                <div
                  class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12"
                >
                  <small
                    class="text-red-500 error_message text-xs"
                    :id="`${payment.provider}_client_id_message`"
                  ></small>
                </div>
              </div>
            </div>
            <div>
              <div
                class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1 items-center"
              >
                <div
                  class="xl:col-span-4 2xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12"
                >
                  <label
                    class="font-medium whitespace-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
                  >
                    {{ payment.provider === 'Stripe' ? 'Secret Key' : 'Client Secret' }}
                  </label>
                </div>
                <div
                  class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
                >
                  <input
                    v-model="payment.client_secret"
                    type="text"
                    name="client_secret"
                    :id="`${payment.provider}_client_secret`"
                    class="block w-full rounded-md border border-primary focus:border-primary py-2 text-gray-800 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                    placeholder="Enter Client Secret"
                  />
                </div>
              </div>
              <div
                class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1 items-center"
              >
                <div
                  class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12 xl:block hidden"
                ></div>
                <div
                  class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
                >
                  <small
                    class="text-red-500 error_message text-xs"
                    :id="`${payment.provider}_client_secret_message`"
                  ></small>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-5" v-if="['Paypal','Cashfree'].includes(payment.provider)">
            <div
              class="border border-primary shadow-sm py-3.5 rounded-md flex justify-between items-start sm:items-center px-3"
            >
              <div class="flex items-center gap-2">
                <Switch
                  v-model="payment.isTestMode"
                  :class="[
                    payment.isTestMode
                      ? isLightColor
                        ? 'bg-custom-700'
                        : 'bg-custom-500'
                      : 'bg-gray-200',
                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0',
                  ]"
                >
                  <span class="sr-only">Use setting</span>
                  <span
                    aria-hidden="true"
                    :class="[
                      payment.isTestMode ? 'translate-x-5' : 'translate-x-0',
                      'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    ]"
                  />
                </Switch>
                <h2 class="text-tiny">Test Mode</h2>
              </div>
              <Badge
                class="py-1.5"
                variant="danger"
                badgeTitle="Sandbox"
                v-if="payment.isTestMode"
              />
              <Badge badgeTitle="Production" variant="success" v-else />
            </div>
          </div>
          <div class="mt-5 flex justify-end items-center">
            <Button @click="updatePayment"> Save Settings </Button>
          </div>
        </template>
      </div>
    </div>
  </div>

  <Confirmation
    :show="openConfirmation"
    :showLoader="showLoader"
    :confirmationTitle="'Update Payment Integration'"
    :btnBgColor="'bg-[#FFB74D]'"
    :submitBtnTitle="`Yes, I'm Sure`"
    @confirm="saveData"
    @closeModal="closeModal"
  >
    <template #icon>
      <span class="material-symbols-outlined text-[#FFB74D] text-[22px]">
        rule_settings
      </span>
    </template>
    <template #content>
      <span class="text-tiny text-gray-600">
        Are you sure you want to update the integrated payment details?
      </span>
      <div class="my-5 text-tiny font-medium flex gap-3">
        <div>
          <span
            class="material-symbols-outlined bg-[#FFB74D] text-white rounded-full font-semibold text-[20px] p-0.5 flex items-center"
          >
            check
          </span>
        </div>
        <span>
          The changes will apply immediately and affect the new transactions.
        </span>
      </div>
    </template>
  </Confirmation>
</template>
<script>
import { Switch } from "@headlessui/vue";
export default {
  name: "PaymentConf",
  components: {
    Switch,
  },
  props: ["provider"],
  data() {
    return {
      payment: null,
      openConfirmation: false,
      showLoader: false,
    };
  },
  created() {
    this.payment = this.provider;
  },
  watch: {
    "provider.isTestMode": {
      handler(val) {
        this.payment.mode = val ? "sandbox" : "live";
      },
    },
    provider: {
      handler(val) {
        if (val) {
          this.payment = val;
        }
      },
      deep: true,
    },
  },
  methods: {
    async updatePayment() {
      this.openConfirmation = true;
    },
    closeModal() {
      this.openConfirmation = false;
      this.showLoader = false;
    },
    async saveData() {
      if (this.showLoader) return;
      this.showLoader = true;
      this.hideError();
      await this.$axios
        .post(`/admin/payments`, this.payment)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.$emit("updateValue");
          this.closeModal();
        })
        .catch(({ response }) => {
          if (response !== undefined) {
            const { status, data } = response;
            if (status === 422) {
              $.each(data.errors, (key, value) => {
                key = key.replaceAll(".", "_");
                $(`#${this.payment.provider}_${key}`).addClass(
                  "border-red-500"
                );
                $(`#${this.payment.provider}_${key}_message`)
                  .html(value[0])
                  .fadeIn();
              });
            } else {
              this.$toast.error(data.message);
            }
            this.closeModal();
          }
        })
        .finally(() => {
          this.showLoader = false;
        });
    },
    async toggle() {
      setTimeout(() => {
        if (!this.payment.enabled && this.payment.id) {
          this.$axios
            .patch(`/admin/payments/${this.payment.id}`)
            .then(({ data }) => {
              this.$toast.success(data.message);
            })
            .catch(({ response: { data } }) => {
              this.$toast.error(data.message);
            })
            .finally(() => {
              this.$emit("updateValue");
            });
        } else {
          if (this.payment.client_id && this.payment.client_secret) {
            this.saveData();
          }
        }
      }, 200);
    },
  },
};
</script>
