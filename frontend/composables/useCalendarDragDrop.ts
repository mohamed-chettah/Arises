import { ref, computed } from 'vue'
import dayjs from 'dayjs'
import { useCalendarStore } from "~/store/CalendarStore"

export const useCalendarDragDrop = () => {
  const calendar = useCalendarStore()

  // **🔥 ÉTATS RÉACTIFS DRAG & DROP**
  const draggedEvent = ref<any>(null)
  const dragTargetCell = ref<{ date: string, hour: number, minutes?: number } | null>(null)
  const isDragging = ref(false)

  // **🔥 SYSTÈME D'ABORT DES REQUÊTES**
  const pendingRequests = ref<Map<string, AbortController>>(new Map())

  // **🔥 ZONE DE SURBRILLANCE PENDANT LE DRAG**
  const dropPreview = ref<{
    date: string
    hour: number
    minutes: number
    topPercent: number
    height: number
    newStart: string
    newEnd: string
  } | null>(null)

  // **🔥 OPTIMISATION FLUIDITÉ DRAG**
  let rafId: number | null = null
  let lastPreviewUpdate = 0

  // **🔥 AUTO-SCROLL PENDANT LE DRAG**
  let autoScrollInterval: ReturnType<typeof setInterval> | null = null
  const autoScrollZone = 100 // Zone de 100px en haut/bas
  const autoScrollSpeed = 8 // Vitesse de base

  // **🔥 UTILITAIRE FORMAT HEURE**
  function formatTime(date: string): string {
    const time = dayjs(date)
    const hour = time.hour()
    const minutes = time.minute()
    
    let hourStr = ''
    if (hour === 0) hourStr = '12'
    else if (hour <= 12) hourStr = hour.toString()
    else hourStr = (hour - 12).toString()
    
    const ampm = hour < 12 ? 'AM' : 'PM'
    const minuteStr = minutes > 0 ? `:${minutes.toString().padStart(2, '0')}` : ''
    
    return `${hourStr}${minuteStr} ${ampm}`
  }

  // **🔥 FONCTIONS DRAG & DROP**
  function onDragStart(event: DragEvent, calendarEvent: any) {
    draggedEvent.value = { ...calendarEvent }
    isDragging.value = true
    
    // **🔥 DÉSACTIVER LA GHOST IMAGE PAR DÉFAUT**
    const canvas = document.createElement('canvas')
    canvas.width = 1
    canvas.height = 1
    const ctx = canvas.getContext('2d')
    if (ctx) {
      ctx.globalAlpha = 0.01 // Quasi transparent
      ctx.fillRect(0, 0, 1, 1)
    }
    event.dataTransfer?.setDragImage(canvas, 0, 0)
  }

  function onDragEnd(event: DragEvent) {
    console.log('🏁 onDragEnd called - cleaning preview')
    isDragging.value = false
    draggedEvent.value = null
    dragTargetCell.value = null
    dropPreview.value = null
    
    // **🔥 NETTOYER RAF ET AUTO-SCROLL**
    if (rafId) {
      cancelAnimationFrame(rafId)
      rafId = null
    }
    stopAutoScroll()
  }

  function onDragOver(event: DragEvent, date: string, hour: number) {
    event.preventDefault() // Autoriser le drop
    
    if (!draggedEvent.value) return
    
    // **🔥 AUTO-SCROLL SI PROCHE DES BORDS**
    handleAutoScroll(event.clientY)
    
    // **🔥 OPTIMISATION : LIMITER LA FRÉQUENCE DES MISES À JOUR**
    const now = performance.now()
    if (now - lastPreviewUpdate < 16) return // Max 60fps
    lastPreviewUpdate = now
    
    // **🔥 UTILISER RAF POUR DES MISES À JOUR FLUIDES**
    rafId = requestAnimationFrame(() => {
      // **🔥 CALCUL PRÉCISION 15 MINUTES**
      const rect = (event.target as HTMLElement).getBoundingClientRect()
      const cellHeight = rect.height
      const mouseY = event.clientY - rect.top
      
      // Diviser la cellule en 4 zones de 15min
      const percentage = mouseY / cellHeight
      let minutes = 0
      let topPercent = 0
      
      if (percentage < 0.25) {
        minutes = 0
        topPercent = 0
      } else if (percentage < 0.5) {
        minutes = 15 
        topPercent = 25
      } else if (percentage < 0.75) {
        minutes = 30
        topPercent = 50
      } else {
        minutes = 45
        topPercent = 75
      }
      
      // **🔥 CALCULER HAUTEUR DE L'ÉVÉNEMENT DRAGUÉ**
      const startTime = dayjs(draggedEvent.value.start)
      const endTime = dayjs(draggedEvent.value.end)
      const durationMinutes = endTime.diff(startTime, 'minute')
      const height = (durationMinutes / 15) * 15 // 15px par quart d'heure
      
      // **🔥 CALCULER NOUVELLE HEURE POUR PREVIEW**
      const newStartTime = dayjs(`${date} ${hour}:${minutes.toString().padStart(2, '0')}:00`)
      const newEndTime = newStartTime.add(durationMinutes, 'minute')
      
      // **🔥 METTRE À JOUR LA ZONE DE PREVIEW**
      dropPreview.value = {
        date,
        hour,
        minutes,
        topPercent,
        height,
        newStart: newStartTime.toISOString(),
        newEnd: newEndTime.toISOString()
      }
      
      dragTargetCell.value = { date, hour, minutes }
    })
  }

  function onDragLeave(event: DragEvent) {
    // **🔥 NETTOYER PREVIEW SI ON QUITTE LA ZONE**
    const relatedTarget = event.relatedTarget as HTMLElement
    
    // Si on sort complètement de la grille, nettoyer
    if (!relatedTarget || !relatedTarget.closest('.calendar-scroll-container')) {
      dropPreview.value = null
      dragTargetCell.value = null
      stopAutoScroll()
    }
  }

  async function onDrop(event: DragEvent, targetDate: string, targetHour: number) {
    event.preventDefault()
    
    // **🔥 NETTOYER IMMÉDIATEMENT LE PREVIEW**
    dropPreview.value = null
    
    if (!draggedEvent.value || !dragTargetCell.value) {
      return
    }
    
    const eventId = draggedEvent.value.id
    
    // **🔥 ANNULER REQUÊTE PRÉCÉDENTE POUR CET ÉVÉNEMENT**
    if (pendingRequests.value.has(eventId)) {
      pendingRequests.value.get(eventId)?.abort()
      pendingRequests.value.delete(eventId)
    }
    
    // **🔥 NOUVELLE HEURE PRÉCISE (15min)**
    const targetMinutes = dragTargetCell.value.minutes || 0
    const newStart = dayjs(`${targetDate} ${targetHour}:${targetMinutes.toString().padStart(2, '0')}:00`)
    
    const originalStart = dayjs(draggedEvent.value.start)
    const duration = dayjs(draggedEvent.value.end).diff(originalStart, 'minute')
    const newEnd = newStart.add(duration, 'minute')
    
    const updatedEvent = {
      ...draggedEvent.value,
      start: newStart.toISOString(),
      end: newEnd.toISOString()
    }
    
    try {
      // 1. Mise à jour optimiste dans le store
      await calendar.updateEventOptimistic(updatedEvent)
      
      // 2. Appel API en arrière-plan avec abort controller
      await updateEventOnServer(updatedEvent)
      
    } catch (error: any) {
      // Ignorer les erreurs d'abort
      if (error?.name === 'AbortError') {
        return
      }
      
      // Rollback en cas d'erreur
      await calendar.rollbackEvent(draggedEvent.value)
    } finally {
      // Nettoyer les références
      pendingRequests.value.delete(eventId)
    }
    
    // Nettoyer l'état
    draggedEvent.value = null
    dragTargetCell.value = null
  }

  // **🔥 APPEL API POUR UPDATE**
  async function updateEventOnServer(event: any) {
    const eventId = event.id
    
    // **🔥 CRÉER ABORT CONTROLLER POUR CETTE REQUÊTE**
    const abortController = new AbortController()
    pendingRequests.value.set(eventId, abortController)
    
    // Appel à l'API Laravel via le store avec signal d'abort
    const eventData = {
      title: event.title,
      start: event.start,
      end: event.end,
      description: event.description || ''
    }
    
    await calendar.updateEvent(event.id, eventData, abortController.signal)
  }

  // **🔥 AUTO-SCROLL PENDANT LE DRAG**
  function handleAutoScroll(clientY: number) {
    const scrollContainer = document.querySelector('.calendar-scroll-container') as HTMLElement
    if (!scrollContainer) return
    
    const containerRect = scrollContainer.getBoundingClientRect()
    const relativeY = clientY - containerRect.top
    const containerHeight = containerRect.height
    
    // Nettoyer l'interval précédent
    if (autoScrollInterval) {
      clearInterval(autoScrollInterval)
      autoScrollInterval = null
    }
    
    let scrollDirection = 0
    let scrollMultiplier = 1
    
    // Zone haute - scroll vers le haut
    if (relativeY < autoScrollZone) {
      scrollDirection = -1
      // Plus proche du bord = plus rapide (multiplicateur de 1 à 5)
      scrollMultiplier = Math.max(1, 5 - (relativeY / (autoScrollZone / 5)))
    }
    // Zone basse - scroll vers le bas  
    else if (relativeY > containerHeight - autoScrollZone) {
      scrollDirection = 1
      const distanceFromBottom = containerHeight - relativeY
      // Plus proche du bord = plus rapide (multiplicateur de 1 à 5)
      scrollMultiplier = Math.max(1, 5 - (distanceFromBottom / (autoScrollZone / 5)))
    }
    
    // Démarrer l'auto-scroll si nécessaire
    if (scrollDirection !== 0) {
      autoScrollInterval = setInterval(() => {
        scrollContainer.scrollTop += scrollDirection * autoScrollSpeed * scrollMultiplier
      }, 8) // 120fps pour plus de fluidité
    }
  }

  function stopAutoScroll() {
    if (autoScrollInterval) {
      clearInterval(autoScrollInterval)
      autoScrollInterval = null
    }
  }

  // **🔥 COMPUTED POUR DEBUG**
  const debugInfo = computed(() => ({
    isDragging: isDragging.value,
    hasDropPreview: !!dropPreview.value,
    pendingRequestsCount: pendingRequests.value.size,
    draggedEventTitle: draggedEvent.value?.title || null
  }))

  // **🔥 NETTOYAGE GLOBAL DE SÉCURITÉ**
  const globalCleanup = () => {
    console.log('🧹 Global cleanup triggered')
    isDragging.value = false
    draggedEvent.value = null
    dragTargetCell.value = null
    dropPreview.value = null
    
    if (rafId) {
      cancelAnimationFrame(rafId)
      rafId = null
    }
    stopAutoScroll()
  }

  // **🔥 AJOUTER DES LISTENERS GLOBAUX POUR SÉCURITÉ**
  if (process.client) {
    document.addEventListener('dragend', globalCleanup)
    document.addEventListener('drop', globalCleanup)
    // Nettoyage aussi si la souris sort de la fenêtre pendant un drag
    document.addEventListener('mouseleave', () => {
      if (isDragging.value) {
        globalCleanup()
      }
    })
  }

  // **🔥 FONCTION DE NETTOYAGE DES LISTENERS**
  const cleanup = () => {
    if (process.client) {
      document.removeEventListener('dragend', globalCleanup)
      document.removeEventListener('drop', globalCleanup)
    }
    globalCleanup()
  }

  // **🔥 RETOURNER TOUT CE DONT LE COMPOSANT A BESOIN**
  return {
    // États
    draggedEvent,
    dragTargetCell,
    isDragging,
    dropPreview,
    
    // Fonctions principales
    onDragStart,
    onDragEnd,
    onDragOver,
    onDragLeave,
    onDrop,
    
    // Utilitaires
    formatTime,
    cleanup,
    
    // Debug
    debugInfo
  }
}
