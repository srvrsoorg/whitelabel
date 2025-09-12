import AdminLayout from "@/pages/layouts/Admin.vue";
import User from "./user";
import integrationRoutes from "./integrations";
import billingDetailsRoutes from "./billing-details";
import planManagementRoutes from "./planManagement";
import serverRoutes from "./server";
import ticketsRoutes from "./ticket";
import billingRoutes from "./billing";
function prefixRoutes(prefix, routes) {
    return routes.map((route) => {
        route.path = prefix + "" + route.path;
        return route;
    });
}

export const routes = [
    ...prefixRoutes("/admin/", [
        ...User,
        ...integrationRoutes,
        ...billingDetailsRoutes,
        ...planManagementRoutes,
        ...serverRoutes,
        ...ticketsRoutes,
        ...billingRoutes,
        {
            name: "adminDashboard",
            path: "",
            component: () => import("@/pages/admin/Dashboard.vue"),
            meta: {
                middleware: ["auth", "admin"],
                title: "Dashboard | Admin",
                setupReqiures: true,
                layout: AdminLayout,
            },
        },
        {
            name: "adminActivity",
            path: "activity",
            component: () => import("@/pages/admin/AdminActivities.vue"),
            meta: {
                middleware: ["auth", "admin"],
                title: "Activity | Admin",
                setupReqiures: true,
                layout: AdminLayout,
            },
        },
        {
            name: "adminTransaction",
            path: "transaction",
            component: () => import("@/pages/admin/AdminTransaction.vue"),
            meta: {
                middleware: ["auth", "admin"],
                title: "Transaction | Admin",
                setupReqiures: true,
                layout: AdminLayout,
            },
        },
        {
            name: "otherSettings",
            path: "other-settings",
            component: () => import("@/pages/admin/otherSettings.vue"),
            meta: {
                middleware: ["auth", "admin"],
                title: "Other Settings | Admin",
                setupReqiures: true,
                layout: AdminLayout,
            },
        },
    ]),
];

export default routes;
