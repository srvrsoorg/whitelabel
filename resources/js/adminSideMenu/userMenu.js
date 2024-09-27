export default [
    {
        name: "Profile",
        url: "/admin/users/{id}",
        icon: "account_circle",
    },
    {
        name: "Security",
        url: "/admin/users/{id}/two-factor-authentication",
        icon: "verified_user",
    },
    {
        name: "Servers",
        url: "/admin/users/{id}/servers",
        icon: "dns",
    },
    {
        name: "Login History",
        url: "/admin/users/{id}/login-history",
        icon: "history",
    },
    {
        name: "Activities",
        url: "/admin/users/{id}/activities",
        icon: "event_note",
    },
    {
        name: "Transactions",
        url: "/admin/users/{id}/transactions",
        icon: "universal_currency",
    },
    {
        name: "Usage Summary",
        url: "/admin/users/{id}/usage-summary",
        icon: "request_quote",
    },
];
