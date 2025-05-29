import {useAuthStore} from "~/store/AuthStore";

export default defineNuxtRouteMiddleware((to, from) => {
    const auth= useAuthStore();

    if( to.path == '/' ||to.path.startsWith('/login') ||
        to.path.startsWith('/ranking') ||
        to.path.startsWith('/what') ||
        to.path.startsWith('/terms') ||
        to.path.startsWith('/privacy')) {
        return true;
    }
    else if (!auth.isAuthenticated) {
        auth.fetchUser()
    }
})