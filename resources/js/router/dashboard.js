import userLayout from "@/pages/layouts/User.vue";
export default [
    {
        path: "/",
        name: "dashboard",
        component: () => import("@/pages/Dashboard.vue"),
        meta: {
            middleware: ["auth"],
            title: `Dashboard`, // Title for the route
            layout: userLayout,
            setupReqiures: true,
        },
    }
];
