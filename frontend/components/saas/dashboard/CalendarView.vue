<script setup lang="ts">
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import dayjs from 'dayjs'
import type { Slot } from '~/types/Slot'
import { useCalendarStore } from "~/store/CalendarStore"

interface Props {
  slot: Slot[]
}

const props = defineProps<Props>()
const calendar = useCalendarStore()

// État simple
const currentDate = ref(dayjs())

// Heures 24h
const displayHours = Array.from({ length: 24 }, (_, i) => i)

// **🔥 NOUVEAUX ÉTATS POUR DRAG & DROP**
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
const autoScrollZone = 100 // Zone de 100px en haut/bas (plus large)
const autoScrollSpeed = 8 // Vitesse de base augmentée

// Format AM/PM simple
function formatHour(hour: number): string {
  if (hour === 0) return '12 AM'
  if (hour === 12) return '12 PM'
  if (hour < 12) return `${hour} AM`
  return `${hour - 12} PM`
}

// **🔥 FORMAT HEURE COMPLÈTE AVEC MINUTES**
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

// Jours de la semaine
const weekDays = computed(() => {
  const start = currentDate.value.startOf('week').add(1, 'day') // Lundi
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
  const start = currentDate.value.startOf('week').add(1, 'day')
  const end = start.add(6, 'day')
  return {
    start: start.toISOString(),
    end: end.endOf('day').toISOString(),
    label: `${start.format('DD MMM')} - ${end.format('DD MMM YYYY')}`
  }
})

// Événements formatés
const events = computed(() => {
  return calendar.formattedEvents.map(event => ({
    ...event,
    startTime: dayjs(event.start),
    hour: dayjs(event.start).hour()
  }))
})

// **🔥 LOGIQUE DE COHABITATION**
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
  
  // Calculer largeur et position pour chaque événement
  return cellEvents.map((event, index) => {
    // **🔥 CALCUL POSITION VERTICALE SELON LES MINUTES (SEULEMENT POUR LA CELLULE DE DÉBUT)**
    const eventStartHour = event.startTime.hour()
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
      width: eventStartHour === hour ? 100 / cellEvents.length : 0, // Largeur visible seulement au début
      leftOffset: eventStartHour === hour ? (100 / cellEvents.length) * index : 0, // Position X
      topOffset, // Position Y en pourcentage
      height, // Hauteur en pixels
      isStartCell: eventStartHour === hour // Pour savoir si c'est la cellule de début
    }
  })
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
  isDragging.value = false
  draggedEvent.value = null
  dragTargetCell.value = null
  dropPreview.value = null // **🔥 NETTOYER PREVIEW**
  
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
    stopAutoScroll() // **🔥 ARRÊTER AUTO-SCROLL**
  }
}

