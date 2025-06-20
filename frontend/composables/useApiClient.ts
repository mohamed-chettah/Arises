import { useAuthStore } from '~/store/AuthStore'

export const useApiClient = () => {
  const config = useRuntimeConfig()
  
  // **🔥 PROMISE PARTAGÉE POUR ÉVITER LES REFRESH MULTIPLES**
  let sharedRefreshPromise: Promise<{
    access_token: string
    refresh_token: string 
    expires_in: number
  }> | null = null
  
  // **🔥 FONCTION DE REFRESH SÉCURISÉE**
  async function performRefresh(): Promise<{access_token: string, refresh_token: string, expires_in: number}> {
    if (sharedRefreshPromise) {
      console.log('⏳ Refresh déjà en cours, attente de la promise partagée...')
      return await sharedRefreshPromise
    }
    
    console.log('🚀 Nouveau refresh lancé')
    const refreshToken = useCookie('refresh_token')
    
    if (!refreshToken.value) {
      throw new Error('Aucun refresh token disponible')
    }
    
    // **🔥 CRÉER LA PROMISE PARTAGÉE**
    sharedRefreshPromise = $fetch<{
      access_token: string
      refresh_token: string
      expires_in: number
    }>('/api/app/auth/refresh', {
      method: 'POST',
      baseURL: config.public.apiBase,
      headers: {
        'Authorization': `Bearer ${refreshToken.value}`,
        'Content-Type': 'application/json'
      }
    })
    
    try {
      const result = await sharedRefreshPromise
      
      // Mise à jour des tokens
      const tokenCookie = useCookie('token')
      const refreshTokenCookie = useCookie('refresh_token')
      
      tokenCookie.value = result.access_token
      refreshTokenCookie.value = result.refresh_token
      
      console.log('✅ Tokens rafraîchis avec succès')
      return result
      
    } finally {
      // **🔥 NETTOYER LA PROMISE PARTAGÉE**
      sharedRefreshPromise = null
    }
  }
  
  // **🔥 CLIENT API AVEC INTERCEPTEURS**
  const $api = $fetch.create({
    baseURL: config.public.apiBase,
    
    // Intercepteur de requête - ajouter l'access token
    onRequest({ options }) {
      const token = useCookie('token')
      
      if (token.value) {
        if (!options.headers) {
          options.headers = {}
        }
        options.headers = {
          ...options.headers,
          'Authorization': `Bearer ${token.value}`
        }
      }
    },
    
    // Intercepteur de réponse - gérer les erreurs 401/403  
    async onResponseError({ request, response, options }) {
      if (response.status === 401 || response.status === 403) {
        const authStore = useAuthStore()
        const refreshToken = useCookie('refresh_token')
        
        if (refreshToken.value) {
          try {
            console.log('🔄 Token expiré, tentative de refresh...')
            
            // **🔥 UTILISER LA FONCTION DE REFRESH SÉCURISÉE**
            await performRefresh()
            
            // Retry de la requête originale avec le nouveau token
            const newToken = useCookie('token').value
            
            if (!options.headers) {
              options.headers = {}
            }
            options.headers = {
              ...options.headers,
              'Authorization': `Bearer ${newToken}`
            }
            
            return $fetch(request, options)
            
          } catch (refreshError) {
            console.error('❌ Échec du refresh token:', refreshError)
            
            // Refresh token invalide/expiré -> déconnexion
            await authStore.logout()
            throw refreshError
          }
        } else {
          // Pas de refresh token -> déconnexion directe
          console.warn('⚠️ Pas de refresh token disponible')
          await authStore.logout()
        }
      }
      
      // Pour toutes les autres erreurs, on laisse passer
      throw response._data || response
    }
  })
  
  return {
    $api
  }
} 