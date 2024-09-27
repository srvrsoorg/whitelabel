import { defineStore } from 'pinia'
import axios from '@/plugins/axios'
import toast from '@/plugins/toast-notification'

export const useAuthStore = defineStore({
    id: 'auth',
    state: () => ({
        authenticated: false,
        access_token: null,
        user: null,
        is_admin: false
    }),
    actions:{
        setAuthenticated(value){
            this.authenticated = value
        },
        setAccessToken(value){
            this.access_token = value
        },
        setUser(value){
            this.user = value
        },
        setIsAdmin(value){
            this.is_admin = value
        },
        async authLogout(){
            this.authenticated = false
            this.access_token = null
            this.user = null
            this.is_admin = false
        },
        async getUser(){
            await axios.get('/me').then(({data}) => {
                this.user = data.user
            }).catch(({ response:{data} }) => {
                toast.error(data.message);
            })
        }
    },
    persist: {
        enabled: true,
        strategies:[
            {
                key:"auth",
                storage: localStorage
            },
        ]
    }
})