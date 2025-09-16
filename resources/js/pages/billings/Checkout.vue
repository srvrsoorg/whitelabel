<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="container-fluid mx-auto">
    <div class="max-w-xl w-full mx-auto shadow rounded-md mt-10">
      <div class="grid grid-cols-1 rounded-md">
        <div class="bg-[#F6F6F6] shadow-md px-6 py-6 rounded-r-md">
          <span class="text-2xl font-medium text-[#2c3138]">Checkout</span>
          <span class="mt-2 text-tiny font-medium text-gray-400 block"
            >Effortless transactions await on our streamlined checkout screen,
            ensuring smooth purchases every time.</span
          >

          <div
            class="my-5 border border-[#A1ACBD] border-dashed"
            style="
              border-image: repeating-linear-gradient(
                  to right,
                  #e5e7eb 0px,
                  #e5e7eb 8px,
                  transparent 2px,
                  transparent 12px
                )
                1;
            "
          ></div>
          <div class="flex justify-between pb-1">
            <p class="text-gray-400 font-medium text-tiny">Sub Total</p>
            <p class="text-[#2c3138] font-medium">
              {{ formatCurrency(checkout.sub_total) }}
            </p>
          </div>
          <div
            v-if="minimumAmount > payload.credits"
            class="flex justify-between py-1.5"
          >
            <p class="text-gray-400 font-medium text-tiny">
              Min Transaction Required
            </p>
            <p class="text-[#2c3138] font-medium">
              {{ formatCurrency(minimumAmount) }}
            </p>
          </div>
          <div
            class="flex justify-between py-1.5"
            v-if="parseFloat(checkout.discount_amount) > 0"
          >
            <p class="text-gray-400 font-medium text-tiny">
              Discount ({{ checkout.discount_percentage }}%)
            </p>
            <p class="text-[#2c3138] font-medium">
              {{ formatCurrency(checkout.discount_amount) }}
            </p>
          </div>
          <template
            v-if="checkout.tax_detail && checkout.tax_detail.length > 0"
          >
            <div
              class="flex justify-between py-1.5"
              v-for="(tax, index) in checkout.tax_detail"
              :key="index"
            >
              <p class="text-gray-400 font-medium text-tiny">
                {{ tax.label }} ({{ tax.tax }}%)
              </p>
              <p class="text-[#2c3138] font-medium">
                {{ formatCurrency(tax.tax_amount) }}
              </p>
            </div>
          </template>
          <div
            class="my-2.5 border border-[#A1ACBD] border-dashed"
            style="
              border-image: repeating-linear-gradient(
                  to right,
                  #e5e7eb 0px,
                  #e5e7eb 8px,
                  transparent 2px,
                  transparent 12px
                )
                1;
            "
          ></div>
          <div class="flex justify-between items-center">
            <p class="text-[#2c3138] text-[18px] font-medium">Total</p>
            <p
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'text-[18px] font-semibold',
              ]"
            >
              {{ formatCurrency(checkout.final_amount) }}
            </p>
          </div>

          <div
            class="mt-3 mb-5 border border-[#A1ACBD] border-dashed"
            style="
              border-image: repeating-linear-gradient(
                  to right,
                  #e5e7eb 0px,
                  #e5e7eb 8px,
                  transparent 2px,
                  transparent 12px
                )
                1;
            "
          ></div>
          <div class="grid sm:grid-cols-4 gap-5 mt-2">
            <div class="sm:col-span-3">
              <input
                v-model="promoCode"
                class="w-full placeholder:font-italitc text-sm border-0 text-zinc-500 focus:ring-0 focus:border-sa-500 rounded-md py-2.5 focus:outline-none"
                placeholder="Enter Promo Code"
                type="text"
                :disabled="
                  checkout.promo_code !== null && checkout.promo_code.active
                "
              />
              <small
                class="text-red-500 error_message"
                id="promo_code_message"
                style="display: none"
              ></small>
            </div>
            <button
              type="button"
              class="bg-red-500 text-white text-tiny rounded w-100"
              @click="removePromoCode"
              v-if="checkout.promo_code !== null && checkout.promo_code.active"
            >
              Remove
            </button>
            <Button
              class="h-9"
              @click="applyCode"
              :disabled="processing || promoCode == ''"
              v-else
            >
              <i
                v-if="processing"
                class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
              ></i>
              Apply
            </Button>
          </div>

          <span class="text-neutral-900 block mt-5">
            Select Payment Method
          </span>
          <div class="w-full cursor-pointer rounded-md">
            <div v-if="payment">
              <RadioGroup v-model="payload.gateway" class="w-full">
                <RadioGroupOption
                  v-for="gateway in enabledPaymentProviders"
                  :key="gateway"
                  :value="gateway"
                  as="div"
                  :class="[
                    payload.gateway == gateway
                      ? isLightColor
                        ? 'border-custom-700 '
                        : 'border-custom-500 '
                      : 'border-primary bg-white',
                    'my-3 cursor-pointer border w-full rounded-md',
                  ]"
                >
                  <div
                    :class="[
                      'justify-between items-center px-5 flex',
                      gateway == 'Paypal' || gateway=='Cashfree' ? 'py-2.5' : 'py-1',
                    ]"
                  >
                    <div class="flex items-center">
                      <input
                        type="radio"
                        v-model="payload.gateway"
                        :value="gateway"
                        class="form-radio rounded-full focus:ring-0 focus:outline-none"
                      />
                      <label
                        :for="gateway"
                        class="ml-2 text-tiny font-medium"
                        >{{ gateway }}</label
                      >
                    </div>
                    <div>
                      <img
                        :src="getGatewayLogo(gateway).src"
                        :width="getGatewayLogo(gateway).width"
                        :height="getGatewayLogo(gateway).height"
                        :style="{
                          padding: getGatewayLogo(gateway).padding,
                        }"
                        class="justify-self-end mx-auto"
                      />
                    </div>
                  </div>
                </RadioGroupOption>
              </RadioGroup>
            </div>
          </div>

          <small
            class="text-red-500 error_message"
            id="payment_gateway_message"
            style="display: none"
          ></small>
          <div v-if="siteSettings && siteSettings.refund_policy" class="mt-3 text-sm text-gray-600">
            By proceeding with payment, you agree to our
            <a :href="siteSettings.refund_policy" target="_blank" class="text-custom-500 hover:underline">
              Refund Policy
            </a>.
          </div>
          <Button
            @click="pay()"
            :disabled="paying || !enabledPaymentProviders.length"
            class="mt-5 w-full flex gap-1 justify-center items-center"
          >
            <i
              v-if="paying"
              class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
            ></i>
            <span v-else class="material-symbols-outlined text-lg"> lock </span>
            Pay Now
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from "@/store/auth";
import { mapState, mapActions } from "pinia";
import { RadioGroup, RadioGroupLabel, RadioGroupOption } from "@headlessui/vue";
import executePayment from "@/mixins/executePayment";

