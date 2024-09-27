import AdminLayout from "@/pages/layouts/Admin.vue";

export default [
    {
        name: "admintickets",
        path: "tickets",
        component: () => import("@/pages/admin/ticket/Tickets.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Tickets",
            layout: AdminLayout,
            requiresAuth: true,
        },
    },
    {
        name: "adminTicketShow",
        path: "tickets/:id",
        component: () => import("@/pages/admin/ticket/TicketShow.vue"),
        meta: {
            middleware: ["auth", "admin"],
            title: "Tickets",
            layout: AdminLayout,
            requiresAuth: true,
        },
    },
];
