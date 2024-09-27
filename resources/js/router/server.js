import userLayout from "@/pages/layouts/User.vue";
export default [
    {
        name: "serverConnect",
        path: "/server/connect",
        component: () => import("@/pages/ConnectServer.vue"),
        meta: {
            middleware: ["auth"],
            title: `Connect | Server`,
            layout: userLayout,
            setupReqires: true,
        },
    },
    {
        path: '/server/:server/status',
        name: "InstallationStatus",
        component: () => import("@/pages/InstallationStatus.vue"),
        meta: {
                  middleware: ["auth"],
                  title: `Installation Status | Server`,
                  layout: userLayout,
                  setupRequires: true,
                },
      },
    
];
