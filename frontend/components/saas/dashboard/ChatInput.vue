<script setup lang="ts">
import type {Slot} from "~/types/Slot";

const props = defineProps<{ loading: boolean, slots: Slot[] | null }>()

const emit = defineEmits<{ 
  (e: 'send', text: string): void,
  (e: 'accept-all-slots'): void,
  (e: 'reject-all-slots'): void
}>()

const text = ref('')

function handleSend() {
  if (!text.value.trim() || props.loading) return
  emit('send', text.value.trim())
  text.value = ''
}

function acceptAllSlots() {
  emit('accept-all-slots')
}

function rejectAllSlots() {
  emit('reject-all-slots')
}
</script>

<template>
  <form @submit.prevent="handleSend">
    <!-- **🔥 BANDE DE GESTION DES SLOTS (STYLE CURSOR)** -->
    <div v-if="slots && slots.length > 0" class="p-2 mx-auto flex flex-row justify-between rounded-tl-lg rounded-tr-lg w-[95%] bg-purple/20">
      <!-- Compteur de slots avec icône -->
      <div class="flex items-center gap-1 text-xs text-gray-500 font-medium inter">
        <span>{{ slots.length }}</span>
        <span class="">
           Event{{ slots.length > 1 ? 's' : '' }}
        </span>
      </div>

      <!-- Boutons Reject/Accept -->
      <div class="flex gap-2 inter ">
        <UButton
            @click="rejectAllSlots"
            class="cursor-pointer px-1 py-1 text-xs font-medium text-gray-500 hover:text-purple rounded-md transition-colors duration-200"
        >
          Reject
        </UButton>
        <UButton
            @click="acceptAllSlots"
            class="cursor-pointer px-1.5 py-1.5 hover:bg-purple hover:text-white text-xs font-medium text-gray-500 bg-white rounded-md transition-colors duration-200"
        >
          Accept
        </UButton>
      </div>
    </div>


<!--    <div class="mx-auto rounded-tl-lg rounded-tr-lg w-[97%] bg-purple/20">-->
<!--t-->
<!--    </div>-->
    <div class="bg-white border-[2px] border-purple/20 flex flex-row items-center gap-4 px-4 py-2 rounded-lg">
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