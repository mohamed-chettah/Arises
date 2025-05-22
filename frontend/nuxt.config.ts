// https://nuxt.com/docs/api/configuration/nuxt-config
import {process} from "std-env";

export default defineNuxtConfig({
  ssr: true,
  nitro: {
    preset: 'node-server',
  },
  devtools: { enabled: false },
  modules: ['@nuxt/ui', '@nuxt/image'],
  css: ['~/assets/css/main.css'],
  pages: true,
  devServer: {
    port: 3000,  // Port interne (doit correspondre au port interne de Docker)
  },
  ui: {
    colorMode: false
  },
  runtimeConfig: {
    public: {
      apiBase: process.env.API_URL
    }
  },
})