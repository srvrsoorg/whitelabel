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
    {
        name: "Webhooks",
        path: "integration/webhooks",
        component: () => import("@/pages/admin/Integration/Webhooks.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Webhooks | Integrations",
            setupReqiures: true,
            layout: AdminLayout,
        },
    },
    {
        name: "WebhookHistory",
        path: "integration/webhooks/:id/history",
        component: () => import("@/pages/admin/Integration/WebhookHistory.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Webhook History | Integrations",
            setupReqiures: true,
            layout: AdminLayout,
        },
    },
];
