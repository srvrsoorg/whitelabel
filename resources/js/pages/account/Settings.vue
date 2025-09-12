<template>
    <Breadcrumb :breadcrumb="breadcrumb" />
    <div class="my-6 ml-3 rounded-md bg-white border relative md:w-4/5 xl:w-3/5 ">
        <div class="-left-5 p-2 pr-3.5 bg-white -top-5 absolute flex justify-center items-center gap-2">
            <span :class="[ isLightColor ? 'text-custom-700' : 'text-custom-500', 'material-symbols-outlined']" >
                settings
            </span>
            <p class="font-medium text-lg">Confirmation Popup Timer</p>
        </div>
        <div class="p-5">
            <div class="">
                    <p class="text-gray-500 text-sm mt-1 mb-4">
                       Customize the countdown timer for confirmation popups by setting a delay between 0-5 seconds. This setting applies only to popups that include a timer, allowing you to control how long you must wait before confirming an action.
                    </p>
                        <div class="w-full">
                            <div>
                                <div
                                    class="grid xl:grid-cols-10 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
                                >
                                    <div
                                        class="xl:col-span-4 sm:col-span-3 col-span-12"
                                    >
                                        <label
                                            for="secs"
                                            class="font-medium text-nowrap text-tiny"
                                            >Confirmation Timer (Seconds)</label
                                        >
                                    </div>

                                    <div
                                        class="xl:col-span-6 sm:col-span-8 col-span-12"
                                    >
                                        <select
                                            id="secs"
                                            v-model="secs"
                                            class="mt-1 w-full cursor-pointer text-sm text-gray-900 border border-slate-300 focus:ring-0 focus:border-sa-500 rounded-md py-2.5 focus:outline-none"
                                        >
                                            <option value="0">0 Second</option>
                                            <option value="1">1 Second</option>
                                            <option value="2">2 Second</option>
                                            <option value="3">3 Second</option>
                                            <option value="4">4 Second</option>
                                            <option value="5">5 Second</option>
                                        </select>
                                        <small
                                            class="text-red-500 error_message"
                                            id="secs_message"
                                        ></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-5">
                            <button @click="openConfirmationModal()" type="button" class="border rounded-md px-5 py-2 text-center text-sm">
                                Show Demo
                            </button>
                            <Button @click="updateTimer()" :disabled="updating">
                                <i  v-if="updating" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                                {{ updating ? "Please wait" : "Update" }}
                            </Button>
                        </div>
            </div>
        </div>
    </div>
    <Confirmation
        confirmationTitle="Demo Popup"
        submitBtnTitle="Yes, I'm sure"
        :showLoader="showConfirmLoader"
        :show="showConfirmation"
        @confirm="updateTimer"
        :timerValue="secs"
        @closeModal="closeConfirmationModal"
    >
        <template #icon>
            <i
                class="fas fa-warning h-6 w-6 text-xl text-red-600 text-center flex items-center justify-center"
                aria-hidden="true"
            ></i>
        </template>
        <template #content>
            <p class="text-sm text-gray-600">
                <span>This is a demo popup to preview the confirmation process.</span>
            </p>
            <p class="text-sm text-gray-600 mt-1">
                <span>Are you sure you want to move forward?</span>
            </p>
        </template>
    </Confirmation>
</template>

<script>
import { useAuthStore } from "@/store/auth";

export default {
    data() {
        return {
            breadcrumb: {
                icon: "account_box",
                pages: [{ name: "Account" }, { name: "Settings" }],
            },
            secs: 5,
            updating: false,
            showConfirmLoader: false,
            showConfirmation: false,
        };
    },
    watch: {
        user: {
            handler(val) {
                this.secs =val && (val.confirmation_timer || val.confirmation_timer == 0) ? val.confirmation_timer: 5;
            },
        },
    },
    computed: {
        authStore() {
            return useAuthStore();
        },
        user() {
            return this.authStore.user;
        },
    },
    mounted() {
        this.secs = this.user && this.user.confirmation_timer ? this.user.confirmation_timer : 5;
    },
    methods: {
        openConfirmationModal() {
            this.showConfirmation = !this.showConfirmation;
        },
        closeConfirmationModal() {
            this.showConfirmLoader = false;
            this.showConfirmation = false;
        },
        async updateTimer() {
            this.updating = true;
            this.showConfirmLoader = true;
            this.hideError();

            await this.$axios.patch(`/user/confirmation-timer`, {confirmation_timer: this.secs,}).then((response) => {
                    this.authStore.getUser();
                    this.$toast.success(response.data.message);
                }).catch(({ response }) => {
                    if (response !== undefined) {
                        const { status, data } = response;
                        if (status === 422) {
                            this.displayError(data);
                        } else {
                            this.$toast.error(data.message);
                        }
                    }
                }).finally(() => {
                    this.updating = false;
                    this.closeConfirmationModal();
                });
        },
    },
};
</script>
