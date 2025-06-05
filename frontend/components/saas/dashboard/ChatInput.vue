<template>
  <form @submit.prevent="handleSend" class="bg-purple/20 border-[1px] border-purple/20 flex flex-row items-center gap-4 px-6 py-4 rounded-lg">

    <textarea
        v-model="text"
        rows="1"
        autoresize
        @keydown.enter="handleSend"
        placeholder="Talk with Arises AI"
        class="text-black custom-scrollbar overflow-y-auto max-h-[480px] flex-1 bg-transparent resize-none outline-none text-sm placeholder-gray-500 overflow-y-auto scrollbar-thin scrollbar-thumb-purple/40 scrollbar-track-transparent "
    />

    <UButton
        type="submit"
        :disabled="loading"
        :loading="loading"
        icon="i-lucide-square-arrow-up"
        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue hover:bg-purple/20 text-sm text-gray-200"
    />
  </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{ loading: boolean }>()

const emit = defineEmits<{ (e: 'send', text: string): void }>()
const text = ref('')

// event key enter
// to send the message


function handleSend() {
  if (!text.value.trim() || props.loading) return
  emit('send', text.value.trim())
  text.value = ''
}
</script>

<style>
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