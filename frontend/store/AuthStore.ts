import type { User } from "~/types/User";

interface UserProfile extends User {
  joinedAt?: string
  plan?: {
    name: string
    status: string
    expiresAt?: string
  }
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as UserProfile | null,
    loading: false,
    error: null as string | null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.user,
    
    userInitials: (state) => {
      if (!state.user?.name) return 'U'
      return state.user.name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
    },

    formattedJoinDate: (state) => {
      if (!state.user?.joinedAt) return ''
      return new Date(state.user.joinedAt).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
  },

  actions: {
    // **🔥 RÉCUPÉRER LES INFOS UTILISATEUR**
    async fetchUser(token?: string) {
      try {
        this.loading = true
        this.error = null

        const { $api } = useApiClient()
        
        const response = await $api<{ user: UserProfile }>('/me', {
          headers: {
            'Accept': 'application/json'
          }
        })

        if (response?.user) {
          this.user = response.user
          return true
        } else {
          this.user = null
          return false
        }
      } catch (error: any) {
        console.error('Error fetching user:', error)
        this.user = null
        this.error = 'Erreur lors de la récupération du profil'
        return false
      } finally {
        this.loading = false
      }
    },

    // **🔥 RÉCUPÉRER LE PROFIL COMPLET**
    async fetchProfile() {
      try {
        this.loading = true
        this.error = null

        const { $api } = useApiClient()
        
        const response = await $api<{ user: UserProfile }>('/user/profile')

        if (response.user) {
          this.user = response.user
        }

      } catch (error: any) {
        console.error('Error fetching user profile:', error)
        
        // Si token invalide, l'intercepteur gère déjà la déconnexion
        if (error?.status === 401) {
          return
        }

        // **🔥 DONNÉES TEMPORAIRES POUR LE DÉVELOPPEMENT**
        // TODO: Supprimer quand l'API sera prête
        this.user = {
          id: 1,
          name: 'John Doe',
          email: 'john.doe@arises.com',
          avatar: '',
          joinedAt: '2024-01-15T00:00:00Z',
          plan: {
            name: 'Gratuit',
            status: 'active'
          }
        } as UserProfile

      } finally {
        this.loading = false
      }
    },

    // **🔥 MISE À JOUR DU PROFIL**
    async updateProfile(userData: Partial<UserProfile>) {
      try {
        this.loading = true
        this.error = null

        const { $api } = useApiClient()

        const response = await $api<{ user: UserProfile }>('/user/profile', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json'
          },
          body: userData
        })

        if (response.user) {
          this.user = response.user
        }

        return response

      } catch (error: any) {
        console.error('Error updating profile:', error)
        this.error = 'Erreur lors de la mise à jour du profil'
        throw error
      } finally {
        this.loading = false
      }
    },

    // **🔥 DÉCONNEXION COMPLÈTE**
    async logout() {
      try {
        const token = useCookie('token')
        const refreshToken = useCookie('refresh_token')

        // Optionnel: Appel API pour invalider le token côté serveur
        if (token.value) {
          try {
            const { $api } = useApiClient()
            await $api('/auth/logout', {
              method: 'POST'
            })
          } catch (error) {
            // Ignorer les erreurs de déconnexion côté serveur
            console.warn('Server logout failed, continuing with client logout')
          }
        }

        // Nettoyer le state
        this.user = null
        this.error = null

        // Nettoyer les cookies
        token.value = null
        refreshToken.value = null

        // Rediriger vers la page de connexion
        await navigateTo('/login')

      } catch (error) {
        console.error('Error during logout:', error)
        
        // Forcer la déconnexion même en cas d'erreur
        this.user = null
        const token = useCookie('token')
        const refreshToken = useCookie('refresh_token')
        token.value = null
        refreshToken.value = null
        await navigateTo('/login')
      }
    },

    // **🔥 VÉRIFIER L'AUTHENTIFICATION**
    async checkAuth() {
      const token = useCookie('token')
      
      if (!token.value) {
        return false
      }

      // Si on a déjà les infos utilisateur, pas besoin de re-fetch
      if (this.user) {
        return true
      }

      // Sinon récupérer le profil
      try {
        await this.fetchUser()
        return !!this.user
      } catch (error) {
        return false
      }
    }
  },
})