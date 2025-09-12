import userLayout from "@/pages/layouts/User.vue";
export default[
    {
        path:'/account/profile',
        name:'AccountInformation',
        component: () => import('@/pages/account/AccountInformation.vue'),
        meta: {
            middleware: ["auth"], 
            layout: userLayout,
            title: `Profile | Account`, // Title for the route
            setupReqiures: true,
        }
    },
    {
        path: "/account/login-history",
        name: "LoginHistory",
        component: () => import("@/pages/account/LoginHistory.vue"),
        meta: {
            middleware: ["auth"],
            layout: userLayout,
            title: `Login History | Account`, // Title for the route
            setupReqiures: true,
        },
    },
    {
        path: "/account/activities",
        name: "AccountActivity",
        component: () => import("@/pages/account/Activity.vue"),
        meta: {
            middleware: ["auth"],
            layout: userLayout,
            title: `Activities | Account`, // Title for the route
            setupReqiures: true,
        },
    },
    {
        path:'/account/security',
        name:'AccountSecurity',
        component: () => import('@/pages/account/TwoFactorAuthentication.vue'),
        meta: {
            middleware: ["auth"], 
            layout: userLayout,
            title: `Security | Account`, // Title for the route
            setupReqiures: true,
        }
    },
    {
        path:'/account/settings',
        name:'AccountSettings',
        component: () => import('@/pages/account/Settings.vue'),
        meta: {
            middleware: ["auth"], 
            layout: userLayout,
            title: `Settings | Account`, // Title for the route
            setupReqiures: true,
        }
    }
];