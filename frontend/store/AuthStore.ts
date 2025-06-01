import type {User} from "~/types/User";

export const useAuthStore = defineStore('auth', {
    state: () => ({
        apiUrl: useRuntimeConfig().public.apiBase,
        isAuthenticated: false,
        user: null as User | null,
    }),
    getters: {

    },
    actions: {
        async fetchUser(token: string) {
            try {
                const response = await $fetch<{user: User}>(this.apiUrl + '/me', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Credentials': 'include'
                    }
                })

                if(response?.user) {
                    this.user = response.user
                    this.isAuthenticated = true
                } else {
                    this.user = null
                    this.isAuthenticated = false
                    return false
                }
            } catch (error) {
                this.user = null
                this.isAuthenticated = false
                return false
            }
        }
    },
})