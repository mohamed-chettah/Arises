// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  modules: ['@nuxt/ui'],
  pages: true,
  runtimeConfig: {
    public: {
      apiUrl: process.env.API_URL
    }
  },
  devServer: {
    port: 3000,  // Port interne (doit correspondre au port interne de Docker)
    host: '0.0.0.0',  // Permet d'exposer le serveur à l'extérieur du conteneur
  }
})
