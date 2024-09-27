import AdminLayout from "@/pages/layouts/Admin.vue";
export default [
    {
        name: "billingSetings",
        path: "billing-settings",
        component: () => import("@/pages/admin/Settings.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Settings | Billing",
            setupReqiures: true,
            layout: AdminLayout,
            isAdminPage: true,
        },
    },
];
