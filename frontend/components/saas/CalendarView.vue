<template>
  <div class="border-[1px] border-purple/20  rounded-lg p-4 w-full h-full flex flex-col">
    <!-- header week selector -->
    <div class="flex items-center justify-between mb-4 text-gray-200 text-sm">
      <button @click="prevWeek" class="hover:text-white"><UIcon name="i-lucide-chevron-left" /></button>
      <div class="font-semibold">{{ formattedRange }}</div>
      <button @click="nextWeek" class="hover:text-white"><UIcon name="i-lucide-chevron-right" /></button>
    </div>

    <!-- calendar grid -->
    <div class="flex-1 grid" :style="gridStyle">
      <!-- time labels -->
      <div v-for="t in hours" :key="t" class="text-xs text-gray-400 pr-2 border-r border-[#26204d] relative">
        <span class="absolute -translate-y-1/2 top-0">{{ tLabel(t) }}</span>
      </div>

      <!-- day columns with slots for events -->
      <template v-for="(day, dIndex) in weekDays" :key="day.iso">
        <div
            v-for="t in hours"
            :key="day.iso+ '-' + t"
            class="border border-[#26204d] relative"
        >
          <!-- events that start at this hour -->
          <template v-for="ev in eventsForSlot(day, t)" :key="ev.id">
            <div
                class="absolute left-0 right-0 px-1 py-0.5 text-[10px] rounded-sm text-white/90 overflow-hidden"
                :class="ev.color"
                :style="{
                top: '2px',
                height: (cellHeight-4)+'px'
              }"
            >
              {{ ev.title }}
            </div>
          </template>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import dayjs from 'dayjs'

interface Event {
  id: string
  title: string
  start: string // ISO date
  end?: string
  color?: string
}

const cellHeight = 48 // px per hour row

const events = ref<Event[]>([
  { id: '1', title: 'Intro PHP and setting up env', start: '2025-05-12T07:00:00', color: 'bg-purple/40' },
  { id: '2', title: 'Monday Wake‑Up Hour', start: '2025-05-12T08:00:00', color: 'bg-blue-600/40' },
  { id: '3', title: 'Financial Update', start: '2025-05-12T10:00:00', color: 'bg-yellow-600/40' },
  { id: '4', title: 'Variables, conditions & loops', start: '2025-05-13T11:00:00', color: 'bg-purple-600/40' },
  { id: '5', title: 'Meeting with a client', start: '2025-05-16T08:00:00', color: 'bg-pink-600/40' },
  { id: '6', title: 'Webinar: Figma', start: '2025-05-16T11:00:00', color: 'bg-green-600/40' },
  { id: '7', title: 'Lunch with John', start: '2025-05-15T12:00:00', color: 'bg-teal-600/40' },
  { id: '8', title: 'Workout with John', start: '2025-05-13T13:00:00', color: 'bg-teal-800/40' }
])

// current week start (Sunday)
const current = ref(dayjs('2025-05-12'))

const hours = Array.from({ length: 13 }, (_, i) => 7 + i) // 7‑19

const weekDays = computed(() => {
  const start = current.value.startOf('week')
  return Array.from({ length: 7 }, (_, i) => ({
    iso: start.add(i, 'day').format('YYYY-MM-DD'),
    label: start.add(i, 'day').format('ddd DD'),
  }))
})

const gridStyle = computed(() => {
  const cols = 1 + 7 // time col + 7 days
  return {
    gridTemplateColumns: `80px repeat(${cols - 1}, 1fr)`,
    gridTemplateRows: `repeat(${hours.length}, ${cellHeight}px)`
  }
})

function tLabel(h: number) {
  return dayjs().hour(h).minute(0).format('h A')
}

function eventsForSlot(day: { iso: string }, hour: number) {
  return events.value.filter(ev => dayjs(ev.start).format('YYYY-MM-DD') === day.iso && dayjs(ev.start).hour() === hour)
}

function nextWeek() {
  current.value = current.value.add(1, 'week')
}
function prevWeek() {
  current.value = current.value.subtract(1, 'week')
}

const formattedRange = computed(() => {
  const start = current.value.startOf('week')
  const end = current.value.endOf('week')
  return `${start.format('MMM DD')} – ${end.format('MMM DD YYYY')}`
})
</script>
