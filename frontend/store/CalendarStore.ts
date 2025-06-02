import type {User} from "~/types/User";

// Interface pour les événements Google Calendar
interface GoogleCalendarEvent {
    id: string
    summary: string
    description?: string
    start: {
        dateTime: string
        timeZone: string
    }
    end: {
        dateTime: string  
        timeZone: string
    }
    colorId?: string
    status: string
}

interface GoogleCalendarResponse {
    kind: string
    items: GoogleCalendarEvent[]
}

export const useCalendarStore = defineStore('calendar', {
    state: () => ({
        apiUrl: useRuntimeConfig().public.apiBase,
        events: [] as GoogleCalendarEvent[],
        loading: false,
        error: null as string | null,
        // 🔄 Gestion de l'annulation des requêtes
        currentAbortController: null as AbortController | null,
        requestId: 0, // Pour tracker les requêtes
    }),
    getters: {
        // Formatter les événements pour le calendrier
        formattedEvents: (state) => {
            return state.events.map(event => ({
                id: event.id,
                title: event.summary || 'Sans titre',
                start: event.start.dateTime,
                end: event.end.dateTime,
                description: event.description,
                color: getEventColor(event.colorId),
                status: event.status
            }))
        },
        
        // Événements filtrés par date
        eventsByDate: (state) => (date: string) => {
            return state.events.filter(event => {
                const eventDate = new Date(event.start.dateTime).toISOString().split('T')[0]
                return eventDate === date
            })
        }
    },
    actions: {
        async getEvent(start: string, end: string) {
            try {
                this.loading = true
                this.error = null
                
                const response = await $fetch<{event: GoogleCalendarResponse}>(this.apiUrl + '/calendar/events', {
                    headers: {
                        'Authorization': 'Bearer ' + useCookie('token').value,
                        'Credentials': 'include'
                    },
                    method: 'GET',
                    query: {
                        start: start,
                        end: end
                    }
                })

                // Stocker les événements dans le state
                if (response.event && response.event.items) {
                    this.events = response.event.items
                }
                
            } catch (error) {
                console.error('Error fetching events:', error);
                this.error = 'Erreur lors de la récupération des événements'
            } finally {
                this.loading = false
            }
        }
    },
})

// Fonction utilitaire pour mapper les couleurs Google Calendar
function getEventColor(colorId?: string): string {
    const colorMap: Record<string, string> = {
        '1': 'bg-blue-500/40',
        '2': 'bg-green-500/40', 
        '3': 'bg-purple-500/40',
        '4': 'bg-red-500/40',
        '5': 'bg-yellow-500/40',
        '6': 'bg-orange-500/40',
        '7': 'bg-pink-500/40',
        '8': 'bg-gray-500/40',
        '9': 'bg-indigo-500/40',
        '10': 'bg-teal-500/40',
        '11': 'bg-lime-500/40',
    }
    
    return colorMap[colorId || '3'] || 'bg-purple-500/40'
}