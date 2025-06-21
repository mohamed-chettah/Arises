<script setup lang="ts">
import { ref, computed, watch, onMounted, nextTick, onUnmounted } from 'vue'
import dayjs from 'dayjs'
import type { Slot } from '~/types/Slot'
import { useCalendarStore } from "~/store/CalendarStore"
import { useCalendarDragDrop } from '~/composables/useCalendarDragDrop'
import { useCalendarLayout } from '~/composables/useCalendarLayout'
import CalendarEvent from './CalendarEvent.vue'
import CalendarCell from './CalendarCell.vue'
import ModalNewEvent from "~/components/saas/dashboard/ModalNewEvent.vue";
import isoWeek from 'dayjs/plugin/isoWeek'
import { CalendarDate } from '@internationalized/date'
dayjs.extend(isoWeek)


interface Props {
  slot: Slot[]
}

const props = defineProps<Props>()
const calendar = useCalendarStore()

// **🔥 NOUVEAU : UTILISATION DU COMPOSABLE DRAG & DROP**
const {
  draggedEvent,
  dragTargetCell,
  isDragging,
  dropPreview,
  onDragStart,
  onDragEnd,
  onDragOver,
  onDragLeave,
  onDrop,
  formatTime,
  cleanup,
  debugInfo
} = useCalendarDragDrop()

// **🔥 NOUVEAU : UTILISATION DU COMPOSABLE LAYOUT**
const {
  events,
  layoutDebugInfo
} = useCalendarLayout()

// État simple
const currentDate = ref(dayjs())


// **🔥 GESTION ABORT CONTROLLER POUR FETCH EVENTS**
let currentFetchController: AbortController | null = null

// Heures 24h
const displayHours = Array.from({ length: 24 }, (_, i) => i)

// **🔥 SYSTÈME D'ABORT DES REQUÊTES**
const pendingRequests = ref<Map<string, AbortController>>(new Map())

// Format AM/PM simple
function formatHour(hour: number): string {
  if (hour === 0) return '12 AM'
  if (hour === 12) return '12 PM'
  if (hour < 12) return `${hour} AM`
  return `${hour - 12} PM`
}

// Jours de la semaine
const weekDays = computed(() => {
  const start = currentDate.value.startOf('isoWeek') // Lundi automatiquement
  return Array.from({ length: 7 }, (_, i) => {
    const day = start.add(i, 'day')
    return {
      iso: day.format('YYYY-MM-DD'),
      label: day.format('ddd DD'),
      isToday: day.isSame(dayjs(), 'day')
    }
  })
})

// Période pour l'API
const currentPeriod = computed(() => {

  const start = currentDate.value.startOf('isoWeek')
  const end = start.add(6, 'day')
  calendar.actualStartWeek = start.toISOString()
  calendar.actualEndWeek = end.toISOString()
  return {
    start: start.toISOString(),
    end: end.endOf('day').toISOString(),
    label: `${start.format('DD MMM')} - ${end.format('DD MMM YYYY')}`
  }
})

// **🔥 FUSION INTELLIGENTE : SLOTS + ÉVÉNEMENTS**
const allCalendarItems = computed(() => {

  // remove les ancien slots
  let slotEvents: any[] = []

  // Convertir les événements normaux
  const normalEvents = events.value.map(event => ({
    ...event,
    type: 'event' as const
  }))

  // Convertir les slots IA avec copie pour éviter la mutation des props
  slotEvents = props.slot.map((slot, index) => {
    // **🔥 CRÉER UNE COPIE DU SLOT POUR ÉVITER LA MUTATION DES PROPS**
    const slotCopy = { ...slot }

    slotCopy.choice = false

    return {
      id: `slot-${index}`, // Utiliser l'index si pas d'id
      title: slotCopy.title || 'Slot disponible',
      start: slotCopy.start,
      end: slotCopy.end,
      description: slotCopy.description,
      color: 'bg-purple', // Toujours orange par défaut, la couleur sera gérée dans CalendarEvent
      type: 'slot' as const,
      originalSlot: slotCopy // Référence vers la copie du slot
    }
  })

  return [...normalEvents, ...slotEvents]
})

