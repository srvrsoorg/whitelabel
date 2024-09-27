<template>
    <span
        :class="badgeClasses"
        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
    >
        {{ badgeTitle }}
    </span>
</template>

<script>
export default{
    props: {
        badgeTitle: {
            required: true,
            type: [String, Number],
        },
        rounded: {
            type: Boolean,
            default: false,
        },
        variant: {
            type: String,
            default: "primary",
        },
    },
    computed: {
        badgeClasses() {
            return {
                "bg-red-50 text-red-600 ring-red-600/20":this.variant === "danger",
                "bg-blue-50 text-blue-600 ring-blue-700/20":this.variant === "primary",
                "bg-yellow-50 text-yellow-600 ring-yellow-600/20":this.variant === "warning",
                "bg-green-50 text-green-600 ring-green-600/20":this.variant === "success",
                "bg-gray-50 text-gray-600 ring-gray-500/10":this.variant === "dark",
                "rounded-full": this.rounded,
                "rounded-md": !this.rounded,
            };
        },
    },
}
</script>
