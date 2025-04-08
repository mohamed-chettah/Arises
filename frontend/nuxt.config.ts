// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  modules: ['@nuxt/ui', '@nuxt/image'],
  css: ['~/assets/css/main.css'],
  pages: true,
  devServer: {
    port: 3000,  // Port interne (doit correspondre au port interne de Docker)
  },
  ui: {
    colorMode: false
  },
  routeRules: {
    '/api/**': { proxy: 'https://backend.arises.app/**' },
  }
})