export default {
  mixins: [executePayment],
  data() {
    return {
      breadcrumb: {
        pages: [
          {
            name: "Billing",
            path: { name: "wallet" }
          },
          {
            name: "Checkout"
          }
        ],
          icon: "lab_profile",
        },
      payment: true,
      payload: {
        type: "discount",
        credits: 0,
        gateway: "",
        promo_code: null,
      },
      checkout: {
        base_amount: 0,
        discount_amount: 0,
        sub_total: 0,
        tax_amount: 0,
        final_amount: 0,
        discount_percentage: 0,
        tax_detail: [],
        promo_code: null,
      },
      promoCode: "",
      enabledPaymentProviders: [],
      showMinTranRequired: false,
      processing: false,
      paying: false,
      minimumAmount: 1,
    };
  },
  components: {
    RadioGroup,
    RadioGroupLabel,
    RadioGroupOption,
  },
  watch: {
    "$route.query": {
      handler(val) {
        if (val.credits) {
          this.payload.credits = val.credits;
        }
        this.promoCode = "";
        this.payload.promo_code = null;
      },
    },
    "payload.promo_code": {
      handler(val) {
        if (
          this.checkout.final_amount < this.minimumAmount &&
          val &&
          val.active
        ) {
          this.removePromoCode();
          this.$toast.error(
            `To take advantage of the promo code, please make a transaction totaling more than ${window.siteSettings.currency_symbol}${this.minimumAmount}.`
          );
        }
      },
    },
    "payload.gateway": {
      handler(val) {
        if (this.checkout.final_amount < this.minimumAmount) {
          this.promoCode = "";
          this.payload.promo_code = null;
        }
      },
    },
  },

  computed: {
    ...mapState(useAuthStore, ["user"]),
    totalTaxPer() {
      return this.checkout.tax_detail.reduce((total, item) => {
        return total + item.tax;
      }, 0);
    },
  },

  created() {
    this.fetchPaymentGateway();
    if (
      this.$route.query.action == "add-credits" &&
      parseFloat(this.$route.query.credits)
    ) {
      this.payload.credits = parseFloat(this.$route.query.credits);
    }
    this.getPaymentDetail();
  },
  methods: {
    removePromoCode() {
      this.promoCode = "";
      this.payload.promo_code = null;
      this.getPaymentDetail();
    },
    async getPaymentDetail() {
      this.hideError();
      this.$axios
        .post("/user/transactions/checkout", {
          credits: this.payload.credits,
          promo_code: this.payload.promo_code,
          promo_code_type: "discount",
        })
        .then(({ data }) => {
          if (
            data.checkout.final_amount < this.minimumAmount &&
            data.promo_code
          ) {
            this.promoCode = "";
            this.payload.promo_code = null;
            $(`#promo_code_message`)
              .html(
                `To take advantage of the promo code, please make a transaction totaling more than ${this.formatCurrency(
                  this.minimumAmount
                )}.`
              )
              .fadeIn();
          } else {
            this.checkout.base_amount = data.checkout.base_amount;
            this.checkout.discount_amount = data.checkout.discount_amount;
            this.checkout.sub_total = data.checkout.sub_total;
            this.checkout.tax_amount = data.checkout.tax_amount;
            this.checkout.final_amount = data.checkout.final_amount;
            this.checkout.tax_detail = data.checkout.tax_detail;
            this.checkout.promo_code = data.promo_code;
          }
        })
        .catch((error) => {
          this.$toast.error(error.response.data.message);
        });
    },
    async fetchPaymentGateway() {
      this.$axios
        .get("/enable-providers")
        .then(({ data }) => {
          this.enabledPaymentProviders = data.payment;
          if (data.basic_details) {
            this.minimumAmount = data.basic_details.minimum_recharge_amount;
          }
          this.payload.gateway = this.enabledPaymentProviders[0];
        })
        .catch((error) => {
          this.$toast.error(error.response.data.message);
        });
    },
    getGatewayLogo(gateway) {
      const logos = {
        Razorpay: {
          src: "/logo/Razorpay.png",
          width: "100px",
          height: "50px",
          padding: "8px",
        },
        Paypal: {
          src: "/logo/paypal.png",
          width: "70px",
          height: "20px",
        },
        Stripe: {
          src: "/logo/stripe-logo.png",
          width: "70px",
          height: "25px",
        },
        Cashfree: {
          src: "/logo/cashfree.svg",
          width: "70px",
          height: "30px",
        },
      };
      return (
        logos[gateway] || {
          src: "",
          width: "auto",
          height: "auto",
          padding: "0",
        }
      );
    },
    async applyCode() {
      this.processing = true;
      $("#promo_code_message").hide();

      await this.$axios
        .get(`/promo-codes/${this.promoCode}`)
        .then(({ data }) => {
          if (
            data.promo_code.active &&
            data.promo_code.type !== "free_credits"
          ) {
            this.payload.promo_code = data.promo_code.code;
            this.checkout.discount_percentage = data.promo_code.discount;
          } else {
            this.payload.promo_code = null;
            $("#promo_code_message").html("Promo code is expired!");
            $("#promo_code_message").show();
          }
          this.getPaymentDetail();
        })
        .catch((error) => {
          this.payload.promo_code = null;
          $("#promo_code_message").html(error.response.data.message);
          $("#promo_code_message").show();
        })
        .finally(() => {
          this.processing = false;
        });
    },
    pay() {
      this.paying = true;
      this.hideError();
      const totalAmount = parseFloat(this.checkout.final_amount);

      if (totalAmount < this.minimumAmount) {
        this.$toast.error(
          `The required minimum transaction amount is ${window.siteSettings.currency_symbol}${this.minimumAmount}`
        );
        this.$router.push("/billing/wallet");
        this.paying = false;
        return;
      }

      let payload = {
        type: this.payload.type,
        credits: this.payload.credits,
        promo_code: this.payload.promo_code ? this.payload.promo_code : null,
        payment_gateway: this.payload.gateway,
      };

      let url = `/user/transactions`;

      this.$axios
        .post(url, payload)
        .then(async ({ data }) => {
          if (parseFloat(this.checkout.final_amount) < this.minimumAmount) {
            this.$router.push("/billing/wallet");
            this.$toast.error(data.message);
          } else {
            await this.executePayment(data.transaction);
            this.$toast.success(data.message);
            this.displayError;
            if (payload.payment_gateway == "Razorpay") {
              this.paying = false;
            }
          }
        })
        .catch(({ response }) => {
          this.paying = false;
          if (response) {
            const { status, data } = response;
            const message = data?.message;

            if ([422, 402, 500].includes(status)) {
              this.$toast.error(message);
              this.$router.push("/billing/wallet");
            } else {
              this.$toast.error(data.message);
            }
          }
        })
    },
  },
};
</script>
