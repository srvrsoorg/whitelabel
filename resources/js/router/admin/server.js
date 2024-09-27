import AdminLayout from "@/pages/layouts/Admin.vue";

export default [
    {
        name: "servers",
        path: "servers",
        component: () => import("@/pages/admin/Servers.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Servers",
            layout: AdminLayout,
            setupReqiures: true,
        },
    },
    {
        path: "server/:server",
        component: () => import("@/pages/admin/servers/Panel.vue"),
        meta: {
            middleware: ["auth", "admin"],
            setupReqiures: true,
            layout: AdminLayout,
            isAdminServer: true,
        },
        children: [
            {
                path: "details",
                name: "serverDetails",
                component: () =>
                    import("@/pages/admin/servers/ServerDetails.vue"),
                meta: {
                    setupReqiures: true,
                    layout: AdminLayout,
                    middleware: ["auth", "admin"],
                    title: "Server | Admin",
                },
            },
        ],
    },
];
