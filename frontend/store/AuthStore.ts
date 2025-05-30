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
        async fetchUser() {
            try {
                const { data, status, error, refresh, clear } = await useFetch<{user: User}>(this.apiUrl + '/me', {
                    onRequest({ request, options }) {
                        options.headers.set('Authorization', 'Bearer ' + useCookie('token').value || '');
                        options.headers.set('Credentials', 'include');
                    },
                })

                console.log('Fetching user data from API:', this.apiUrl + '/me', data, status, error, refresh, clear)

                if(data.value?.user) {
                    this.user = data.value.user
                    this.isAuthenticated = true
                }

                else if(status.value != 'success') {
                    this.user = null
                    this.isAuthenticated = false
                    return navigateTo('/login');
                }
            } catch (error) {
                this.user = null
                this.isAuthenticated = false
                return navigateTo('/login');
            }
        }
    },
})