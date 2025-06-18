<script setup lang="ts">
import type {Slot} from "~/types/Slot";

const props = defineProps<{ loading: boolean, slots: Slot[] | null }>()

const emit = defineEmits<{ (e: 'send', text: string): void }>()
const text = ref('')

function handleSend() {
  if (!text.value.trim() || props.loading) return
  emit('send', text.value.trim())
  text.value = ''
}
</script>

<template>

  <form @submit.prevent="handleSend">
      <div v-if="slots && slots?.length > 0" class="mb-3 flex justify-between">
        <UButton class="text-white text-[12px] hover:text-purple hover:bg-white px-3 py-1 inter cta shadow-xl bg-gradient-to-r from-[#A480F2] via-[#9977E2] to-[#5F4A8C] rounded-lg">
          Accept Events
        </UButton>
        <UButton class="text-white text-[12px] hover:text-purple hover:bg-white px-3 py-1 inter cta shadow-xl bg-gradient-to-r from-[#A480F2] via-[#9977E2] to-[#5F4A8C] rounded-lg">
          Remove Events
        </UButton>
      </div>

    <div  class="bg-white border-[4px] border-purple/20 flex flex-row items-center gap-4 px-4 py-2 rounded-lg">
      <UTextarea
          v-model="text"
          :rows="1"
          :maxrows="2"
          :maxlength="300"
          variant="none"
          autoresize
          @keyup.enter="handleSend"
          placeholder="Ask Arises AI"
          class="text-black custom-scrollbar overflow-y-auto flex-1 bg-transparent resize-none outline-none text-xs placeholder-gray-500 overflow-y-auto scrollbar-thin scrollbar-thumb-purple/40 scrollbar-track-transparent "
      />

      <UButton
          type="submit"
          :disabled="loading || !text.trim()"
          icon="i-lucide-square-arrow-up"
          class="cursor-pointer disabled:bg-purple inline-flex items-center text-primary gap-2 px-3 py-2 rounded-lg bg-purple hover:bg-purple/20  text-sm text-white hover:text-gray-500"
      />
    </div>
  </form>
</template>


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