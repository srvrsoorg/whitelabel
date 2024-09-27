import { defineStore } from 'pinia'
import axios from '@/plugins/axios'

export const useSetupStore = defineStore({
    id: 'setup',
    state: () => ({
        setupComplete: false,
        smtpComplete: false,
        permissionComplete: false,
        databaseComplete: false,
        keyVerificationComplete: false,
        registerComplete: false
    }),
    actions: {
        async getSetupStatus(){
            await axios.get('/installation-steps').then(({data}) => {
                this.setupComplete = data.site_setting
                this.smtpComplete = data.smtp
                this.registerComplete = data.register,
                this.keyVerificationComplete = data.license_key,
                this.databaseComplete = data.database
                this.permissionComplete = data.storage_permission
            }).catch(() => {})
        },
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
