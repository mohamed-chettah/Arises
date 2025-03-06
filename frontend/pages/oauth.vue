<script setup>
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

// Récupérer le token depuis l'URL
const token = route.query.token

if (token) {
  // Vérifier si l'API Chrome est disponible (l'extension est installée)
  if (window.chrome && chrome.runtime) {
    chrome.runtime.sendMessage(
        "ID_DE_TON_EXTENSION", // Remplace par ton ID d'extension
        { action: "saveToken", token: "XYZ123" }, // Envoie le token
        (response) => {
          console.log("Réponse de l'extension :", response);
        }
    );
  } else {
    console.warn("Extension non détectée");
  }

}
</script>

<template>
  <div class="oauth-container">
    <h2>Connexion en cours...</h2>
    <div v-if="token">
      <p >Votre connexion est en train d'être validée.</p>
      <p >Veuillez patienter...</p>
      <p>Maintenant vous pouvez cliquez sur l'extension et accomplir de grandes choses</p>
    </div>

    <p v-else>Erreur : aucun token trouvé.</p>
  </div>
</template>

<style scoped>
.oauth-container {
  text-align: center;
  padding: 50px;
}
</style>
