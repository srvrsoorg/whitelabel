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
];
