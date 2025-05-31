import type {User} from "~/types/User";

export const useCalendarStore = defineStore('calendar', {
    state: () => ({
        apiUrl: useRuntimeConfig().public.apiBase,
        event: [],
    }),
    getters: {
    },
    actions: {
        async getEvent(start: string, end: string) {
            try {
                const response = await $fetch<{user: User}>(this.apiUrl + '/calendar/events', {
                    headers: {
                        'Authorization': 'Bearer ' + useCookie('token').value,
                        'Credentials': 'include'
                    },
                    method: 'GET',
                    query: {
                        start: start,
                        end: end
                    }
                })

            } catch (error) {
                console.error('Error fetching user:', error);
            }
        }
    },
})