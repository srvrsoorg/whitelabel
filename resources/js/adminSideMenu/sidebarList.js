export default [
    {
        id: 1,
        name: "Dashboard",
        icon: "grid_view",
        url: "/admin",
    },
    {
        id: 2,
        name: "User Management",
        icon: "groups",
        dropIcon: "arrow_drop_down",
        url: "/admin/users",
        children: [
            {
                id: 1,
                name: "Users",
                url: "/admin/users",
            },
            {
                id: 2,
                name: "Transactions",
                url: "/admin/transaction",
            },
            {
                id: 3,
                name: "Admin Activities",
                url: "/admin/activity",
            },
        ],
    },
    {
        id: 3,
        name: "Site Settings",
        url: "/admin/billing-settings",
        icon: "settings_applications",
    },
    {
        id: 4,
        name: "Servers",
        icon: "dns",
        url: "/admin/servers",
    },
    {
        id: 5,
        name: "Integrations",
        icon: "rule_settings",
        url: "/admin/integration/cloud-platforms",
        children: [
            {
                id: 1,
                name: "Cloud Platforms",
                url: "/admin/integration/cloud-platforms",
            },
            {
                id: 2,
                name: "Payment",
                url: "/admin/integration/payment",
            },
            {
                id: 3,
                name: "SMTP",
                url: "/admin/integration/smtp",
            },
        ],
    },
    {
        id: 6,
        name: "Billing",
        icon: "lab_profile",
        url: "/admin/billing/plan",
        children: [
            {
                id: 1,
                name: "Plan",
                url: "/admin/billing/plan",
            },
            {
                id: 2,
                name: "Promo codes",
                url: "/admin/billing/promoCode",
            },
            {
                id: 3,
                name: "Settings",
                url: "/admin/billing/settings",
            },
            {
                id: 4,
                name: "Taxes",
                url: "/admin/billing/taxes",
            },
        ],
    },
    {
        id: 7,
        name: "Tickets",
        icon: "confirmation_number",
        url: "/admin/tickets",
    },
];
