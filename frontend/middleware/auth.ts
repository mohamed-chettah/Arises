import {useAuthStore} from "~/store/AuthStore";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const auth = useAuthStore();
    const token = useCookie('token').value;

    if (!auth.isAuthenticated) {
        if(!token) {
            return navigateTo('/login');
        }
        await auth.fetchUser(token);

    }
})