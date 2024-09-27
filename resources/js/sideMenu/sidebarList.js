export default [
    {
        id: "1",
        name: "Servers",
        url: "/",
        icon: "dns",
    },
    {
        id: "2",
        name: "Billing",
        url: "/billing/wallet",
        icon: "lab_profile",
        children: [
            {
                id: 1,
                name: "Wallet",
                url: "/billing/wallet",
                parentId: 2,
            },
            {
                id: 2,
                name: "Transactions",
                url: "/billing/transactions",
                parentId: 2,
            },
            {
                id: 3,
                name: "Settings",
                url: "/billing/settings",
                parentId: 2,
            },
        ],
    },
    {
        id: "3",
        name: "Account",
        url: "/account/profile",
        icon: "account_box",
        children: [
            {
                id: 1,
                name: "Profile",
                url: "/account/profile",
                parentId: 3,
            },
            {
                id: 2,
                name: "Security",
                url: "/account/security",
                parentId: 3,
            },
            {
                id: 3,
                name: "Login History",
                url: "/account/login-history",
                parentId: 3,
            },
            {
                id: 4,
                name: "Activities",
                url: "/account/activities",
                parentId: 3,
            },
        ],
    },
    {
        id: "4",
        name: "Tickets",
        url: "/tickets",
        icon: "confirmation_number",
    },
];
