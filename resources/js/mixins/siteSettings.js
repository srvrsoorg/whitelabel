export default {
    data() {
        return {
            showLoader: false,
            openConfirmation: false,
        };
    },
    methods: {
        openConfirmationModal() {
            this.openConfirmation = true;
        },
        closeModal() {
            this.openConfirmation = false;
            this.showLoader = false;
        },
        handleSiteLogoUpload() {
            this.payload.logo = this.$refs.logo.files[0];
            let reader = new FileReader();
            reader.onload = (e) => {
                this.logo = e.target.result;
            };
            reader.readAsDataURL(this.$refs.logo.files[0]);
            document.getElementById("logo").value = "";
        },
        handleSmallLogoUpload() {
            this.payload.icon = this.$refs.icon.files[0];
            let reader = new FileReader();
            reader.onload = (e) => {
                this.icon = e.target.result;
            };
            reader.readAsDataURL(this.$refs.icon.files[0]);
            document.getElementById("icon").value = "";
        },
        handleFaviconUpload(e) {
            this.payload.favicon = this.$refs.favicon.files[0];
            let reader = new FileReader();
            reader.onload = (e) => {
                this.favicon = e.target.result;
            };
            reader.readAsDataURL(this.$refs.favicon.files[0]);
            document.getElementById("favicon").value = "";
        },

        // Save Site Settings
        async saveSiteSettings() {
            this.showLoader = true;
            this.hideError();

            var formData = new FormData();
            formData.append("app_name", this.payload.app_name);
            formData.append("analytics", this.payload.analytics);
            formData.append("tag_line", this.payload.tag_line);
            formData.append(
                "color_code",
                this.payload.color_code.replace("#", "")
            );
            if (this.payload.redis_password) {
                formData.append("redis_password", this.payload.redis_password);
            }
            formData.append("sa_org_id", this.payload.sa_org_id);

            if (this.$route.meta.isAdminPage) {
                formData.append("header", this.payload.header);
                formData.append("footer", this.payload.footer);
                formData.append("_method", "PATCH");
            }

            if (
                this.payload.logo != null &&
                typeof this.payload.logo !== "string"
            ) {
                formData.append("logo", this.payload.logo);
            } else {
                formData.append("logo", "");
            }

            if (
                this.payload.icon != null &&
                typeof this.payload.icon !== "string"
            ) {
                formData.append("icon", this.payload.icon);
            } else {
                formData.append("icon", "");
            }

            if (
                this.payload.favicon != null &&
                typeof this.payload.favicon !== "string"
            ) {
                formData.append("favicon", this.payload.favicon);
            } else {
                formData.append("favicon", "");
            }

            this.$axios
                .post("/admin/site-setting", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then(({ data }) => {
                    this.closeModal();
                    this.$toast.success(data.message);
                    if (this.$route.name == "setupSiteSettings") {
                        window.location.href = "/admin";
                        // this.$router.push({
                        //     name: 'adminDashboard'
                        // })
                    } else {
                        location.reload();
                    }
                })
                .catch(({ response }) => {
                    if (response !== undefined) {
                        const { status, data } = response;
                        this.closeModal();
                        if (status === 422) {
                            this.displayError(data);
                        } else {
                            this.$toast.error(data.message);
                        }
                    }
                })
        },
    },
};
