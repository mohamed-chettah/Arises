import {useAuthStore} from "~/store/AuthStore";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const auth = useAuthStore();
    const token = useCookie('token').value;

    if(to.path === '/login') {
        if (!auth.isAuthenticated && token) {
            if(!token) {
                return navigateTo('/login');
            }
            console.log('Fetching user for authenticated session from middleware...')
            await auth.fetchUser(token);
        }
        else if(auth.isAuthenticated) {
            return navigateTo('/dashboard');
        }
        else {
            return true;
        }
    }

    else if (!auth.isAuthenticated) {
        if(!token) {
            return navigateTo('/login');
        }
        await auth.fetchUser(token);

    }
})