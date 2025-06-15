// Interface pour les événements Google Calendar
import type {GoogleCalendarEvent, GoogleCalendarResponse} from "~/types/GoogleCalendar";
import {CalendarDate} from "@internationalized/date";
import type {Slot} from "~/types/Slot";

export const useCalendarStore = defineStore('calendar', {
    state: () => ({
        apiUrl: useRuntimeConfig().public.apiBase,
        events: [] as GoogleCalendarEvent[],
        loading: false,
        loadingCreation: false,
        error: null as string | null,
        // 🔄 Gestion de l'annulation des requêtes
        currentAbortController: null as AbortController | null,
        requestId: 0, // Pour tracker les requêtes
        slots: [] as Slot[],
        actualStartWeek: '',
        actualEndWeek: '',
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
        async getEvent(start: string, end: string, abortSignal?: AbortSignal) {
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
                    },
                    signal: abortSignal
                })

                // **🔥 VÉRIFIER SI LA REQUÊTE N'A PAS ÉTÉ ABORTED**
                if (abortSignal?.aborted) {
                    console.log('🚫 Request was aborted, not updating state')
                    return
                }

                // Stocker les événements dans le state
                if (response.event && response.event.items) {
                    this.events = response.event.items
                }
                
            } catch (error: any) {
                // **🔥 GESTION SPÉCIALE DES ERREURS D'ABORT**
                if (error?.name === 'AbortError') {
                    console.log('🚫 Calendar fetch aborted')
                    return // Ne pas traiter comme une erreur
                }
                
                console.error('Error fetching events:', error);
                this.error = 'Erreur lors de la récupération des événements'
            } finally {
                // **🔥 NE PAS CHANGER LE LOADING SI LA REQUÊTE A ÉTÉ ABORTED**
                if (!abortSignal?.aborted) {
                    this.loading = false
                }
            }
        },

        async createEvent(date: CalendarDate, start: string, end: string, title: string | undefined , description?: string, colorId?: string){

            const toast = useToast()
            try {
                this.loadingCreation = true
                this.error = null

                let body = {
                    date : date,
                    start: start,
                    end: end,
                    title: title,
                    description: description || '',
                    colorId: colorId || '3', // Couleur par défaut
                }

                const response = await $fetch(
                    this.apiUrl + '/calendar/event',
                    {
                        headers: {
                            'Authorization': 'Bearer ' + useCookie('token').value,
                            'Content-Type': 'application/json'
                        },
                        method: 'POST',
                        body: body
                    }
                ) as GoogleCalendarEvent

                toast.add({
                    title: 'Event successfully created',
                    icon: 'i-lucide-check-check',
                    color: 'success',
                })

                // Ajouter l'événement créé au state local
                this.events.push(response)

            } catch (error) {
                this.error = 'Erreur lors de la création de l\'événement'
                this.loadingCreation = false
                toast.add({
                    title: 'Error creating event',
                    icon: 'i-lucide-x',
                    color: 'error',
                })

            } finally {
                this.loadingCreation = false
            }
        },

        // **🔥 MÉTHODES POUR DRAG & DROP MODE OPTIMISTE**
        updateEventOptimistic(updatedEvent: any) {
            // Mise à jour immédiate dans le state local
            const index = this.events.findIndex(event => event.id === updatedEvent.id)
            
            if (index !== -1) {
                // Convertir vers le format Google Calendar
                const updatedGoogleEvent = {
                    ...this.events[index],
                    start: { 
                        dateTime: updatedEvent.start, 
                        timeZone: 'Europe/Paris' 
                    },
                    end: { 
                        dateTime: updatedEvent.end, 
                        timeZone: 'Europe/Paris' 
                    }
                }
                
                // **🔥 FORCER VUE À DÉTECTER LE CHANGEMENT**
                this.events.splice(index, 1, updatedGoogleEvent)
            }
        },


        rollbackEvent(originalEvent: any) {
            // En cas d'erreur, remettre l'événement original
            const index = this.events.findIndex(event => event.id === originalEvent.id)
            if (index !== -1) {
                this.events[index] = {
                    ...this.events[index],
                    start: { 
                        dateTime: originalEvent.start, 
                        timeZone: 'Europe/Paris' 
                    },
                    end: { 
                        dateTime: originalEvent.end, 
                        timeZone: 'Europe/Paris' 
                    }
                }
            }
        },

        // **🔥 APPEL API POUR UPDATE D'UN ÉVÉNEMENT**
        async updateEvent(eventId: string, eventData: any, abortSignal?: AbortSignal) {
            try {
                return await $fetch<{ event: GoogleCalendarEvent }>(
                    this.apiUrl + `/calendar/event/${eventId}`,
                    {
                        headers: {
                            'Authorization': 'Bearer ' + useCookie('token').value,
                            'Content-Type': 'application/json'
                        },
                        method: 'PUT',
                        body: eventData,
                        signal: abortSignal
                    }
                )
            } catch (error) {
                console.error('Error updating event:', error)
                throw error
            }
        },

        async setSlot(newSlots: Slot[]){

        },

        async acceptSlot(slotId: number, start: string, end: string) {

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