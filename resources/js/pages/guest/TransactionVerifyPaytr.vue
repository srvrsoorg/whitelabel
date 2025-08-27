<template>
    <div class="bg-[#FCFEFF] flex flex-col h-full min-h-screen">
        <div class="flex flex-1 items-center justify-center h-full">
            <div
                class="p-5 w-full max-w-xl bg-white rounded-lg border shadow-md sm:p-6 md:p-8">
                    <div>
                        <img
                            class="w-60 mx-auto"
                            src="/images/transaction-verify.png"
                        />
                        <p class="mt-12 text-2xl font-medium text-center">Payment Verification in Progress!</p>
                    </div>
                <p class="mt-6 text-center font-normal">Please wait while we verify the transaction and update your account.</p>
                <p class="mt-2 text-center font-normal">It may take few more seconds.</p>
            </div>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from '@/store/auth'
import { mapActions, mapState } from 'pinia'
export default {
    data(){
        return{
            transaction: null
        }
    },
    computed: {
        ...mapState(useAuthStore, ['authenticated'])
    },
    mounted(){
        this.verifyPayment()
    },
    methods:{
        ...mapActions(useAuthStore, ['getUser']),
    
        async verifyPayment(){

            var redirectUrl = null
            if(this.authenticated){
                redirectUrl = {
                    name:"transactions"
                }
            }else{
                redirectUrl = {
                    name:"login"
                }
            }
            await this.$axios.post(`/payment/paytr/verify`).then(response => {
                this.$toast.success(response.data.message)
                this.getUser()
            }).catch(error => {
                this.$toast.error(error.response.data.message)
            }).finally(()=>{
                setTimeout(() => {
                    this.$router.push(redirectUrl)
                }, 3000)
            })
        }
    }
}
</script>