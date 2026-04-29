<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="container-fluid mx-auto pb-5">
    <div class="mb-5">
      <h1 class="text-[#2c3138] font-medium text-xl">Auto Recharge</h1>
      <p class="text-gray-500 text-sm mt-1">
        Configure automatic wallet recharge and a default payment method.
      </p>
    </div>

    <div
      v-if="!stripeEnabled && providersFetched"
      class="bg-white border border-amber-200 rounded-xl p-5 sm:p-6"
    >
      <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-amber-500 text-2xl">warning</span>
        <div>
          <h2 class="text-base font-medium text-[#2c3138]">Auto Recharge unavailable</h2>
          <p class="text-sm text-gray-600 mt-1">
            Auto Recharge requires Stripe, but Stripe is not enabled by admin yet.
            Please contact admin to enable Stripe payment gateway.
          </p>
        </div>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 2xl:grid-cols-12 gap-5 items-start">
      <div class="2xl:col-span-8 bg-white border border-slate-200 rounded-xl p-4 sm:p-6">
        <div class="flex items-start gap-3 mb-5">
          <div class="w-10 h-10 min-w-[40px] rounded-lg bg-gray-50 border border-slate-200 flex items-center justify-center text-custom-500">
            <span class="material-symbols-outlined text-[22px]">autorenew</span>
          </div>
          <div>
            <p class="font-medium text-base">Auto Recharge Settings</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
              Automatically recharge wallet when credits go below your configured threshold.
            </p>
          </div>
        </div>

        <template v-if="fetchingProviders">
          <div class="rounded-lg border border-slate-200 bg-gray-50 p-3 sm:p-4 mb-4">
            <Skeleton :count="1" class="!h-5 !w-40 !rounded-md" />
            <Skeleton :count="1" class="!h-4 !w-64 !rounded-md !mt-2" />
          </div>
          <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div>
              <Skeleton :count="1" class="!h-4 !w-32 !rounded-md !mb-1.5" />
              <Skeleton :count="1" class="!h-[42px] !rounded-md" />
            </div>
            <div>
              <Skeleton :count="1" class="!h-4 !w-32 !rounded-md !mb-1.5" />
              <Skeleton :count="1" class="!h-[42px] !rounded-md" />
            </div>
            <div class="xl:col-span-2">
              <Skeleton :count="1" class="!h-4 !w-32 !rounded-md !mb-1.5" />
              <Skeleton :count="1" class="!h-[42px] !rounded-md" />
            </div>
          </div>
          <Skeleton :count="1" class="!h-10 !w-36 !rounded-md !mt-5" />
        </template>

        <template v-else>
          <div class="rounded-lg border border-slate-200 bg-gray-50 p-3 sm:p-4 mb-4">
            <div class="flex items-center justify-between gap-3">
              <div>
                <p class="text-tiny font-medium text-[#2c3138]">Enable Auto Recharge</p>
                <p class="text-xs text-gray-500 mt-1">Turn on automatic recharge checks for your wallet.</p>
              </div>
              <button
                type="button"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none"
                :class="form.auto_recharge_enabled ? 'bg-custom-500' : 'bg-slate-300'"
                @click="handleAutoRechargeToggle"
                :aria-pressed="form.auto_recharge_enabled ? 'true' : 'false'"
              >
                <span
                  class="inline-block h-5 w-5 transform rounded-full bg-white border border-slate-200 transition-transform duration-200"
                  :class="form.auto_recharge_enabled ? 'translate-x-5' : 'translate-x-0.5'"
                ></span>
              </button>
            </div>
            <small id="auto_recharge_enabled_message" class="text-red-500 error_message text-xs mt-2 block"></small>
          </div>

          <div v-if="form.auto_recharge_enabled" class="grid grid-cols-1 xl:grid-cols-2 gap-4">
          <div>
            <label class="text-xs text-gray-500 block mb-1.5">Minimum Threshold</label>
            <div class="flex items-center shadow-sm rounded-md">
              <div class="pointer-events-none bg-gray-50 h-[42px] flex items-center px-3 border border-r-0 border-slate-300 rounded-l-md">
                <span class="text-sm font-medium text-gray-700">{{ siteSettings.currency_symbol }}</span>
              </div>
              <input
                type="number"
                v-model="form.auto_recharge_minimum_credit"
                class="w-full h-[42px] text-sm p-2.5 border border-slate-300 focus:border-slate-300 focus:ring-0 rounded-r-md"
                placeholder="Enter minimum threshold"
              >
            </div>
            <small id="auto_recharge_minimum_credit_message" class="text-red-500 error_message text-xs mt-1.5 block"></small>
          </div>

          <div>
            <label class="text-xs text-gray-500 block mb-1.5">Recharge Amount</label>
            <div class="flex items-center shadow-sm rounded-md">
              <div class="pointer-events-none bg-gray-50 h-[42px] flex items-center px-3 border border-r-0 border-slate-300 rounded-l-md">
                <span class="text-sm font-medium text-gray-700">{{ siteSettings.currency_symbol }}</span>
              </div>
              <input
                type="number"
                v-model="form.auto_recharge_amount"
                class="w-full h-[42px] text-sm p-2.5 border border-slate-300 focus:border-slate-300 focus:ring-0 rounded-r-md"
                placeholder="Enter recharge amount"
              >
            </div>
            <small id="auto_recharge_amount_message" class="text-red-500 error_message text-xs mt-1.5 block"></small>
          </div>

          <div class="xl:col-span-2">
            <label class="text-xs text-gray-500 block mb-1.5">Payment Gateway</label>
            <input
              type="text"
              value="Stripe"
              disabled
              class="w-full h-[42px] text-sm p-2.5 border border-slate-300 bg-gray-50 text-gray-600 rounded-md cursor-not-allowed"
            >
            <small id="auto_recharge_payment_gateway_message" class="text-red-500 error_message text-xs mt-1.5 block"></small>
          </div>
          </div>

          <div v-else class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs sm:text-sm text-amber-700">
            Auto recharge is disabled. Enable it to configure threshold, amount, and payment gateway.
          </div>

          <Button class="w-full sm:w-auto mt-5 px-5 py-2.5 text-sm" :disabled="savingSettings" @click="saveSettings">
            {{ savingSettings ? "Please Wait" : "Save Settings" }}
          </Button>
        </template>
      </div>

      <div class="2xl:col-span-4 bg-white border border-slate-200 rounded-xl p-4 sm:p-6">
        <div class="flex items-start gap-3 mb-5">
          <div class="w-10 h-10 min-w-[40px] rounded-lg bg-gray-50 border border-slate-200 flex items-center justify-center text-custom-500">
            <span class="material-symbols-outlined text-[22px]">credit_card</span>
          </div>
          <div>
            <p class="font-medium text-base">Default Payment Method</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
              Save a default Stripe card so auto recharge can run without manual checkout.
            </p>
          </div>
        </div>

        <div v-if="fetchingPaymentMethodStatus" class="space-y-3">
          <div class="rounded-lg border border-slate-200 p-3">
            <Skeleton :count="1" class="!h-4 !w-1/2 !rounded-md !mb-2" />
            <Skeleton :count="1" class="!h-4 !w-4/5 !rounded-md" />
          </div>
          <div class="rounded-lg border border-slate-200 p-3">
            <Skeleton :count="1" class="!h-4 !w-1/2 !rounded-md !mb-2" />
            <Skeleton :count="1" class="!h-4 !w-3/4 !rounded-md" />
          </div>
          <Skeleton :count="1" class="!h-10 !rounded-md" />
        </div>
        <div v-else>
          <div
            v-if="paymentMethodStatus.payment_methods && paymentMethodStatus.payment_methods.length"
            class="space-y-3 max-h-[280px] overflow-y-auto pr-1"
          >
            <div
              v-for="card in paymentMethodStatus.payment_methods"
              :key="card.id"
              class="rounded-lg border p-3 text-sm"
              :class="card.is_default ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-slate-700'"
            >
              <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                  <input
                    type="radio"
                    :checked="card.is_default"
                    name="default_payment_method"
                    class="h-4 w-4 text-custom-500 border-slate-300 focus:ring-0"
                    @change="setDefaultPaymentMethod(card.id)"
                    :disabled="updatingDefaultPaymentMethod || card.is_default"
                  >
                  <div class="min-w-0">
                    <p class="font-medium truncate">
                      {{ card.brand?.toUpperCase() }} ****{{ card.last4 }} - Exp: {{ card.exp_month }}/{{ card.exp_year }}
                    </p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span
                    v-if="card.is_default"
                    class="text-xs rounded-full bg-emerald-100 text-emerald-700 px-2 py-1 whitespace-nowrap"
                  >
                    Default
                  </span>
                  <button
                    type="button"
                    class="text-xs rounded-md px-2 py-1 border"
                    :class="card.is_default ? 'border-slate-200 text-slate-400 cursor-not-allowed' : 'border-rose-200 text-rose-600 hover:bg-rose-50'"
                    :disabled="removingPaymentMethod || card.is_default"
                    @click="removeDefaultPaymentMethod(card.id)"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
            No default payment method saved.
          </div>
        </div>

        <Button
          class="w-full mt-5 px-4 py-2.5 text-sm"
          :disabled="savingPaymentMethod"
          @click="saveDefaultPaymentMethod"
        >
          {{ savingPaymentMethod ? "Please Wait" : "Add New Card" }}
        </Button>

        <p class="text-xs text-gray-500 mt-3">
          You will be redirected to Stripe to securely save your card details.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from "@/store/auth";
