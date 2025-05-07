<template>
  <div class="flex min-h-screen bg-background">
    <!-- nav / sidebar -->
    <div class="flex">
      <SideBar class="w-64 shrink-0" />
    </div>


    <div class="flex-1">

      <Header />
      <!-- Welcome heading -->

      <div class="py-8 px-20">
        <h1 class="text-white bank-gothic text-2xl mb-6">Welcome Mohamed !</h1>

        <!-- Content grid -->
        <div class="grid grid-cols-3 gap-6 ">
          <!-- Chat column -->

          <ChatView :messages="messages" class="flex-1 col-span-1" />

          <!-- Calendar column -->
          <CalendarView class="col-span-2" />
        </div>
        <ChatInput class="my-5" @send="addMessage" />
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
import Header from "~/components/saas/Header.vue";

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