async function onDrop(event: DragEvent, targetDate: string, targetHour: number) {
  event.preventDefault()
  
  if (!draggedEvent.value || !dragTargetCell.value) return
  
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
  dropPreview.value = null // **🔥 NETTOYER PREVIEW**
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
  await calendar.getEvent(currentPeriod.value.start, currentPeriod.value.end)
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

// Actions pour les slots IA
function confirmSlot(slotId: string) {
  const slot = props.slot.find(s => `slot-${s.id}` === slotId)
  if (slot) {
    slot.choice = false
    slot.color = 'bg-green-500/40'
  }
}

function removeSlot(slotId: string) {
  const index = props.slot.findIndex(s => `slot-${s.id}` === slotId)
  if (index > -1) {
    props.slot.splice(index, 1)
  }
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
</script>

<template>
  <div class="rounded-xl h-[94vh] backdrop-blur-sm flex flex-col">
    
    <!-- Header simple -->
    <div class="p-4 rounded-lg">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3">
          <UButton
              variant="outline"
              class="rounded-lg hover:bg-purple/20 hover:text-primary cursor-pointer inter text-gray-500"
              size="sm"
              @click="goToToday"
          >
            Today
          </UButton>
          <div class="flex items-center gap-2">
            <UButton 
              variant="ghost" 
              size="sm" 
              icon="i-heroicons-chevron-left"
              @click="navigateWeek('prev')"
              class="cursor-pointer"
            />
            <span class="text-sm font-medium min-w-[200px] text-center">
              {{ currentPeriod.label }}
            </span>
            <UButton 
              variant="ghost" 
              size="sm" 
              icon="i-heroicons-chevron-right"
              class="cursor-pointer"
              @click="navigateWeek('next')"
            />
          </div>
        </div>

        <div class="flex items-center gap-2">

        </div>
      </div>
    </div>

    <!-- Corps du calendrier -->
    <div class="flex-1 overflow-hidden flex flex-col">
      
      <!-- Grid calendrier avec header intégré -->
      <div class="flex-1 overflow-y-auto calendar-scroll-container">
        
        <!-- Header des jours (dans le scroll pour alignement parfait) -->
        <div class="grid grid-cols-8 border-b border-gray-200 sticky top-0 bg-white z-10">
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
          class="grid grid-cols-8 border-b border-gray-200"
          style="height: 60px"
        >
          <!-- Colonne heures -->
          <div class="flex items-center justify-end pr-2 text-[10px] text-gray-400 border-r border-gray-200">
            <span class="inter">{{ formatHour(hour) }}</span>
          </div>

          <!-- Cellules jours -->
          <div 
            v-for="day in weekDays" 
            :key="`${day.iso}-${hour}`"
            class="relative border-r border-gray-200 last:border-r-0 hover:bg-blue-50/20 calendar-cell"
            :class="{ 
              'bg-purple-50/20': day.isToday
            }"
            :data-date="day.iso"
            :data-hour="hour"
            @dragover="onDragOver($event, day.iso, hour)"
            @dragleave="onDragLeave($event)"
            @drop="onDrop($event, day.iso, hour)"
          >
            <!-- **🔥 ZONE DE PREVIEW PENDANT LE DRAG** -->
            <div 
              v-if="dropPreview && dropPreview.date === day.iso && dropPreview.hour === hour"
              class="absolute bg-purple/20 border-[1px] border-purple border-dashed rounded z-20 pointer-events-none drop-preview"
              :style="{
                height: `${dropPreview.height}px`,
                width: '95%',
                left: '2.5%',
                top: `${dropPreview.topPercent}%`
              }"
            >
              <div class="text-[10px] font-medium p-1">
                {{ formatTime(dropPreview.newStart) }}-{{ formatTime(dropPreview.newEnd) }}
                {{ draggedEvent?.title }}
              </div>
            </div>
            
            <!-- Événements -->
            <div 
              v-for="event in getEventsAt(day.iso, hour)" 
              :key="`${event.id}-${hour}`"
              class="absolute rounded border-l-2 p-1 text-xs font-medium shadow-sm cursor-move z-10 event-draggable"
              :class="[
                event.color,
                { 'opacity-0': !event.isStartCell }
              ]"
              :style="{
                height: event.isStartCell ? `${event.height}px` : '60px',
                width: event.isStartCell ? `${event.width}%` : '100%',
                left: event.isStartCell ? `${event.leftOffset}%` : '0%',
                top: event.isStartCell ? `${event.topOffset}%` : '0%'
              }"
              draggable="true"
              @dragstart="onDragStart($event, event)"
              @dragend="onDragEnd($event)"
            >
              <div 
                v-if="event.isStartCell"
                class="truncate font-semibold text-[10px] flex items-center gap-1"
              >
                <span class="text-gray-600">{{ formatTime(event.start) }}-{{ formatTime(event.end) }}</span>
                <span>{{ event.title }}</span>
              </div>
            </div>
          </div>
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

/* Optimisation des événements draggables */
.event-draggable {
  will-change: transform, opacity;
  backface-visibility: hidden;
  transform: translateZ(0);
  /* **🔥 S'ASSURER QUE TOUTE LA SURFACE EST DRAGGABLE** */
  user-select: none;
  -webkit-user-select: none;
  -webkit-touch-callout: none;
  /* **🔥 FORCER LE DRAG SUR TOUTE LA SURFACE** */
  display: block !important;
  overflow: hidden;
}

.event-draggable * {
  /* **🔥 EMPÊCHER TOUS LES ENFANTS D'INTERFÉRER** */
  pointer-events: none !important;
  user-select: none !important;
  -webkit-user-select: none !important;
}

.event-draggable:hover {
  /* Feedback visuel au hover */
  filter: brightness(1.1);
  transition: filter 0.1s ease;
}

.event-draggable:active {
  /* Feedback au click/drag */
  filter: brightness(0.95);
}

/* Preview fluide */
.drop-preview {
  will-change: transform, opacity;
  transform: translateZ(0);
  transition: opacity 0.1s ease-out;
}

/* Cellules optimisées */
.calendar-cell {
  will-change: background-color;
  transform: translateZ(0);
}

/* Styles minimaux */
.grid > div {
  position: relative;
}

/* Contenu des événements */
.event-content {
  /* Le contenu ne doit pas interférer avec le drag */
  pointer-events: none !important;
  overflow: hidden;
}
</style>