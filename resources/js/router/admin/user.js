import AdminLayout from "@/pages/layouts/Admin.vue";

export default[
    {
        name: "adminUsers",
        path: "users",
        component: () => import("@/pages/admin/Users.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Users | Admin",
            setupReqiures: true,
            layout: AdminLayout,
        },
    },
    {
        path: "users/:user",
        component: () => import("@/pages/admin/user/Panel.vue"),
        meta: {
            middleware: ["auth", "admin"],
            setupReqiures: true,
            layout: AdminLayout,
            isAdminAccount: true,
        },
        children: [
            {
                path: "",
                name: "userProfile",
                component: () => import("@/pages/admin/user/Account.vue"),
                meta: {
                    middleware: ["auth", "admin"],
                    title: "Account | User | Admin",
                },
            },
            {
                path: "two-factor-authentication",
                name: "userTfa",
                component: () => import("@/pages/admin/user/Tfa.vue"),
                meta: {
                    middleware: ["auth", "admin"],
                    title: "Two Factor Authentication | User | Admin",
                },
            },
            {
                name: "userActivity",
                path: "activities",
                component: () => import("@/pages/admin/user/Activity.vue"),
                meta: {
                    middleware: ["auth", "admin"],
                    title: "Activity | User | Admin",
                },
            },
            {
                name: "userLoginHistory",
                path: "login-history",
                component: () => import("@/pages/admin/user/LoginHistory.vue"),
                meta: {
                    middleware: ["auth", "admin"],
                    title: "Login History | User | Admin",
                },
            },
            {
                name: "userServers",
                path: "servers",
                component: () => import("@/pages/admin/user/Servers.vue"),
                meta: {
                    middleware: ["auth", "admin"],
                    title: "Servers | User | Admin",
                },
            },
            {
                name: "userTransactions",
                path: "transactions",
                component: () => import("@/pages/admin/user/Transactions.vue"),
                meta: {
                    middleware: ["auth", "admin"],
                    title: "Transactions | User | Admin",
                },
            },
            {
                name: "usageSummary",
                path: "usage-summary",
                component: () => import("@/pages/admin/user/UsageSummary.vue"),
                meta: {
                    middleware: ["auth", "admin"],
                    title: "Usage Summary | User | Admin",
                },
            },
        ]
    }
]