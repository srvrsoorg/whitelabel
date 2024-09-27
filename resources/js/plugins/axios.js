import axios from "axios";
import { useAuthStore } from "@/store/auth";
import Router from '@/router'

// Create an Axios instance with the base URL for the API
const instance = axios.create({
    baseURL: `${window.location.protocol}//${window.location.host}/api`,
});

// Request interceptor to set Authorization header with the access token
instance.interceptors.request.use(
    (config) => {
        let token = useAuthStore().access_token;
        if (token) {
            config.headers["Authorization"] = `Bearer ${token}`; // Set Authorization header
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor to handle responses and potential errors
instance.interceptors.response.use(
    (response) => response,
    (error) => {
        
        if (error.response.status === 401 || error.response.status === 403) {
            // Log out the user in case of unauthorized (401) or forbidden (403) responses.
            useAuthStore().authLogout();
                Router.push({
                    name: "login",
                });
        }
        throw error;
    }
);

export default instance;
