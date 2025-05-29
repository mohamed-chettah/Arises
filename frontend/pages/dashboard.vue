
<script setup lang="ts">
import ChatView from '~/components/saas/dashboard/ChatView.vue'
import ChatInput from '~/components/saas/dashboard/ChatInput.vue'
import CalendarView from '~/components/saas/dashboard/CalendarView.vue'
import SideBar from "~/components/saas/layout/SideBar.vue";
import Header from "~/components/saas/layout/Header.vue";
import type {Slot} from "~/types/Slot";

interface Message { id: string; role: 'user' | 'assistant'; content: string }
const messages = ref<Message[]>([])

const runtimeConfig = useRuntimeConfig()
const apiUrl = runtimeConfig.public.apiBase
const loading = ref(false)
const slots = ref(<Slot[]>[])

async function addMessage(text: string) {
  messages.value.push({ id: Date.now().toString(), role: 'user', content: text })

  const token = localStorage.getItem('token')

  try {
    loading.value = true
    const { data, status, error, refresh, clear } = await useFetch<{ message ?: string, slots ?: [] }>(apiUrl +'/arises-ai/ask', {
      method: 'POST',
      headers: {
        authorization : "Bearer " + token,
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

    if(data.value?.slots){
      data.value?.slots.map((slot: Slot) => {
        slot.color = 'bg-purple'
        slot.choice = true
        slots.value.push(slot)
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
      <SideBar class="w-16 shrink-0" />
    </div>

    <div class="flex-1 px-5 py-5">
<!--      <Header />-->
      <div class="pt-2 ">

        <!-- Content grid -->
        <div class="grid grid-cols-3 gap-6 ">
          <!-- Chat column -->
          <div class="h-full">
            <ChatView :messages="messages" :loading="loading" class="" />
            <ChatInput class="mt-5" @send="addMessage" :loading="loading" />
          </div>

          <!-- Calendar column -->
          <CalendarView :slot="slots" class="col-span-2" />
        </div>

      </div>

    </div>
  </div>
</template>
