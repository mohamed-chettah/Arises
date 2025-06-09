<script setup lang="ts">
import { computed } from 'vue'
import CalendarEvent from './CalendarEvent.vue'

interface CalendarCellProps {
  date: string
  hour: number
  isToday: boolean
  events: any[]
  dropPreview: {
    date: string
    hour: number
    minutes: number
    topPercent: number
    height: number
    newStart: string
    newEnd: string
  } | null
  draggedEvent: any
  formatTime: (date: string) => string
}

interface CalendarCellEmits {
  dragover: [event: DragEvent, date: string, hour: number]
  dragleave: [event: DragEvent]
  drop: [event: DragEvent, date: string, hour: number]
  eventDragstart: [event: DragEvent, calendarEvent: any]
  eventDragend: [event: DragEvent]
}

const props = defineProps<CalendarCellProps>()
const emit = defineEmits<CalendarCellEmits>()

// **🔥 GESTIONNAIRES D'ÉVÉNEMENTS DRAG & DROP**
function onDragOver(event: DragEvent) {
  emit('dragover', event, props.date, props.hour)
}

function onDragLeave(event: DragEvent) {
  emit('dragleave', event)
}

function onDrop(event: DragEvent) {
  emit('drop', event, props.date, props.hour)
}

function onEventDragStart(event: DragEvent, calendarEvent: any) {
  emit('eventDragstart', event, calendarEvent)
}

function onEventDragEnd(event: DragEvent) {
  emit('eventDragend', event)
}

// **🔥 COMPUTED POUR VÉRIFIER LA PREVIEW**
const showDropPreview = computed(() => {
  return props.dropPreview && 
         props.dropPreview.date === props.date && 
         props.dropPreview.hour === props.hour
})

// **🔥 CLASSES CSS CALCULÉES**
const cellClasses = computed(() => [
  'relative border-r border-gray-200 last:border-r-0 hover:bg-blue-50/20 calendar-cell',
  { 'bg-purple-50/20': props.isToday }
])
</script>

<template>
  <div 
    :class="cellClasses"
    :data-date="date"
    :data-hour="hour"
    @dragover="onDragOver"
    @dragleave="onDragLeave"
    @drop="onDrop"
  >
    <!-- **🔥 ZONE DE PREVIEW PENDANT LE DRAG** -->
    <div 
      v-if="showDropPreview && dropPreview"
      class="absolute bg-purple/20 border-[1px] border-purple border-dashed rounded z-20 pointer-events-none drop-preview"
      :style="{
        height: `${dropPreview.height}px`,
        width: '95%',
        left: '2.5%',
        top: `${dropPreview.topPercent}%`
      }"
    >
      <p class="font-semibold text-sm">{{ draggedEvent?.title }}</p>
      <div class="text-[10px] font-medium">
        {{ formatTime(dropPreview.newStart) }}-{{ formatTime(dropPreview.newEnd) }}
      </div>
    </div>
    
    <!-- **🔥 ÉVÉNEMENTS DE LA CELLULE** -->
    <CalendarEvent 
      v-for="(event, index) in events" 
      :key="`${event.id}-${date}-${hour}-${index}`"
      :event="event"
      :format-time="formatTime"
      @dragstart="onEventDragStart"
      @dragend="onEventDragEnd"
    />
  </div>
</template>

<style scoped>
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
</style> 