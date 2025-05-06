<template>
  <div class="flex min-h-screen bg-background">
    <!-- nav / sidebar -->
    <SideBar class="w-64 shrink-0" />


    <div class="flex-1 p-6">
      <!-- Welcome heading -->
      <h1 class="text-white bank-gothic text-2xl mb-6">Welcome Mohamed !</h1>

      <!-- Content grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chat column -->
        <div class="flex flex-col gap-5 rounded-xl overflow-hidden">
          <ChatView :messages="messages" class="flex-1" />
          <ChatInput @send="addMessage" />
        </div>

        <!-- Calendar column -->
        <CalendarView />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ChatView from '~/components/saas/ChatView.vue'
import ChatInput from '~/components/saas/ChatInput.vue'
import CalendarView from '~/components/saas/CalendarView.vue'
import SideBar from "~/components/saas/SideBar.vue";

interface Message { id: string; role: 'user' | 'assistant'; content: string }
const messages = ref<Message[]>([
  {
    id: '1',
    role: 'assistant',
    content: 'Hello, how can I assist you today?'
  },
  {
    id: '2',
    role: 'user',
    content: 'Can you help me with my schedule?'
  },
  {
    id: '3',
    role: 'assistant',
    content: 'Sure! What do you need help with?'
  }
])

function addMessage(text: string) {
  messages.value.push({ id: Date.now().toString(), role: 'user', content: text })
  // 👉 integrate backend call then push assistant reply
}
</script>