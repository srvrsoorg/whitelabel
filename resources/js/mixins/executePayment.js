import { useAuthStore } from "@/store/auth";
import { mapActions } from "pinia";
import GlobalMixin from "@/mixins/global";

export default {
    mixin: [GlobalMixin],
    data() {
        return {
            razorpay_key: null,
            razorpay_order_id: null,
            razorpay_payment_id: null,
            script: `https://checkout.razorpay.com/v1/checkout.js`,
            activeTransactionKey: null,
        };
    },
    methods: {
        ...mapActions(useAuthStore, ["getUser"]),
        // Execute Payment
        async executePayment(transaction) {
            this.activeTransactionKey = transaction.key;
            let url = `/user/transactions/execute/${transaction.key}`;
            this.isProcessing = true;
            await this.$axios
                .post(url)
                .then(({ data }) => {
                    if (data.transaction.payment_gateway == "Razorpay") {
                        this.razorpay_order_id = data.transaction.payment_link;
                        this.razorpayPayment(transaction);
                    } else {
                        if (typeof this.paying !== undefined) {
                            this.paying = false;
                        }
                        window.location.href = data.transaction.payment_link;
                    }
                })
                .catch((error) => {
                    this.paying = false
                    this.$toast.error(error.response.data.message);
                })
                .finally(() => {
                    this.isProcessing = false;
                    this.activeTransactionKey = null;
                });
        },
        // Execute Razorpay Payment
        async razorpayPayment(transaction) {
            const result = await this.loadRazorPay();
            if (!result) {
                this.$toast.error("Failed to load razorpay script");
                return;
            }
            const options = {
                key: this.razorpay_key,
                amount: transaction.final_amount,
                currency: "INR",
                name: this.app_name,
                description: this.app_name,
                order_id: this.razorpay_order_id,
                image: this.icon
                    ? this.icon
                    : `${window.location.protocol}//${window.location.host}/logo/logo-sm.png`,
                handler: (response) => {
                    this.executeRazorpay(
                        transaction,
                        response.razorpay_payment_id
                    );
                },
            };
            const paymentObject = new window.Razorpay(options);
            paymentObject.open();
        },
        async loadRazorPay() {
            return new Promise((resolve) => {
                const script = document.createElement("script");
                script.src = this.script;
                script.onload = () => {
                    resolve(true);
                };
                script.onerror = () => {
                    resolve(false);
                };
                document.body.appendChild(script);
            });
        },
        async executeRazorpay(transaction, payment_id) {
            await this.$axios
                .post(`/user/transactions/verify/${transaction.key}`, {
                    razorpay_payment_id: payment_id,
                })
                .then(({ data }) => {
                    this.$toast.success(data.message);
                    this.getUser();
                })
                .catch((error) => {
                    this.$toast.error(error.response.data.message);
                })
                .finally(() => {
                    if(this.$route.name === 'transactions'){
                        this.fetchTransactions()
                    }else{
                        setTimeout(() => {
                            this.$router.push({ name: "transactions" });
                        }, 2000);
                    }
                });
        },
    },
};
