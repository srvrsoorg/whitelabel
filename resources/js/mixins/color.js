export default {
    computed: {
        selectedColor() {
            const { siteSettings } = this;
            return siteSettings?.color_palette?.["500"] || "#6200EE";
        },
        isLightColor() {
            const hexColor = this.selectedColor.replace("#", "");
            const brightness = this.calculateColorBrightness(hexColor);
            return brightness > 125; // Return true for light colors
        },
        textColorClass() {
            return this.isLightColor ? "text-gray-800" : "text-white";
        },
        textHoverClass(){
            return this.isLightColor ? "hover:text-gray-800" : "hover:text-white";
        },
        sidebarHoverLinks() {
            return this.isLightColor
                ? "hover:text-gray-900"
                : "hover:text-custom-500";
        },
        sidebarActiveLinks() {
            return this.isLightColor
                ? "bg-custom-200 hover:!bg-custom-200 text-gray-800 hover:!text-gray-800"
                : "bg-custom-100 hover:!bg-custom-100 !text-custom-500 hover:!text-custom-500";
        },
    },
    methods: {
        calculateColorBrightness(hexColor) {
            const r = parseInt(hexColor.substring(0, 2), 16);
            const g = parseInt(hexColor.substring(2, 4), 16);
            const b = parseInt(hexColor.substring(4, 6), 16);
            return (r * 299 + g * 587 + b * 114) / 1000;
        },
    },
};
