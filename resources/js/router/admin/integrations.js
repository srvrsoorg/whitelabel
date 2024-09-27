import AdminLayout from "@/pages/layouts/Admin.vue";
export default [
    {
        name: "payment",
        path: "integration/payment",
        component: () => import("@/pages/admin/Integration/Payment.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Payment | Integrations",
            setupReqiures: true,
            layout: AdminLayout,
        },
    },
    {
        name: "SMTP",
        path: "integration/smtp",
        component: () => import("@/pages/admin/Integration/SMTP.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "SMTP | Integrations",
            setupReqiures: true,
            layout: AdminLayout,
        },
    },
    
];
