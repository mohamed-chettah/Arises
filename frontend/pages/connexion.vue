<script setup>
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

// Récupérer le token depuis l'URL
const token = route.query.token

if (token) {
  // Supprime le token de l'URL pour la sécurité
  window.history.replaceState({}, document.title, "/oauthConnexion")

  // Envoyer le token à l'extension via window.postMessage
  setTimeout(() => {
    window.postMessage({ type: "AUTH_SUCCESS", token }, "*")
  }, 1000)

  // Optionnel : Fermer la page après quelques secondes
  setTimeout(() => {
    window.close()
  }, 5000)
}
</script>

<template>
  <div class="oauth-container">
    <h2>Connexion en cours...</h2>
    <p v-if="token">Votre connexion est en train d'être validée.</p>
    <p v-else>Erreur : aucun token trouvé.</p>
  </div>
</template>

<style scoped>
.oauth-container {
  text-align: center;
  padding: 50px;
}
</style>
