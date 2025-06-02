<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import dayjs from 'dayjs'
import type { Slot } from '~/types/Slot'
import { useCalendarStore } from "~/store/CalendarStore"

/**
 * 📅 Calendrier simplifié pour debugging
 * 
 * ✅ Navigation semaine simple
 * ✅ Affichage événements Google Calendar
 * ✅ Format AM/PM
 * ✅ Code minimal et lisible
 */

interface Props {
  slot: Slot[]
}

const props = defineProps<Props>()
const calendar = useCalendarStore()

// État simple
const currentDate = ref(dayjs())

// Heures 24h
const displayHours = Array.from({ length: 24 }, (_, i) => i)

// Format AM/PM simple
function formatHour(hour: number): string {
  if (hour === 0) return '12:00 AM'
  if (hour === 12) return '12:00 PM'
  if (hour < 12) return `${hour}:00 AM`
  return `${hour - 12}:00 PM`
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

// Événements pour une cellule
function getEventsAt(date: string, hour: number) {
  return events.value.filter(event => {
    const eventDate = event.startTime.format('YYYY-MM-DD')
    return eventDate === date && event.hour === hour
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
  await calendar.getEvent(currentPeriod.value.start, currentPeriod.value.end)
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

// Lifecycle
onMounted(() => {
  fetchEvents()
})

// Watcher pour changement de semaine
watch(currentPeriod, () => {
  fetchEvents()
})
</script>

<template>
  <div class="border p-4 border-grey-calendar rounded-xl backdrop-blur-sm h-[calc(100vh-200px)] flex flex-col text-white">
    
    <!-- Header simple -->
    <div class="p-4 rounded-lg">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2">
            <UButton 
              variant="ghost" 
              size="sm" 
              icon="i-heroicons-chevron-left"
              @click="navigateWeek('prev')"
            />
            <span class="text-sm font-medium min-w-[200px] text-center">
              {{ currentPeriod.label }}
            </span>
            <UButton 
              variant="ghost" 
              size="sm" 
              icon="i-heroicons-chevron-right"
              @click="navigateWeek('next')"
            />
          </div>
        </div>

        <div class="flex items-center gap-2">
          <UButton 
            variant="outline" 
            size="sm" 
            @click="goToToday"
          >
            Today
          </UButton>
          
<!--          <UButton -->
<!--            size="sm" -->
<!--            icon="i-heroicons-plus"-->
<!--          >-->
<!--            New-->
<!--          </UButton>-->
        </div>
      </div>

      <!-- Info simple -->
      <div class="flex items-center justify-between text-sm">
        <div v-if="calendar.loading" class="flex items-center gap-2 text-purple-600">
          <UIcon name="i-heroicons-arrow-path" class="animate-spin" />
          <span>Loading...</span>
        </div>
        <div v-else>
          <span>{{ events.length }} events</span>
        </div>
      </div>
    </div>

    <!-- Corps du calendrier -->
    <div class="flex-1 overflow-hidden flex flex-col">
      
      <!-- Header des jours -->
      <div class="grid grid-cols-8 border-b border-grey-calendar">
        <div class="p-2 text-xs text-gray-500 border-r border-gray-200">Time</div>
        <div 
          v-for="day in weekDays" 
          :key="day.iso"
          class="p-2 text-center border-l border-gray-100 first:border-l-0"
          :class="{ 'bg-purple-50 text-purple-600': day.isToday }"
        >
          <div class="font-medium text-sm">{{ day.label }}</div>
        </div>
      </div>

      <!-- Grid calendrier -->
      <div class="flex-1 overflow-y-auto">
        <div 
          v-for="hour in displayHours" 
          :key="hour"
          class="grid grid-cols-8 border-b border-gray-50"
          style="height: 40px"
        >
          <!-- Colonne heures -->
          <div class="flex items-center justify-end pr-2 text-xs text-gray-500 border-r border-gray-100">
            <span class="font-mono">{{ formatHour(hour) }}</span>
          </div>

          <!-- Cellules jours -->
          <div 
            v-for="day in weekDays" 
            :key="`${day.iso}-${hour}`"
            class="relative border-l first:border-l-0 hover:bg-blue-50/20"
            :class="{ 'bg-purple-50/20': day.isToday }"
          >
            <!-- Événements -->
            <div 
              v-for="event in getEventsAt(day.iso, hour)" 
              :key="event.id"
              class="absolute inset-x-0.5 top-0.5 rounded border-l-2 p-1 text-xs font-medium shadow-sm"
              :class="event.color"
              style="height: 38px"
            >
              <div class="truncate font-semibold">{{ event.title }}</div>
              
              <!-- Actions pour slots IA -->
              <div v-if="event" class="flex gap-1 mt-1">
                <UButton 
                  size="2xs" 
                  color="green" 
                  @click="confirmSlot(event.id)"
                >
                  ✓
                </UButton>
                <UButton 
                  size="2xs" 
                  color="red" 
                  @click="removeSlot(event.id)"
                >
                  ✕
                </UButton>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* Styles minimaux */
.grid > div {
  position: relative;
}
</style>