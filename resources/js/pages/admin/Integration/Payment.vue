<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="">
    <span class="text-[#31363f] text-xl font-medium">Payment Integration</span>
  </div>
  <div class="my-5">
    <PaymentConf :provider="razorpay" @updateValue="fetchData()"></PaymentConf>
  </div>
  <div class="my-5">
    <PaymentConf :provider="paypal" @updateValue="fetchData()"></PaymentConf>
  </div>
  <div class="my-5">
    <PaymentConf :provider="stripe" @updateValue="fetchData()"></PaymentConf>
  </div>
  <div class="my-5">
    <PaymentConf :provider="paytr" @updateValue="fetchData()"></PaymentConf>
  </div>
</template>

<script>
import PaymentConf from "@/components/siteConfigration/PaymentConf.vue";
export default {
  components: {
    PaymentConf,
  },
  name: "Payment",
  data() {
    return {
      breadcrumb: {
        title: "Integrations",
        icon: "rule_settings",
        pages: [{ name: "Payment" }],
      },
      razorpay: {
        provider: "Razorpay",
        enabled: false,
        client_id: "",
        client_secret: "",
        mode: "sandbox",
        isTestMode: true,
        processing: false,
      },
      paypal: {
        provider: "Paypal",
        enabled: false,
        client_id: "",
        client_secret: "",
        mode: "sandbox",
        isTestMode: true,
        processing: false,
      },
      stripe: {
        provider: "Stripe",
        enabled: false,
        client_id: "",
        client_secret: "",
        mode: "sandbox",
        processing: false,
      },
      paytr: {
        provider: "Paytr",
        enabled: false,
        client_id: "",
        client_secret: "",
        client_key: "",
        mode: "sandbox",
        processing: false,
      },
    };
  },
  methods: {
    async fetchData() {
      await this.$axios
        .get(`/admin/payments`)
        .then(({ data }) => {
          if (data.paymentConfigs.length) {
            data.paymentConfigs.map((data) => {
              if (Object.keys(data).length) {
                if (data.provider == "Razorpay") {
                  this.razorpay = data;
                  this.razorpay.isTestMode =
                    data.mode == "sandbox" ? true : false;
                } else if (data.provider == "Paypal") {
                  this.paypal = data;
                  this.paypal.isTestMode =
                    data.mode == "sandbox" ? true : false;
                } else if (data.provider == "Stripe") {
                  this.stripe = data;
                } else if (data.provider == "Paytr") {
                  this.paytr = data;
                }
              }
            });
          }
        })
        .catch(({ response }) => {
          this.$toast.errro(response.data.message);
        });
    },
  },

  mounted() {
    this.fetchData();
  },
};
</script>
