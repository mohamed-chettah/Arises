<template>
  <!-- Scrollable chat history -->
  <div
      ref="chatBox"
      class="border-[1px] border-purple/20 bg-purple/20 flex flex-col gap-4 overflow-y-auto scrollbar-thin scrollbar-thumb-purple/40 scrollbar-track-transparent  p-6 rounded-lg"
  >
    <template v-for="m in messages" :key="m.id">
      <div
          :class="[
          'max-w-[85%] px-4 py-3 text-sm leading-relaxed rounded-lg',
          m.role === 'user'
            ? 'bg-purple/30 text-white self-end'
            : 'bg-[#26204d] text-gray-200 self-start',
        ]"
      >
        <p class="text-xs">{{ m.content }}</p>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from 'vue'

interface Message {
  id: string
  content: string
  role: 'user' | 'assistant'
}

const props = defineProps<{ messages: Message[] }>()

const chatBox = ref<HTMLElement | null>(null)

// auto‑scroll to the newest message
watch(
    () => props.messages.length,
    async () => {
      await nextTick()
      chatBox.value?.scrollTo({ top: chatBox.value.scrollHeight, behavior: 'smooth' })
    }
)
</script>
