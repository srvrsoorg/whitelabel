import AdminLayout from "@/pages/layouts/Admin.vue";
export default [
    {
        name: "Plan",
        path: "billing/plan",
        component: () => import("@/pages/admin/Billing/Plan.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Plan | Billing",
            setupReqiures: true,
            layout: AdminLayout,
        },
    },
    {
        name: "setPlan",
        path: "plan/:id/plans",
        component: () => import("@/pages/admin/Billing/SetPlan.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Cloud Platform Plans | Billing",
            setupReqiures: true,
            layout: AdminLayout,
        },
    },

    {
        name: "promoCodes",
        path: "billing/promoCode",
        component: () => import("@/pages/admin/Billing/PromoCodes.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Promo codes | Billing",
            setupReqiures: true,
            layout: AdminLayout,
        },
    },

    {
        name: "Settings",
        path: "billing/settings",
        component: () => import("@/pages/admin/Billing/Settings.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Settings | Billing",
            setupReqiures: true,
            layout: AdminLayout,
        },
    },

    {
        name: "taxes",
        path: "billing/taxes",
        component: () => import("@/pages/admin/Billing/Taxes.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Taxes | Billing",
            setupReqiures: true,
            layout: AdminLayout,
        },
    },
];
