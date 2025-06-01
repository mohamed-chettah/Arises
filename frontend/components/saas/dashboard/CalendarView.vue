<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import dayjs from 'dayjs'
import type { Slot } from '~/types/Slot'
import {useCalendarStore} from "~/store/CalendarStore";

/**
 * Calendar – même look & feel que ta version initiale mais avec Drag & Drop natif.
 *
 * ✔️ Style conservé : bordure violette, table avec scrollbars fines, couleurs & boutons de confirmation
 * ✔️ Drag & Drop : tu peux déplacer les events d’une cellule à l’autre (comme Notion)
 * ✔️ Pas de dépendance externe supplémentaire
 */

interface EventItem {
  id: number
  title: string
  start: string // ISO 8601
  end?: string
  color: string
  choice?: boolean
}

interface Props {
  slot: Slot[]
}

const props = defineProps<Props>()
const calendar = useCalendarStore()

onMounted(() => {
  fetchEvents()
})

function fetchEvents(){
  calendar.getEvent('2025-05-30T20:00:00', '2025-05-30T20:00:00')
}


/***********************
 *   ETAT PRINCIPAL    *
 ***********************/
const events = ref<EventItem[]>([
  { id: 1, title: 'Intro PHP and setting up env', start: '2025-05-06T07:00:00', color: 'bg-blue/40' },
  { id: 2, title: 'Thursday Daily Meet',         start: '2025-05-08T09:00:00', color: 'bg-blue-600/40' },
  { id: 3, title: 'Padel with Mark',              start: '2025-05-05T13:00:00', color: 'bg-blue-600/40' },
  { id: 4, title: 'Financial Update',             start: '2025-05-09T10:00:00', color: 'bg-yellow-600/40' },
  { id: 5, title: 'Meeting with Sarah',           start: '2025-05-09T08:00:00', color: 'bg-yellow-600/40' },
  { id: 6, title: 'Meeting with a client',        start: '2025-05-07T11:00:00', color: 'bg-yellow-600/40' },
  { id: 7, title: 'Variables, conditions & loops',start: '2025-05-13T11:00:00', color: 'bg-purple-600/40' },
  { id: 8, title: 'Meeting with a client',        start: '2025-05-05T08:00:00', color: 'bg-pink-600/40' },
  { id: 9, title: 'Webinar: Figma',               start: '2025-05-04T11:00:00', color: 'bg-green-600/40' },
  { id: 10, title: 'Lunch with John',             start: '2025-05-09T12:00:00', color: 'bg-teal-600/40' },
  { id: 11, title: 'Workout with John',           start: '2025-05-12T13:00:00', color: 'bg-teal-800/40' },
  { id: 12, title: 'Workout with Pierre',         start: '2025-05-07T13:00:00', color: 'bg-teal-800/40' }
])

/***********************
 *      UTILITIES      *
 ***********************/
function eventsAt(isoDate: string, hour: number) {
  return events.value.filter(ev => {
    const d = dayjs(ev.start)
    return d.format('YYYY-MM-DD') === isoDate && d.hour() === hour
  })
}

const current = ref(dayjs('2025-05-07')) // point de départ ➜ Mer 07/05/2025
const hours  = Array.from({ length: 16 }, (_, i) => 7 + i) // 07 ➜ 22 h
const weekDays = computed(() => {
  const start = current.value.startOf('week') // dim. – change en isoWeek si tu veux lundi
  return Array.from({ length: 7 }, (_, i) => {
    const d = start.add(i, 'day')
    return {
      iso:   d.format('YYYY-MM-DD'),
      label: d.format('ddd DD'),
    }
  })
})

/***********************
 *   SYNCHRO DES SLOT  *
 ***********************/
watch(
    () => props.slot,
    newSlots => {
      newSlots.forEach(slot => {
        events.value.push({
          id: events.value.length + 1,
          title: slot.title,
          start: slot.start,
          end:   slot.end,
          color: slot.color ?? 'bg-purple/40',
          choice: true,
        })
      })
    },
    { immediate: true, deep: true }
)

/***********************
 *  DRAG & DROP STATE  *
 ***********************/
const draggedId = ref<number | null>(null)
function onDragStart(id: number) { draggedId.value = id }
function onDrop(dayIso: string, hour: number) {
  if (draggedId.value === null) return
  const ev = events.value.find(e => e.id === draggedId.value)
  if (!ev) return

  // conservez les minutes d’origine pour plus de précision
  const m = dayjs(ev.start).minute()
  ev.start = dayjs(`${dayIso}T00:00:00`).hour(hour).minute(m).toISOString()
  draggedId.value = null
}

/***********************
 * CONFIRM / SUPPRIMER *
 ***********************/
function confirmEvent(id: number) {
  const ev = events.value.find(e => e.id === id)
  if (!ev) return
  ev.choice = false
  ev.color  = 'bg-green-600/40'
}
function removeEvent(id: number) {
  events.value = events.value.filter(e => e.id !== id)
}
</script>

<template>
  <div class="border-[1px] border-purple/20 rounded-lg p-3 w-full flex flex-col max-h-[550px] overflow-y-auto">

    <!-- Navigation semaine -->
    <div class="flex justify-between items-center mb-3 text-xs font-medium text-purple">
      <button @click="current = current.subtract(1, 'week')" class="px-2 py-1 rounded bg-purple/10">Prev</button>
      <span>{{ current.startOf('week').format('DD MMM YYYY') }} – {{ current.endOf('week').format('DD MMM') }}</span>
      <button @click="current = current.add(1, 'week')" class="px-2 py-1 rounded bg-purple/10">Next</button>
    </div>

    <table class="overflow-y-auto scrollbar-thin scrollbar-thumb-purple/40 scrollbar-track-transparent w-full">
      <thead>
      <tr>
        <th class="w-10"></th>
        <th v-for="day in weekDays" :key="day.iso" class="text-sm text-grey pb-2 font-medium text-center">{{ day.label }}</th>
      </tr>
      </thead>

      <tbody>
      <tr v-for="hour in hours" :key="hour" class="rounded-lg">
        <!-- Colonne heures -->
        <td class="rounded-lg w-10 pr-2 text-right text-xs text-grey">{{ hour }}:00</td>

        <!-- Cellules jour*heure -->
        <td v-for="day in weekDays" :key="day.iso"
            class="relative border border-grey-calendar h-[60px]"
            @dragover.prevent
            @drop="onDrop(day.iso, hour)"
        >
          <!-- Events pour ce slot -->
          <div v-for="event in eventsAt(day.iso, hour)" :key="event.id"
               class="w-full font-semibold absolute inset-0 p-1 text-[10px] rounded-md border-l-4 flex flex-col justify-between cursor-move"
               :class="event.choice ? `text-purple border-l-purple ${event.color}` : 'text-[#3B7F92] border-l-[#3B7F92] bg-[#CCEBF2]'"
               draggable="true"
               @dragstart="onDragStart(event.id)"
          >
            <p>{{ event.title }}</p>
            <div v-if="event.choice" class="flex gap-1 justify-end">
              <button @click="confirmEvent(event.id)" class="text-xs px-1 rounded bg-green-500 text-white">✔</button>
              <button @click="removeEvent(event.id)"   class="text-xs px-1 rounded bg-red-500   text-white">✖</button>
            </div>
          </div>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
/* Conserve le motif grid subtil du calendrier original */
td {
  background-image: linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px);
  background-size: 100% 60px; /* hauteur ligne 60px */
}
</style>