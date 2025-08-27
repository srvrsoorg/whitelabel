import GuestLayout from "@/pages/layouts/Guest.vue";
export default [
    {
        path: "/register",
        name: "register",
        component: () => import("@/pages/guest/Register.vue"),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Register`, // Title for the route
            layout: GuestLayout,
            setupReqiures: true,
        },
    },
    {
        path: "/login",
        name: "login",
        component: () => import("@/pages/guest/Login.vue"),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Login`, // Title for the route
            layout: GuestLayout,
            setupReqiures: true,
        },
    },
    {
        path: "/two-factor-authentication",
        name: "Tfa",
        component: () => import("@/pages/guest/TfaVerify.vue"),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Two Factor Authentication`, // Title for the route
            layout: GuestLayout,
            setupReqiures: true,
        },
    },
    {
        path: "/forgot-password",
        name: "forgotPassword",
        component: () => import("@/pages/guest/ForgotPassword.vue"),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Forgot Password`, // Title for the route
            layout: GuestLayout,
            setupReqiures: true,
        },
    },
    {
        path: "/reset-password/:token",
        name: "resetPassword",
        component: () => import("@/pages/guest/ResetPassword.vue"),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Reset Password`, // Title for the route
            layout: GuestLayout,
            setupReqiures: true,
        },
    },
    {
        path: "/setup/database",
        name: "setupDatabase",
        component: () => import("@/pages/guest/setup/Database.vue"),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Setup Database`, // Title for the route
            layout: GuestLayout,
            setupReqiures: false,
        },
    },
    {
        path: "/setup/key-verification",
        name: "keyVerification",
        component: () => import("@/pages/guest/setup/KeyVerification.vue"),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Key Verification`, // Title for the route
            layout: GuestLayout,
            setupReqiures: false,
        },
    },
    {
        path: "/setup/permissions",
        name: "checkPermissions",
        component: () => import("@/pages/guest/setup/Permissions.vue"),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Permissions`, // Title for the route
            layout: GuestLayout,
            setupReqiures: false,
        },
    },
    {
        path: "/setup/smtp",
        name: "setupSmtp",
        component: () => import("@/pages/guest/setup/SMTP.vue"),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Setup SMTP Credentials`, // Title for the route
            layout: GuestLayout,
            setupReqiures: false,
        },
    },
    {
        path: "/setup/register",
        name: "setupRegister",
        component: () => import("@/pages/guest/setup/AdminRegister.vue"),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Admin Registration`, // Title for the route
            layout: GuestLayout,
            setupReqiures: false,
        },
    },
    {
        path: "/verify-transaction/:key",
        name: "verifyTransaction",
        component: () => import("@/pages/guest/TransactionVerify.vue"),
        meta: {
            title: `Verify Transaction`, // Title for the route
            setupReqiures: true,
        },
    },
    {
        path: "/verify-paytr-transaction",
        name: "verifyPaytrTransaction",
        component: () => import("@/pages/guest/TransactionVerifyPaytr.vue"),
        meta: {
            title: `Verify Transaction`, // Title for the route
            setupReqiures: true,
        },
    },
    {
        path: "/:pathMatch(.*)*",
        name: "404",
        component: () => import("@/pages/PageNotFound.vue"),
        meta: {
            layout: GuestLayout,
            title: "Page Not Found",
        },
    },
    {
        path: "/verify/:key",
        name: "userVerification",
        component:()=>import('@/pages/guest/UserVerification.vue'),
        meta: {
            title: `Verify User`,
        },
    },
];
