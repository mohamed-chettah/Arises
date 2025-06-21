<script setup lang="ts">
import { onMounted } from 'vue'
import { useUserStore } from '~/store/UserStore'

// **🔥 META DE LA PAGE**
definePageMeta({
  layout: 'dashboard',
  middleware: 'auth'
})

// **🔥 STORE UTILISATEUR**
const userStore = useUserStore()

// **🔥 NAVIGATION VERS LE DASHBOARD**
function goToDashboard() {
  navigateTo('/dashboard')
}

// **🔥 LIFECYCLE**
onMounted(async () => {
  // Récupérer les données utilisateur si pas déjà chargées
  if (!userStore.user) {
    await userStore.fetchProfile()
  }
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-6">
      
      <!-- **🔥 HEADER DE LA PAGE AVEC BOUTON RETOUR** -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center space-x-4">
            <UButton
              variant="ghost"
              size="sm"
              icon="i-heroicons-arrow-left"
              @click="goToDashboard"
              class="hover:bg-purple-50 hover:text-purple-600 transition-colors"
            >
              Retour au dashboard
            </UButton>
          </div>
          
          <!-- Badge utilisateur connecté -->
          <div v-if="userStore.user" class="flex items-center space-x-2 px-3 py-1.5 bg-green-50 text-green-700 rounded-full text-sm">
            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
            <span class="font-medium">Connecté</span>
          </div>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Profil</h1>
        <p class="text-gray-600">Gérez vos informations personnelles et préférences</p>
      </div>

      <div v-if="userStore.loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
      </div>

      <div v-else-if="userStore.user" class="space-y-6">
        
        <!-- **🔥 CARTE INFORMATIONS PERSONNELLES** -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Informations personnelles</h2>
            <UButton
              variant="outline"
              size="sm"
              class="text-sm"
            >
              Modifier
            </UButton>
          </div>

          <div class="flex items-start space-x-6">
            <!-- Avatar -->
            <div class="flex-shrink-0">
              <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white text-2xl font-semibold">
                {{ userStore.userInitials }}
              </div>
            </div>

            <!-- Informations -->
            <div class="flex-1 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                <p class="text-lg font-medium text-gray-900">{{ userStore.user.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                <p class="text-lg text-gray-900">{{ userStore.user.email }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Membre depuis</label>
                <p class="text-sm text-gray-600">{{ userStore.formattedJoinDate }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- **🔥 CARTE PLAN D'ABONNEMENT** -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Plan d'abonnement</h2>
            <UButton
              variant="outline"
              size="sm"
              class="text-sm"
              disabled
            >
              Gérer
            </UButton>
          </div>

          <!-- **🔥 AFFICHAGE DU PLAN SI DISPONIBLE** -->
          <div v-if="userStore.user.plan" class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-200">
            <div>
              <h3 class="font-semibold text-gray-900">{{ userStore.user.plan.name }}</h3>
              <p class="text-sm text-gray-600">
                Statut: <span class="font-medium text-green-600">{{ userStore.user.plan.status }}</span>
              </p>
            </div>
            <div class="flex items-center space-x-2">
              <div class="w-3 h-3 bg-green-500 rounded-full"></div>
              <span class="text-sm font-medium text-gray-700">Actif</span>
            </div>
          </div>

          <div v-else class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
              <Icon name="i-heroicons-credit-card" class="w-8 h-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Gestion des abonnements</h3>
            <p class="text-gray-600 mb-4">Cette section sera bientôt disponible</p>
            <p class="text-sm text-gray-500">
              Vous pourrez gérer votre plan, facturation et historique des paiements
            </p>
          </div>
        </div>

        <!-- **🔥 CARTE PRÉFÉRENCES** -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">Préférences</h2>
          
          <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
              <div>
                <h3 class="font-medium text-gray-900">Notifications par email</h3>
                <p class="text-sm text-gray-600">Recevoir des notifications pour vos événements</p>
              </div>
              <UToggle disabled />
            </div>

            <div class="flex items-center justify-between py-3 border-b border-gray-100">
              <div>
                <h3 class="font-medium text-gray-900">Mode sombre</h3>
                <p class="text-sm text-gray-600">Utiliser le thème sombre pour l'interface</p>
              </div>
              <UToggle disabled />
            </div>

            <div class="flex items-center justify-between py-3">
              <div>
                <h3 class="font-medium text-gray-900">Synchronisation automatique</h3>
                <p class="text-sm text-gray-600">Synchroniser automatiquement avec Google Calendar</p>
              </div>
              <UToggle disabled />
            </div>
          </div>
        </div>

        <!-- **🔥 CARTE SÉCURITÉ** -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">Sécurité</h2>
          
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-medium text-gray-900">Mot de passe</h3>
                <p class="text-sm text-gray-600">Dernière modification il y a 30 jours</p>
              </div>
              <UButton
                variant="outline"
                size="sm"
                disabled
              >
                Modifier
              </UButton>
            </div>

            <div class="pt-4 border-t border-gray-100">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-medium text-red-600">Déconnexion</h3>
                  <p class="text-sm text-gray-600">Se déconnecter de votre compte sur cet appareil</p>
                </div>
                <UButton
                  color="error"
                  variant="outline"
                  size="sm"
                  :loading="userStore.loading"
                  @click="userStore.logout"
                >
                  <Icon name="i-heroicons-arrow-right-on-rectangle" class="w-4 h-4 mr-2" />
                  Se déconnecter
                </UButton>
              </div>
            </div>
          </div>
        </div>

        <!-- **🔥 ACTIONS RAPIDES** -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">Actions rapides</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <UButton
              variant="outline"
              size="lg"
              class="justify-start"
              @click="goToDashboard"
            >
              <Icon name="i-heroicons-squares-2x2" class="w-5 h-5 mr-3" />
              Aller au tableau de bord
            </UButton>
            
            <UButton
              variant="outline"
              size="lg"
              class="justify-start"
              disabled
            >
              <Icon name="i-heroicons-cog-6-tooth" class="w-5 h-5 mr-3" />
              Paramètres avancés
            </UButton>
          </div>
        </div>

      </div>

      <!-- **🔥 GESTION D'ERREUR** -->
      <div v-else-if="userStore.error" class="text-center py-12">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
          <Icon name="i-heroicons-exclamation-triangle" class="w-8 h-8 text-red-500" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Erreur de chargement</h3>
        <p class="text-gray-600 mb-4">{{ userStore.error }}</p>
        <div class="space-x-3">
          <UButton @click="userStore.fetchProfile()">
            Réessayer
          </UButton>
          <UButton variant="outline" @click="goToDashboard">
            Retour au dashboard
          </UButton>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
/* Styles spécifiques si nécessaire */
</style> 