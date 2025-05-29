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
        fetchUser() {
            try {
                const { data } = useFetch<{user: User}>(this.apiUrl + '/me', {
                    onRequest({ request, options }) {
                        options.headers.set('Authorization', 'Bearer ' + useCookie('token').value || '');
                    },
                })
                if(data.value?.user) {
                    this.user = data.value.user
                }
                this.isAuthenticated = true
            } catch (error) {
                this.user = null
                this.isAuthenticated = false
                return navigateTo('/login');
            }
        }
    },
})