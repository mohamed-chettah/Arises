<script setup lang="ts">
import { computed } from 'vue'
import type { CalendarEventProps, CalendarEventEmits } from '~/types/GoogleCalendar'

const props = defineProps<CalendarEventProps>()
const emit = defineEmits<CalendarEventEmits>()

// **🔥 GESTIONNAIRES D'ÉVÉNEMENTS**
function onDragStart(event: DragEvent) {
  // **🔥 EMPÊCHER LE DRAG DES SLOTS**
  if (props.event.type === 'slot') {
    event.preventDefault()
    return
  }
  emit('dragstart', event, props.event)
}

function onDragEnd(event: DragEvent) {
  emit('dragend', event)
}

// **🔥 ACTIONS SPÉCIFIQUES AUX SLOTS**
function acceptSlot() {
  if (props.event.type === 'slot' && props.event.originalSlot) {
    props.event.originalSlot.choice = true
    // Émettre un événement pour notifier le parent
    emit('slot-accepted', props.event.originalSlot)
  }
}

function rejectSlot() {
  if (props.event.type === 'slot' && props.event.originalSlot) {
    // Émettre un événement pour notifier le parent
    emit('slot-rejected', props.event.originalSlot)
  }
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
  'absolute rounded border-l-3 p-1 text-xs font-medium shadow-sm z-10',
  props.event.color,
  { 'opacity-0': !props.event.isStartCell },
  // **🔥 STYLES DIFFÉRENTS SELON LE TYPE**
  props.event.type === 'slot' ? 'border-l-orange cursor-default' : 'border-l-purple cursor-move event-draggable'
])
</script>

<template>
  <div 
    :class="eventClasses"
    :style="eventStyles"
    :draggable="event.type !== 'slot'"
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
      
      <!-- **🔥 ACTIONS POUR LES SLOTS** -->
      <div v-if="event.type === 'slot' && !event.originalSlot?.choice" class="flex gap-1 mt-1">
        <button 
          @click="acceptSlot"
          class="bg-green-500 text-white text-[8px] px-1 py-0.5 rounded hover:bg-green-600"
        >
          ✓
        </button>
        <button 
          @click="rejectSlot"
          class="bg-red-500 text-white text-[8px] px-1 py-0.5 rounded hover:bg-red-600"
        >
          ✗
        </button>
      </div>
      
      <!-- **🔥 INDICATEUR SLOT ACCEPTÉ** -->
      <div v-if="event.type === 'slot' && event.originalSlot?.choice" class="mt-1">
        <span class="bg-green-500 text-white text-[8px] px-1 py-0.5 rounded">
          Accepté ✓
        </span>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* **🔥 OPTIMISATIONS PERFORMANCE DRAG & DROP** */
.event-draggable {
  will-change: transform, opacity;
  backface-visibility: hidden;
  transform: translateZ(0);
  user-select: none;
  -webkit-user-select: none;
  -webkit-touch-callout: none;
  display: block !important;
  overflow: hidden;
}

.event-draggable * {
  pointer-events: none !important;
  user-select: none !important;
  -webkit-user-select: none !important;
}

.event-draggable:hover {
  filter: brightness(1.1);
  transition: filter 0.1s ease;
}

.event-draggable:active {
  filter: brightness(0.95);
}

/* **🔥 STYLES SPÉCIFIQUES AUX SLOTS** */
button {
  pointer-events: auto !important;
}
</style> 