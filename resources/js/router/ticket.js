import userLayout from "@/pages/layouts/User.vue";
export default [
    {
        path: "/tickets",
        name: "tickets",
        component: () => import("@/pages/Tickets/Ticket.vue"),
        meta: {
            layout: userLayout,
            title: "Tickets",
            requiresAuth: true,
        },
    },
    {
        path: "/tickets/:id",
        name: "ticketshow",
        component: () => import("@/pages/Tickets/TicketShow.vue"),
        meta: {
            layout: userLayout,
            title: "Ticket",
            requiresAuth: true,
        },
    },
];
