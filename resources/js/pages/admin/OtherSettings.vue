<template>
    <Breadcrumb :breadcrumb="breadcrumb" />
    <div class="text-xl whitespace-nowrap text-[#31363f] font-medium my-2.5">
        Other Settings
    </div>
    <div class="my-5 mt-8 rounded-md bg-white border relative">
        <div class="-left-5 p-2 bg-white -top-5 absolute flex justify-center items-center gap-2">
            <span
                :class="[
                    isLightColor ? 'text-custom-700' : 'text-custom-500',
                    'material-symbols-outlined',
                ]"
            >
                link
            </span>
            <p class="font-medium">Links</p>
        </div>
        <div class="grid sm:grid-cols-2 grid-cols-1 md:mt-6 xl:gap-x-32 2xl:gap-x-52 sm:gap-x-14 gap-2.5 px-7 xl:mb-6 mb-3 mt-6 sm:pt-2">
            <div>
                <div class="grid xl:grid-cols-12 2xl:grid-cols-11 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1">
                    <div class="xl:col-span-5 2xl:col-span-4 xl:pt-2 sm:col-span-3 col-span-12">
                        <label class="font-medium text-nowrap text-tiny flex items-center gap-1">
                            Terms Link
                            <span
                                v-tooltip="`Add a link to your site’s Terms & Conditions. If set, it will appear on the Register page.`"
                                :class="[isLightColor ? 'text-custom-700' : 'text-custom-500','material-symbols-outlined text-[16px] cursor-pointer' ]"
                            >
                                info
                            </span>
                        </label>
                    </div>
                    <div class="xl:col-span-7 2xl:col-span-7 sm:col-span-12 col-span-12">
                        <input
                            v-model="payload.terms_condition"
                            type="text"
                            name="terms_condition"
                            id="terms_condition"
                            class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                            placeholder="Enter Terms Link"
                        />
                    </div>
                </div>
                <div class="grid xl:grid-cols-12 2xl:grid-cols-11 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1">
                    <div class="xl:col-span-5 2xl:col-span-4 xl:pt-2 sm:col-span-3 col-span-12"></div>
                    <div class="xl:col-span-7 2xl:col-span-7 sm:col-span-12 col-span-12">
                        <small
                            id="terms_condition_message"
                            class="text-red-500 error_message text-xs"
                        ></small>
                    </div>
                </div>
            </div>
            <div>
                <div class="grid xl:grid-cols-12 2xl:grid-cols-11 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1">
                    <div class="xl:col-span-5 2xl:col-span-4 xl:pt-2 sm:col-span-3 col-span-12">
                        <label class="font-medium text-nowrap text-tiny flex items-center gap-1">
                            Privacy Policy Link
                            <span
                                v-tooltip="`Add a link to your site’s Privacy Policy. If set, it will appear on the Register page.`"
                                :class="[isLightColor ? 'text-custom-700' : 'text-custom-500','material-symbols-outlined text-[16px] cursor-pointer' ]"
                            >
                                info
                            </span>
                        </label>
                    </div>
                    <div class="xl:col-span-7 2xl:col-span-7 sm:col-span-12 col-span-12">
                        <input
                            v-model="payload.privacy_policy"
                            type="text"
                            name="privacy_policy"
                            id="privacy_policy"
                            class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                            placeholder="Enter Privacy Policy Link"
                        />
                    </div>
                </div>
                <div class="grid xl:grid-cols-12 2xl:grid-cols-11 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1">
                    <div class="xl:col-span-5 2xl:col-span-4 xl:pt-2 sm:col-span-3 col-span-12"></div>
                    <div class="xl:col-span-7 2xl:col-span-7 sm:col-span-12 col-span-12">
                        <small
                            id="privacy_policy_message"
                            class="text-red-500 error_message text-xs"
                        ></small>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid sm:grid-cols-2 grid-cols-1 xl:gap-x-32 2xl:gap-x-52 sm:gap-x-14 gap-2.5 px-7 xl:mb-6 mb-4">
            <div>
                <div class="grid xl:grid-cols-12 2xl:grid-cols-11 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1">
                    <div class="xl:col-span-5 2xl:col-span-4 xl:pt-2 sm:col-span-3 col-span-12">
                        <label class="font-medium text-nowrap text-tiny flex items-center gap-1">
                            Refund Policy Link
                            <span
                                v-tooltip="`Add a link to your site’s Refund Policy. If set, it will appear on the Checkout page.`"
                                :class="[isLightColor ? 'text-custom-700' : 'text-custom-500','material-symbols-outlined text-[16px] cursor-pointer' ]"
                            >
                                info
                            </span>
                        </label>
                    </div>
                    <div class="xl:col-span-7 2xl:col-span-7 sm:col-span-12 col-span-12">
                        <input
                            v-model="payload.refund_policy"
                            type="text"
                            name="refund_policy"
                            id="refund_policy"
                            class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                            placeholder="Enter Refund Policy Link"
                        />
                    </div>
                </div>
                <div class="grid xl:grid-cols-12 2xl:grid-cols-11 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1">
                    <div class="xl:col-span-5 2xl:col-span-4 xl:pt-2 sm:col-span-3 col-span-12"></div>
                    <div class="xl:col-span-7 2xl:col-span-7 sm:col-span-12 col-span-12">
                        <small
                            id="refund_policy_message"
                            class="text-red-500 error_message text-xs"
                        ></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-row-reverse">
        <div class="text-end rounded-md">
            <Button :disabled="processing" @click="saveSettings">
            <i
                v-if="processing"
                class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
            ></i>
                {{ processing ? "Please wait" : "Save Settings" }}
            </Button>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            breadcrumb: {
                icon: "settings",
                pages: [
                    {
                        name: "Other Settings",
                    },
                ],
            },
            payload: {
                terms_condition: '',
                privacy_policy: '',
                refund_policy: ''
            },
            processing: false
        }
    },
    mounted(){
        this.fetchData()
    },
    methods: {
        async fetchData(){
            await this.$axios.get(`/admin/links`).then(({data}) => {
                this.payload.terms_condition = data.terms_condition
                this.payload.privacy_policy = data.privacy_policy
                this.payload.refund_policy = data.refund_policy
            }).catch(({ response }) => {
                this.$toast.error(response.data.message);
            });
        },
        async saveSettings(){
            this.processing = true
            await this.$axios.post(`/admin/links`, this.payload).then(({data}) => {
                this.$toast.success(data.message)
                this.fetchData()
            }).catch(({ response }) => {
                if(response.status === 422) {
                    this.displayError(response.data);
                }else{
                    this.$toast.error(response.data.message);
                }
            }).finally(() => {
                this.processing = false
            });
        }
    }
}
</script>