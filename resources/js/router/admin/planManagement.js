import AdminLayout from "@/pages/layouts/Admin.vue";
export default [
    {
        name: "integrateCloudPlatForms",
        path: "integration/cloud-platforms",
        component: () => import("@/pages/admin/Billing/Plan.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Cloud Platforms Settings",
            setupReqiures: true,
            layout: AdminLayout,
        },
    },
    {
            name: "setPlan",
            path: "integration/cloud-platforms/:id/plans",
            component: () => import("@/pages/admin/Billing/SetPlan.vue"),
            meta: {
                middleware: ["auth", "admin"],
                title: "Cloud Platform Plans | Integrations",
                setupReqiures: true,
                layout: AdminLayout,
            },
        },
];