// **🔥 ADAPTER getEventsAt POUR UTILISER LA FUSION**
function getEventsAt(date: string, hour: number) {
  const cellEvents = allCalendarItems.value.filter(event => {
    const eventDate = dayjs(event.start).format('YYYY-MM-DD')
    if (eventDate !== date) return false

    const eventStartHour = dayjs(event.start).hour()
    const eventEndTime = dayjs(event.end)
    const eventEndHour = eventEndTime.hour()
    const eventEndMinutes = eventEndTime.minute()

    const adjustedEndHour = eventEndMinutes === 0 ? eventEndHour - 1 : eventEndHour
    return eventStartHour <= hour && hour <= adjustedEndHour
  })

  return cellEvents.map((event, index) => {
    const eventStartHour = dayjs(event.start).hour()

    let topOffset = 0
    if (eventStartHour === hour) {
      const eventMinutes = dayjs(event.start).minute()
      if (eventMinutes >= 45) topOffset = 75
      else if (eventMinutes >= 30) topOffset = 50
      else if (eventMinutes >= 15) topOffset = 25
      else topOffset = 0
    }

    let height = 60
    if (eventStartHour === hour) {
      const startTime = dayjs(event.start)
      const endTime = dayjs(event.end)
      const durationMinutes = endTime.diff(startTime, 'minute')
      height = (durationMinutes / 15) * 15
    } else {
      height = 0
    }

    return {
      ...event,
      width: eventStartHour === hour ? 100 / cellEvents.length : 0,
      leftOffset: eventStartHour === hour ? (100 / cellEvents.length) * index : 0,
      topOffset,
      height,
      isStartCell: eventStartHour === hour
    }
  })
}

// Navigation simple
function navigateWeek(direction: 'prev' | 'next') {
  currentDate.value = direction === 'prev'
    ? currentDate.value.subtract(1, 'week')
    : currentDate.value.add(1, 'week')
}

function goToToday() {
  currentDate.value = dayjs()
}

// Récupérer les événements
async function fetchEvents() {
  // **🔥 ANNULER LA REQUÊTE PRÉCÉDENTE SI ELLE EXISTE**
  if (currentFetchController) {
    currentFetchController.abort()
  }

  // **🔥 CRÉER UN NOUVEAU CONTROLLER**
  currentFetchController = new AbortController()

  try {
    await calendar.getEvent(
      currentPeriod.value.start,
      currentPeriod.value.end,
      currentFetchController.signal
    )
  } catch (error: any) {
    // **🔥 IGNORER LES ERREURS D'ABORT**
    if (error?.name === 'AbortError') {
      return
    }
    console.error('❌ Error fetching events:', error)
  } finally {
    // **🔥 NETTOYER LE CONTROLLER**
    if (currentFetchController && !currentFetchController.signal.aborted) {
      currentFetchController = null
    }
  }
}

// **🔥 SCROLL VERS L'HEURE ACTUELLE**
function scrollToCurrentTime() {
  nextTick(() => {
    const currentHour = dayjs().hour()
    const currentMinutes = dayjs().minute()

    // Calculer la position dans la grille
    const cellHeight = 60 // hauteur d'une cellule en px
    const hourPosition = currentHour * cellHeight
    const minuteOffset = (currentMinutes / 60) * cellHeight
    const totalPosition = hourPosition + minuteOffset

    // Scroll vers cette position (centré dans la vue)
    const scrollContainer = document.querySelector('.calendar-scroll-container')
    if (scrollContainer) {
      scrollContainer.scrollTo({
        top: totalPosition - (scrollContainer.clientHeight / 2),
        behavior: 'smooth'
      })
    }
  })
}

// **🔥 GESTIONNAIRES POUR LES ACTIONS DES SLOTS**
async function handleSlotAccepted(slot: any) {

  try {
    // **🔥 1. CRÉER L'ÉVÉNEMENT DANS LE CALENDRIER GOOGLE**
    const startDate = dayjs(slot.start)
    const endDate = dayjs(slot.end)

    // Convertir en CalendarDate pour l'API
    const calendarDate = new CalendarDate(
      startDate.year(),
      startDate.month() + 1,
      startDate.date()
    )

    // Créer l'événement via le store
    await calendar.createEvent(
      calendarDate,
      startDate.format('HH:mm'),
      endDate.format('HH:mm'),
      slot.title,
      slot.description || '',
    )

    // **🔥 2. SUPPRIMER LE SLOT DE LA LISTE**
    const index = props.slot.findIndex(s => s.id === slot.id)
    if (index > -1) {
      props.slot.splice(index, 1)
    }

  } catch (error) {
    console.error('❌ Erreur lors de l\'acceptation du slot:', error)
  }
}

