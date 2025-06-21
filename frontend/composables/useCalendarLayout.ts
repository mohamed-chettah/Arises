import { computed } from 'vue'
import dayjs from 'dayjs'
import { useCalendarStore } from "~/store/CalendarStore"

export const useCalendarLayout = () => {
  const calendar = useCalendarStore()

  // **🔥 ÉVÉNEMENTS FORMATÉS AVEC DONNÉES TEMPS**
  const events = computed(() => {
    return calendar.formattedEvents.map(event => ({
      ...event,
      startTime: dayjs(event.start),
      hour: dayjs(event.start).hour()
    }))
  })

  // **🔥 LOGIQUE DE COHABITATION ET POSITIONNEMENT**
  // Événements pour une cellule avec calcul de position
  function getEventsAt(date: string, hour: number) {
    const cellEvents = events.value.filter(event => {
      const eventDate = event.startTime.format('YYYY-MM-DD')
      if (eventDate !== date) return false
      
      // **🔥 INCLURE LES ÉVÉNEMENTS QUI TRAVERSENT CETTE CELLULE**
      const eventStartHour = event.startTime.hour()
      const eventEndTime = dayjs(event.end)
      const eventEndHour = eventEndTime.hour()
      const eventEndMinutes = eventEndTime.minute()
      
      // Si l'événement se termine exactement à l'heure pile (ex: 18:00), 
      // ne pas l'inclure dans la cellule suivante
      const adjustedEndHour = eventEndMinutes === 0 ? eventEndHour - 1 : eventEndHour
      
      // L'événement traverse cette cellule s'il commence avant ou pendant cette heure
      // ET se termine après le début de cette heure
      return eventStartHour <= hour && hour <= adjustedEndHour
    })
    
    // **🔥 DEBUG - LOG POUR DÉTECTER LES DOUBLONS**
    if (cellEvents.length > 0) {
      const eventIds = cellEvents.map(e => e.id)
      const uniqueIds = [...new Set(eventIds)]
      if (eventIds.length !== uniqueIds.length) {
        console.log('🚨 DOUBLONS DÉTECTÉS:', date, hour, eventIds)
      }
    }
    
    // **🔥 CALCULER LARGEUR ET POSITION POUR CHAQUE ÉVÉNEMENT**
    return cellEvents.map((event, index) => {
      const eventStartHour = event.startTime.hour()
      
      // **🔥 CALCUL POSITION VERTICALE SELON LES MINUTES (SEULEMENT POUR LA CELLULE DE DÉBUT)**
      let topOffset = 0 // Position verticale en %
      
      // Ne calculer l'offset que pour la cellule de début
      if (eventStartHour === hour) {
        const eventMinutes = event.startTime.minute()
        if (eventMinutes >= 45) topOffset = 75
        else if (eventMinutes >= 30) topOffset = 50  
        else if (eventMinutes >= 15) topOffset = 25
        else topOffset = 0
      }
      
      // **🔥 CALCUL HAUTEUR SELON DURÉE RÉELLE (SEULEMENT POUR LA CELLULE DE DÉBUT)**
      let height = 60 // Hauteur par défaut d'une cellule
      
      if (eventStartHour === hour) {
        // Calcul complet de la hauteur seulement pour la cellule de début
        const startTime = event.startTime
        const endTime = dayjs(event.end)
        const durationMinutes = endTime.diff(startTime, 'minute')
        height = (durationMinutes / 15) * 15 // 15px par quart d'heure
      } else {
        // Pour les cellules de continuation, masquer visuellement mais garder draggable
        height = 0
      }
      
      return {
        ...event,
        // **🔥 LARGEUR ET POSITION SELON COHABITATION**
        width: eventStartHour === hour ? 100 / cellEvents.length : 0, // Largeur visible seulement au début
        leftOffset: eventStartHour === hour ? (100 / cellEvents.length) * index : 0, // Position X
        topOffset, // Position Y en pourcentage
        height, // Hauteur en pixels
        isStartCell: eventStartHour === hour // Pour savoir si c'est la cellule de début
      }
    })
  }

  // **🔥 UTILITAIRES LAYOUT**
  
  // Calculer la durée d'un événement en minutes
  function getEventDurationMinutes(event: any): number {
    const startTime = dayjs(event.start)
    const endTime = dayjs(event.end)
    return endTime.diff(startTime, 'minute')
  }
  
  // Calculer la hauteur en pixels selon la durée
  function calculateEventHeight(durationMinutes: number): number {
    return (durationMinutes / 15) * 15 // 15px par quart d'heure
  }
  
  // Vérifier si un événement traverse une cellule donnée
  function doesEventCrossCell(event: any, targetDate: string, targetHour: number): boolean {
    const eventDate = dayjs(event.start).format('YYYY-MM-DD')
    if (eventDate !== targetDate) return false
    
    const eventStartHour = dayjs(event.start).hour()
    const eventEndTime = dayjs(event.end)
    const eventEndHour = eventEndTime.hour()
    const eventEndMinutes = eventEndTime.minute()
    
    // Ajustement si l'événement se termine pile à l'heure
    const adjustedEndHour = eventEndMinutes === 0 ? eventEndHour - 1 : eventEndHour
    
    return eventStartHour <= targetHour && targetHour <= adjustedEndHour
  }
  
  // Obtenir tous les événements d'une journée
  function getEventsForDay(date: string) {
    return events.value.filter(event => {
      const eventDate = event.startTime.format('YYYY-MM-DD')
      return eventDate === date
    })
  }
  
  // Calculer le nombre d'événements simultanés max pour une journée
  function getMaxConcurrentEvents(date: string): number {
    const dayEvents = getEventsForDay(date)
    let maxConcurrent = 0
    
    // Pour chaque heure de la journée
    for (let hour = 0; hour < 24; hour++) {
      const eventsAtHour = dayEvents.filter(event => 
        doesEventCrossCell(event, date, hour)
      )
      maxConcurrent = Math.max(maxConcurrent, eventsAtHour.length)
    }
    
    return maxConcurrent
  }

  // **🔥 COMPUTED POUR DEBUG LAYOUT**
  const layoutDebugInfo = computed(() => ({
    totalEvents: events.value.length,
    eventsGroupedByDay: events.value.reduce((acc, event) => {
      const date = event.startTime.format('YYYY-MM-DD')
      acc[date] = (acc[date] || 0) + 1
      return acc
    }, {} as Record<string, number>)
  }))

  // **🔥 RETOURNER TOUT CE DONT LE COMPOSANT A BESOIN**
  return {
    // Données principales
    events,
    
    // Fonction principale
    getEventsAt,
    
    // Utilitaires
    getEventDurationMinutes,
    calculateEventHeight,
    doesEventCrossCell,
    getEventsForDay,
    getMaxConcurrentEvents,
    
    // Debug
    layoutDebugInfo
  }
}
