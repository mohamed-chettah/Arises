
<script setup lang="ts">
import { ref, computed } from 'vue'
import dayjs from 'dayjs'


const props = defineProps({
  slots: {
    type: Array as () => Event[],
    default: () => []
  }
})
const toast = useToast()

interface Event {
  id?: number
  title: string
  start: string // ISO date
  end?: string
  color?: string
  choice?: boolean
}

const events = ref<Event[]>([
  { id: 1, title: 'Intro PHP and setting up env', start: '2025-05-6T07:00:00', color: 'bg-blue/40' },
  { id: 2, title: 'Monday Wake‑Up Hour', start: '2025-05-08T08:00:00', color: 'bg-blue-600/40' },
  { id: 3, title: 'Financial Update', start: '2025-05-09T10:00:00', color: 'bg-yellow-600/40' },
  { id: 4, title: 'Variables, conditions & loops', start: '2025-05-13T11:00:00', color: 'bg-purple-600/40' },
  { id: 5, title: 'Meeting with a client', start: '2025-05-05T08:00:00', color: 'bg-pink-600/40' },
  { id: 6, title: 'Webinar: Figma', start: '2025-05-04T11:00:00', color: 'bg-green-600/40' },
  { id: 7, title: 'Lunch with John', start: '2025-05-09T12:00:00', color: 'bg-teal-600/40' },
  { id: 8, title: 'Workout with John', start: '2025-05-12T13:00:00', color: 'bg-teal-800/40' }
])

function eventsAt(isoDate: string, hour: number) {
  return events.value.filter((event) => {
    const date = dayjs(event.start)
    return date.format('YYYY-MM-DD') === isoDate && date.hour() === hour
  })
}
// current week start (Sunday)
const current = ref(dayjs('2025-05-07'))

const hours = Array.from({ length: 7 }, (_, i) => 7 + i) // 7‑19

const weekDays = computed(() => {
  const start = current.value.startOf('week')
  return Array.from({ length: 7 }, (_, i) => ({
    iso: start.add(i, 'day').format('YYYY-MM-DD'),
    label: start.add(i, 'day').format('ddd DD'),
  }))
})

watch(() => props.slots, (newSlots) => {
  newSlots.map((slot: any) => {
    events.value.push({
      id: events.value.length + 1,
      title: slot.title,
      start: slot.start,
      end: slot.end,
      color: slot.color,
      choice: true,
    })
  })
  console.log(events.value)
})

function confirmEvent(id: number){
  const event = events.value.find(event => event.id === id)
  if (event) {
    event.choice = false
    event.color = 'bg-green-600/40'
  }
  toast.add({
    title: 'Event confirmed',
    description: `You have confirmed the event: ${event?.title}`,
  } as any)
}

function removeEvent(id: number){
  events.value = events.value.filter(event => event.id !== id)
}
</script>

<template>
  <div class="border-[1px] border-purple/20  rounded-lg p-3 w-full flex flex-col">

    <!-- Pseudocode -->
    <table class="overflow-y-auto scrollbar-thin scrollbar-thumb-purple/40 scrollbar-track-transparent ">
      <thead>
      <tr>
        <th></th> <!-- vide pour l'heure -->
        <th v-for="day in weekDays" class="text-sm text-grey pb-2 font-medium">{{ day.label }}</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="hour in hours" :key="hour" class="rounded-lg ">
        <td class="rounded-lg  w-10 pr-2 text-right text-xs text-grey">{{ hour }}:00</td>
        <td
            v-for="day in weekDays"
            :key="day.iso"
            class="rounded-lg relative border border-grey-calendar h-[60px]"
        >
          <div
              v-for="event in eventsAt(day.iso, hour)"
              :key="event.id"
              class="rounded-lg absolute inset-0 p-1 text-[10px] text-white rounded-md"
              :class="event.color"
          >
            {{ event.title }}
            <div v-if="event.choice">
              <UButton @click="confirmEvent(event.id)" class="cursor-pointer" icon="i-lucide-check" size="xs" color="primary" variant="solid"></UButton>              <UButton icon="x" class="absolute right-1 top-1 bg-red/40 text-white rounded-full p-1" />
              <UButton @click="removeEvent(event.id)" class="cursor-pointer" icon="i-lucide-x" size="xs" color="primary" variant="solid"></UButton>              <UButton icon="x" class="absolute right-1 top-1 bg-red/40 text-white rounded-full p-1" />
            </div>
          </div>
        </td>
      </tr>
      </tbody>
    </table>

  </div>
</template>
