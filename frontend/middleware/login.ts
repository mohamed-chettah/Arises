import {useAuthStore} from "~/store/AuthStore";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const auth = useAuthStore();
    const token = useCookie('token').value;

    if(auth.isAuthenticated) {
        return navigateTo('/dashboard');
    }

    else if(token){
        await auth.fetchUser(token);
        if(auth.isAuthenticated) {
            return navigateTo('/dashboard');
        } else {
            return navigateTo('/login');
        }
    }
})