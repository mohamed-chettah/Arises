<template>
  <form @submit.prevent="handleSend" class="bg-purple/20 border-[1px] border-purple/20 flex items-center gap-4 bg-[#1d1640] px-6 py-4 rounded-lg">
    <!-- attachment placeholder (paperclip) -->
    <label class="cursor-pointer text-gray-400 hover:text-gray-200">
      <UIcon name="i-lucide-paperclip" class="w-5 h-5" />
      <input type="file" class="hidden" />
    </label>

    <!-- message textarea -->
    <textarea
        v-model="text"
        rows="1"
        placeholder="Talk with Arises AI"
        class="flex-1 bg-transparent resize-none outline-none text-sm text-gray-200 placeholder-gray-500"
    />

    <!-- submit button -->
    <button
        type="submit"
        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue hover:bg-purple/20 text-sm text-gray-200"
    >
      <UIcon name="i-lucide-square-arrow-up" class="w-4 h-4" />
    </button>
  </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const emit = defineEmits<{ (e: 'send', text: string): void }>()
const text = ref('')

function handleSend() {
  if (!text.value.trim()) return
  emit('send', text.value.trim())
  text.value = ''
}
</script>