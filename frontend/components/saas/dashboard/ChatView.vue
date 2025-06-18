
<script setup lang="ts">
import type { Message } from '~/types/Message'

const props = defineProps<{ messages: Message[], loading: boolean }>()
const chatBox = ref<HTMLElement | null>(null)

watch(
    () => props.messages.length,
    async () => {
      await nextTick()
      chatBox.value?.scrollTo({ top: chatBox.value.scrollHeight, behavior: 'smooth' })
    }
)

const text = ref('')

watch(
    () => props.loading,
    (newVal) => {
      if (newVal) {
        text.value = 'Thinking...'

        setTimeout(() => {
          text.value = 'Analyzing your calendar...'
        }, 2000) // Delay to simulate thinking

        setTimeout(() => {
          text.value = 'Preparing an answer...'
        }, 4000) // Delay to simulate thinking
      } else {
        text.value = ''
      }
    }
)

// // Scroll to bottom when the component is mounted
onMounted(() => {
  nextTick(() => {
    chatBox.value?.scrollTo({ top: chatBox.value.scrollHeight, behavior: 'smooth' })
  })
})

function acceeptAllEvents() {
  // Emit an event to accept all events
  // This function should be implemented in the parent component
  // to handle the acceptance of all events
  console.log('Accepting all events')
}
</script>

<template>
  <div
      ref="chatBox"
      class="h-full border-[1px] border-purple/20 bg-purple/20 flex flex-col gap-4 overflow-y-auto p-6 rounded-lg custom-scrollbar"
  >
    <template v-for="m in messages" :key="m.id">
      <div
          :class="[
          'max-w-[85%] px-4 py-3 text-sm leading-relaxed rounded-lg',
          m.role === 'user'
            ? 'bg-purple/20 font-semibold self-end '
            : 'bg-[#26204d] text-gray-200 self-start',
        ]"
      >
        <p class="text-xs line-clamp-8">{{ m.content }}</p>



      </div>

    </template>

    <!-- Loader bubble -->
    <div v-if="loading" class="">
      <p class="text-xs mt-2">{{ text }}</p>
    </div>
  </div>
</template>


<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(168, 85, 247, 0.6); /* Tailwind's purple-500 with opacity */
  border-radius: 4px;
}
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgba(168, 85, 247, 0.6) transparent; /* For Firefox */
}
</style>
