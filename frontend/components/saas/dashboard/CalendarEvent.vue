<script setup lang="ts">
import { computed } from 'vue'

interface CalendarEventProps {
  event: {
    id: string
    title: string
    start: string
    end: string
    color: string
    width: number
    leftOffset: number
    topOffset: number
    height: number
    isStartCell: boolean
  }
  formatTime: (date: string) => string
}

interface CalendarEventEmits {
  dragstart: [event: DragEvent, calendarEvent: any]
  dragend: [event: DragEvent]
}

const props = defineProps<CalendarEventProps>()
const emit = defineEmits<CalendarEventEmits>()

// **🔥 GESTIONNAIRES D'ÉVÉNEMENTS**
function onDragStart(event: DragEvent) {
  emit('dragstart', event, props.event)
}

function onDragEnd(event: DragEvent) {
  emit('dragend', event)
}

// **🔥 STYLES CALCULÉS**
const eventStyles = computed(() => ({
  height: props.event.isStartCell ? `${props.event.height}px` : '60px',
  width: props.event.isStartCell ? `${props.event.width}%` : '100%',
  left: props.event.isStartCell ? `${props.event.leftOffset}%` : '0%',
  top: props.event.isStartCell ? `${props.event.topOffset}%` : '0%'
}))

// **🔥 CLASSES CSS CALCULÉES**
const eventClasses = computed(() => [
  'absolute rounded border-l-3 border-l-purple p-1 text-xs font-medium shadow-sm cursor-move z-10 event-draggable',
  props.event.color,
  { 'opacity-0': !props.event.isStartCell }
])
</script>

<template>
  <div 
    :class="eventClasses"
    :style="eventStyles"
    draggable="true"
    @dragstart="onDragStart"
    @dragend="onDragEnd"
  >
    <div 
      v-if="event.isStartCell"
      class="font-semibold text-[10px] px-1"
    >
      <p class="font-semibold text-xs wrap-break-word line-clamp-2">{{ event.title }}</p>
      <p class="text-gray-500 font-normal">
        {{ formatTime(event.start) }}-{{ formatTime(event.end) }}
      </p>
    </div>
  </div>
</template>

<style scoped>
/* **🔥 OPTIMISATIONS PERFORMANCE DRAG & DROP** */
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
</style> 