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
    <div v-if="loading" class="self-start flex gap-1 px-3 py-2">
      <span class="w-2.5 h-2.5 bg-[#26204d] rounded-full animate-bounce"></span>
      <span class="w-2.5 h-2.5 bg-[#26204d] rounded-full animate-bounce"></span>
      <span class="w-2.5 h-2.5 bg-[#26204d] rounded-full animate-bounce"></span>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Message {
  id: string
  content: string
  role: 'user' | 'assistant'
}

const props = defineProps<{ messages: Message[], loading: boolean }>()
const chatBox = ref<HTMLElement | null>(null)

watch(
    () => props.messages.length,
    async () => {
      await nextTick()
      chatBox.value?.scrollTo({ top: chatBox.value.scrollHeight, behavior: 'smooth' })
    }
)
</script>

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
