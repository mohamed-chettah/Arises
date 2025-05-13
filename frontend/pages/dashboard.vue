
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
])

const runtimeConfig = useRuntimeConfig()
const apiUrl = runtimeConfig.public.apiBase
const loading = ref(false)
const slots= ref([])

async function addMessage(text: string) {
  messages.value.push({ id: Date.now().toString(), role: 'user', content: text })

  // TODO call to laravel
  try {
    loading.value = true
    const { data, status, error, refresh, clear } = await useFetch(apiUrl +'/arises-ai/ask', {
      method: 'POST',
      headers: {
        authorization : "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjUwMDUvYXBpL2xvZ2luIiwiaWF0IjoxNzQ3MTY0NjI2LCJleHAiOjE3NDcxNzE4MjYsIm5iZiI6MTc0NzE2NDYyNiwianRpIjoiVWJxUjZMQ1hWcXEydFE2YiIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.sze1gRviR-VH4kRbdqYKwDD34T6_U90IlkUEt24JWEk",
      },
      body: {
        question: text,
        start: "2025-05-01T00:00:00Z",
        end: "2025-05-31T23:59:59Z",
      }
    })

    if(data.value?.message){
      messages.value.push({ id: Date.now().toString(), role: 'assistant', content: data.value?.message })
    } else {
      messages.value.push({ id: Date.now().toString(), role: 'assistant', content: "Sorry, I couldn't find any information." })
    }

    // todo placer les slots dans le calendrier
    if(data.value?.slots){
      console.log(data.value?.slots)
      slots.value = data.value?.slots.map((slot: any) => {
        return {
          title: slot.title,
          start: slot.start,
          end: slot.end,
          color: 'bg-purple',
          choice: true,
        }
      })
      console.log(slots.value)
    } else {
      slots.value = []
    }


    loading.value = false
    if (error.value) {
      return
    }
  } catch (error) {
    console.error(error)
  }
  finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen bg-background">
    <!-- nav / sidebar -->
    <div class="flex">
      <SideBar class="w-64 shrink-0" />
    </div>

    <div class="flex-1">

      <Header />
      <!-- Welcome heading -->

      <div class="py-8 px-26">
        <h1 class="text-white bank-gothic text-2xl mb-6">Welcome Mohamed !</h1>

        <!-- Content grid -->
        <div class="grid grid-cols-3 gap-6 ">
          <!-- Chat column -->
          <ChatView :messages="messages" :loading="loading" class="flex-1 col-span-1" />

          <!-- Calendar column -->
          <CalendarView :slots="slots" class="col-span-2" />
        </div>
        <ChatInput class="mt-5" @send="addMessage" :loading="loading" />
      </div>

    </div>
  </div>
</template>