function handleSlotRejected(slot: any) {
  const index = props.slot.findIndex(s => s.id === slot.id)
  if (index > -1) {
    props.slot.splice(index, 1)
  }

}

// Lifecycle
onMounted(async () => {
  await fetchEvents()
  // **🔥 SCROLL VERS L'HEURE ACTUELLE APRÈS CHARGEMENT**
  scrollToCurrentTime()
})

// Watcher pour changement de semaine
watch(currentPeriod, () => {
  fetchEvents()
})

// Cleanup on unmount
onUnmounted(() => {
  // **🔥 NETTOYER LE DRAG & DROP**
  cleanup()
  
  // **🔥 NETTOYER LES REQUÊTES FETCH EN COURS**
  if (currentFetchController) {
    currentFetchController.abort()
    currentFetchController = null
  }
})

</script>

<template>
  <div class="rounded-xl h-[94vh] backdrop-blur-sm flex flex-col">
    
    <!-- Header simple -->
    <div class="p-4 rounded-lg">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3">
          <UButton
              variant="outline"
              color="neutral"
              class="rounded-lg hover:bg-purple/20 hover:text-primary cursor-pointer inter text-gray-500"
              size="sm"
              icon="i-lucide-calendar"
              @click="goToToday"
          >
            Today
          </UButton>
          <div class="flex items-center gap-1">
            <UButton 
              variant="ghost" 
              size="sm" 
              icon="i-heroicons-chevron-left"
              @click="navigateWeek('prev')"
              class="cursor-pointer"
            />
            <span class="text-sm font-medium min-w-[170px] text-center inter">
              {{ currentPeriod.label }}
            </span>
            <UButton 
              variant="ghost" 
              size="md"
              icon="i-heroicons-chevron-right"
              class="cursor-pointer"
              @click="navigateWeek('next')"
            />
          </div>
        </div>

        <div class="flex items-center gap-2">
          <!-- Modal add event-->
          <ModalNewEvent />
        </div>
      </div>
    </div>

    <!-- Corps du calendrier -->
    <div class="flex-1 overflow-hidden flex flex-col">
      
      <!-- Grid calendrier avec header intégré -->
      <div class="flex-1 overflow-y-auto calendar-scroll-container">
        
        <!-- Header des jours (dans le scroll pour alignement parfait) -->
        <div class="grid grid-cols-8 border-b border-gray-200 sticky top-0 bg-white z-96">
          <div class="text-xs text-gray-500 border-r border-gray-200"></div>
          <div 
            v-for="day in weekDays" 
            :key="day.iso"
            class="p-2 text-center border-r border-gray-200 last:border-r-0"
            :class="{ 'bg-purple-50 text-purple-600': day.isToday }"
          >
            <div class="font-medium text-sm">{{ day.label }}</div>
          </div>
        </div>

        <div 
          v-for="hour in displayHours" 
          :key="hour"
          class="grid grid-cols-8 border-b border-gray-100"
          style="height: 60px"
        >
          <!-- Colonne heures -->
          <div class="flex items-center justify-end pr-2 text-[10px] text-gray-400 border-r border-gray-200">
            <span class="inter">{{ formatHour(hour) }}</span>
          </div>

          <!-- Cellules jours -->
          <CalendarCell 
            v-for="day in weekDays" 
            :key="`${day.iso}-${hour}`"
            :date="day.iso"
            :hour="hour"
            :is-today="day.isToday"
            :events="getEventsAt(day.iso, hour)"
            :drop-preview="dropPreview"
            :dragged-event="draggedEvent"
            :format-time="formatTime"
            @dragover="onDragOver"
            @dragleave="onDragLeave"
            @drop="onDrop"
            @event-dragstart="onDragStart"
            @event-dragend="onDragEnd"
            @slot-accepted="handleSlotAccepted"
            @slot-rejected="handleSlotRejected"
          />

        </div>
      </div>
    </div>

  </div>


</template>

<style scoped>
/* **🔥 OPTIMISATIONS PERFORMANCE DRAG & DROP** */
.calendar-scroll-container {
  /* Accélération matérielle pour le scroll */
  transform: translateZ(0);
  will-change: scroll-position;
}

/* Styles minimaux */
.grid > div {
  position: relative;
}
</style>