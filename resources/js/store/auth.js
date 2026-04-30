import { defineStore } from 'pinia'
import toast from '@/plugins/toast-notification'

export const useAuthStore = defineStore({
    id: 'auth',
    state: () => ({
        authenticated: false,
        access_token: null,
        user: null,
        is_admin: false,
        switched_from_admin: false
    }),
    actions:{
        setAuthenticated(value){
            this.authenticated = value
        },
        setAccessToken(value){
            this.access_token = value
            this.switched_from_admin = false
        },
        setUser(value){
            this.user = value
        },
        setIsAdmin(value){
            this.is_admin = value
        },
        setSwitchedFromAdmin(value){
            this.switched_from_admin = value
        },
        applySession({ token, user }){
            this.setAccessToken(token)
            this.setUser(user)
            this.setIsAdmin(user?.is_admin || false)
            this.setSwitchedFromAdmin(user?.switched_from_admin || false)
            this.setAuthenticated(true)
        },
        async authLogout(){
            this.authenticated = false
            this.access_token = null
            this.user = null
            this.is_admin = false
            this.switched_from_admin = false
        },
        async getUser(){
            const { default: axios } = await import('@/plugins/axios')
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