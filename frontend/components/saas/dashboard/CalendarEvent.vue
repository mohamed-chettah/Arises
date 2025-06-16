<script setup lang="ts">
import { computed } from 'vue'

// **🔥 TYPE ÉTENDU POUR GÉRER SLOTS ET ÉVÉNEMENTS**
interface CalendarItem {
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
  type?: 'slot' | 'event'
  originalSlot?: any
}

const props = defineProps<{
  event: CalendarItem
  formatTime: (date: string) => string
}>()

const emit = defineEmits<{
  dragstart: [event: DragEvent, calendarEvent: CalendarItem]
  dragend: [event: DragEvent]
  'slot-accepted': [slot: any]
  'slot-rejected': [slot: any]
}>()

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
    console.log('🟢 Acceptation du slot:', props.event.originalSlot.title)
    emit('slot-accepted', props.event.originalSlot)
  }
}

function rejectSlot() {
  if (props.event.type === 'slot' && props.event.originalSlot) {
    console.log('🔴 Rejet du slot:', props.event.originalSlot.title)
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
const eventClasses = computed(() => {
  const baseClasses = ['absolute rounded border-l-3 p-1 text-xs font-medium shadow-sm z-10']
  
  if (props.event.type === 'slot') {
    // **🔥 COULEUR DYNAMIQUE POUR LES SLOTS**
    const isAccepted = props.event.originalSlot?.choice === true
    
    // Debug pour voir l'état
    console.log(`🎨 Slot "${props.event.title}":`, {
      choice: props.event.originalSlot?.choice,
      isAccepted,
      color: isAccepted ? 'vert' : 'orange'
    })
    
    const colorClasses = isAccepted 
      ? 'bg-purple-500/40 border-l-purple-500/40 text-purple-900'
      : 'bg-orange-500/40 border-l-orange text-orange-900'
    
    return [
      ...baseClasses,
      colorClasses,
      'cursor-default',
    ]
  } else {
    // **🔥 COULEUR NORMALE POUR LES ÉVÉNEMENTS**
    return [
      ...baseClasses,
      `${props.event.color} border-l-purple`,
      'cursor-move event-draggable',
      { 'opacity-0': !props.event.isStartCell }
    ]
  }
})
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
      
      <!-- **🔥 ACTIONS POUR LES SLOTS NON ACCEPTÉS** -->
      <div v-if="event.type === 'slot' && event.originalSlot?.choice === false" class="flex gap-1 mt-1">
        <UButton
          icon="i-lucide-check" 
          @click="acceptSlot" 
          variant="solid"
          class="cursor-pointer"
        />
        <UButton 
          icon="i-lucide-x" 
          @click="rejectSlot" 
          variant="solid"
          class="cursor-pointer"
        />
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