import { mapActions, mapState } from "pinia";

export default {
  data() {
    return {
      breadcrumb: {
        icon: "lab_profile",
        pages: [{ name: "Billing" }, { name: "Auto Recharge" }],
      },
      paymentGateways: [],
      form: {
        auto_recharge_enabled: false,
        auto_recharge_minimum_credit: "",
        auto_recharge_amount: "",
        auto_recharge_payment_gateway: "",
      },
      savingSettings: false,
      savingPaymentMethod: false,
      removingPaymentMethod: false,
      updatingDefaultPaymentMethod: false,
      fetchingProviders: false,
      fetchingPaymentMethodStatus: false,
      paymentMethodStatus: {
        has_default_payment_method: false,
        default_payment_method: null,
        payment_methods: [],
      },
      providersFetched: false,
    };
  },
  computed: {
    ...mapState(useAuthStore, ["user"]),
    stripeAutoRechargeGateways() {
      return (this.paymentGateways || []).filter((gateway) => gateway === "Stripe");
    },
    stripeEnabled() {
      return this.paymentGateways.includes("Stripe");
    },
  },
  created() {
    this.initFromUser();
    this.fetchProviders();
    this.fetchPaymentMethodStatus();
    this.verifySetupSessionFromQuery();
  },
  methods: {
    ...mapActions(useAuthStore, ["getUser"]),
    initFromUser() {
      if (!this.user) return;
      this.form.auto_recharge_enabled = !!this.user.auto_recharge_enabled;
      this.form.auto_recharge_minimum_credit = this.user.auto_recharge_minimum_credit;
      this.form.auto_recharge_amount = this.user.auto_recharge_amount;
      this.form.auto_recharge_payment_gateway = "Stripe";
    },
    fetchProviders() {
      this.fetchingProviders = true;
      this.$axios
        .get("/enable-providers")
        .then(({ data }) => {
          this.paymentGateways = data.payment || [];
          this.form.auto_recharge_payment_gateway = "Stripe";
        })
        .catch(({ response }) => {
          this.$toast.error(response?.data?.message || "Unable to fetch payment gateways.");
        })
        .finally(() => {
          this.providersFetched = true;
          this.fetchingProviders = false;
        });
    },
    saveSettings() {
      this.savingSettings = true;
      this.hideError();
      this.$axios
        .patch("/auto-recharge", {
          auto_recharge_enabled: !!this.form.auto_recharge_enabled,
          auto_recharge_minimum_credit: this.form.auto_recharge_enabled
            ? this.form.auto_recharge_minimum_credit
            : null,
          auto_recharge_amount: this.form.auto_recharge_enabled
            ? this.form.auto_recharge_amount
            : null,
          auto_recharge_payment_gateway: this.form.auto_recharge_enabled
            ? this.form.auto_recharge_payment_gateway
            : null,
        })
        .then(async ({ data }) => {
          this.$toast.success(data.message);
          await this.getUser();
          this.initFromUser();
        })
        .catch(({ response }) => {
          if (response?.status !== 422) {
            this.$toast.error(response?.data?.message || "Unable to save auto recharge settings.");
          }
          if (response?.data) this.displayError(response.data);
        })
        .finally(() => {
          this.savingSettings = false;
        });
    },
    handleAutoRechargeToggle() {
      this.form.auto_recharge_enabled = !this.form.auto_recharge_enabled;

      // When disabling, save immediately so users don't need an extra click.
      if (!this.form.auto_recharge_enabled) {
        this.saveSettings();
      }
    },
    saveDefaultPaymentMethod() {
      this.savingPaymentMethod = true;
      this.$axios
        .post("/auto-recharge/setup-session", {
          gateway: "Stripe",
        })
        .then(({ data }) => {
          if (data.url) {
            window.location.href = data.url;
          } else {
            this.$toast.error("Unable to create Stripe setup session.");
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response?.data?.message || "Unable to start payment method setup.");
        })
        .finally(() => {
          this.savingPaymentMethod = false;
        });
    },
    removeDefaultPaymentMethod(paymentMethodId) {
      this.removingPaymentMethod = true;
      this.$axios
        .delete("/auto-recharge/default-payment-method", {
          data: {
            payment_method_id: paymentMethodId,
          },
        })
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.fetchPaymentMethodStatus();
        })
        .catch(({ response }) => {
          this.$toast.error(response?.data?.message || "Unable to remove default payment method.");
        })
        .finally(() => {
          this.removingPaymentMethod = false;
        });
    },
    setDefaultPaymentMethod(paymentMethodId) {
      this.updatingDefaultPaymentMethod = true;
      this.$axios
        .patch("/auto-recharge/default-payment-method", {
          payment_method_id: paymentMethodId,
        })
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.fetchPaymentMethodStatus();
        })
        .catch(({ response }) => {
          this.$toast.error(response?.data?.message || "Unable to update default payment method.");
        })
        .finally(() => {
          this.updatingDefaultPaymentMethod = false;
        });
    },
    verifySetupSessionFromQuery() {
      const sessionId = this.$route.query.session_id;
      const setupStatus = this.$route.query.setup_status;
      if (!sessionId || setupStatus !== "success") return;

      this.$axios
        .post("/auto-recharge/verify-setup-session", {
          session_id: sessionId,
        })
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.fetchPaymentMethodStatus();
          this.$router.replace({ name: "autoRecharge" });
        })
        .catch(({ response }) => {
          this.$toast.error(response?.data?.message || "Unable to verify payment method setup.");
          this.$router.replace({ name: "autoRecharge" });
        });
    },
    fetchPaymentMethodStatus() {
      this.fetchingPaymentMethodStatus = true;
      this.$axios
        .get("/auto-recharge/payment-method-status")
        .then(({ data }) => {
          this.paymentMethodStatus = data;
        })
        .catch(() => {
          this.paymentMethodStatus = {
            has_default_payment_method: false,
            default_payment_method: null,
            payment_methods: [],
          };
        })
        .finally(() => {
          this.fetchingPaymentMethodStatus = false;
        });
    },
  },
};
</script>
