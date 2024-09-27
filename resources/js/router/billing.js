import userLayout from "@/pages/layouts/User.vue";
export default [
    {
        name: "wallet",
        path: "/billing/wallet",
        component: () => import("@/pages/billings/Wallet.vue"),
        meta: {
            middleware: ["auth"],
            title: `Wallet | Billing`, // Title for the route
            layout: userLayout,
            setupReqiures: true,
        },
    },
    {
        name: "checkout",
        path: "/billing/checkout",
        component: () => import("@/pages/billings/Checkout.vue"),
        meta: {
            middleware: ["auth"],
            title: `Checkout | Billing`, // Title for the route
            layout: userLayout,
            setupReqiures: true,
        },
    },
    {
        name: "transactions",
        path: "/billing/transactions",
        component: () => import("@/pages/billings/Transactions.vue"),
        meta: {
            middleware: ["auth"],
            title: `Transaction | Billing`, // Title for the route
            layout: userLayout,
            setupReqiures: true,
        },
    },
    {
        name: "settings",
        path: "/billing/settings",
        component: () => import("@/pages/billings/Settings.vue"),
        meta: {
            middleware: ["auth"],
            title: `Settings | Billing`, // Title for the route
            layout: userLayout,
            setupReqiures: true,
        },
    },
    {
        name: "viewTransaction",
        path: "/billing/transactions/:key",
        component: () => import("@/pages/billings/ViewTransaction.vue"),
        meta: {
            middleware: ["auth"],
            title: `Transaction`, // Title for the route
            layout: userLayout,
            setupReqiures: true,
        },
    },
    {
        name: "billingDetails",
        path: "/billing/details",
        component: () => import("@/pages/billings/BillingDetails.vue"),
        meta: {
            middleware: ["auth"],
            title: `Billing Details`, // Title for the route
            layout: userLayout,
            setupReqiures: true,
        },
    },
];
