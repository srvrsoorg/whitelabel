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
        this.getTransaction()
    },
    methods:{
        ...mapActions(useAuthStore, ['getUser']),
        async getTransaction() {
            await this.$axios.get(`/user/transactions/${this.$route.params.key}`)
                .then(({ data }) => {
                    this.transaction = data.transaction
                    this.verifyPayment()
                }).catch(({ response: { data } }) => {
                    this.$toast.error(data.message)
                })
        },
        async verifyPayment(){
            var params = null
            if(this.transaction.payment_gateway === 'Paypal'){
                params = {
                    payer_id: this.$route.query.PayerID,
                    token: this.$route.query.token
                }
            }else if(this.transaction.payment_gateway === 'Stripe'){
                params = {
                    session_id: this.$route.query.session_id
                }
            } 

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
            await this.$axios.post(`/user/transactions/verify/${this.$route.params.key}`,params).then(response => {
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