<script setup lang="ts">
import { computed } from 'vue'
import CalendarEvent from './CalendarEvent.vue'

const props = defineProps<{
  date: string
  hour: number
  isToday: boolean
  events: any[]
  dropPreview: any
  draggedEvent: any
  formatTime: (date: string) => string
}>()

const emit = defineEmits<{
  dragover: [event: DragEvent, date: string, hour: number]
  dragleave: [event: DragEvent]
  drop: [event: DragEvent, date: string, hour: number]
  eventDragstart: [event: DragEvent, calendarEvent: any]
  eventDragend: [event: DragEvent]
  'slot-accepted': [slot: any]
  'slot-rejected': [slot: any]
}>()

// **🔥 GESTIONNAIRES D'ÉVÉNEMENTS DRAG & DROP**
function onDragOver(event: DragEvent) {
  // **🔥 EMPÊCHER LE DRAG & DROP DES SLOTS**
  const target = event.target as HTMLElement
  const isSlot = target.closest('.event-draggable')?.getAttribute('data-status') === 'slot'
  if (isSlot) return

  emit('dragover', event, props.date, props.hour)
}

function onDragLeave(event: DragEvent) {
  emit('dragleave', event)
}

function onDrop(event: DragEvent) {
  // **🔥 EMPÊCHER LE DROP DES SLOTS**
  const target = event.target as HTMLElement
  const isSlot = target.closest('.event-draggable')?.getAttribute('data-status') === 'slot'
  if (isSlot) return

  emit('drop', event, props.date, props.hour)
}

function onEventDragStart(event: DragEvent, calendarEvent: any) {
  // **🔥 EMPÊCHER LE DRAG DES SLOTS**
  if (calendarEvent.type === 'slot') {
    event.preventDefault()
    return
  }
  emit('eventDragstart', event, calendarEvent)
}

function onEventDragEnd(event: DragEvent) {
  emit('eventDragend', event)
}

// **🔥 GESTIONNAIRES POUR LES SLOTS**
function onSlotAccepted(slot: any) {
  emit('slot-accepted', slot)
}

function onSlotRejected(slot: any) {
  emit('slot-rejected', slot)
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
      <p class="font-semibold text-sm wrap-break-word">{{ draggedEvent?.title }}</p>
      <div class="text-[10px] font-medium">
        {{ formatTime(dropPreview.newStart) }}-{{ formatTime(dropPreview.newEnd) }}
      </div>
    </div>
    
    <!-- **🔥 ÉVÉNEMENTS ET SLOTS DE LA CELLULE** -->
    <CalendarEvent 
      v-for="(event, index) in events" 
      :key="`${event.id}-${date}-${hour}-${index}`"
      :event="event"
      :format-time="formatTime"
      @dragstart="onEventDragStart"
      @dragend="onEventDragEnd"
      @slot-accepted="onSlotAccepted"
      @slot-rejected="onSlotRejected"
    />
  </div>
</template>

<style scoped>
/* Preview fluide */
.drop-preview {

  transform: translateZ(0);
  transition: opacity 0.1s ease-out;
}

</style> 