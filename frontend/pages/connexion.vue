<script setup>

const route = useRoute()
const router = useRouter()

// Récupérer le token depuis l'URL
const token = route.query.token

if (token) {
  // Envoyer le token à l'extension Chrome
  setTimeout(() => {
    try {
      chrome.runtime.sendMessage({ type: "AUTH_SUCCESS", token }, (response) => {
        console.log("Réponse de l'extension :", response)
      })
    } catch (error) {
      console.error("Impossible d'envoyer le message à l'extension :", error)
    }
  }, 1000) // Petit délai pour s'assurer du chargement

  setTimeout(() => {
    window.close();
  }, 5000); // Ferme la page après 5 secondes
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
