<script setup lang="ts">
import ChatView from '~/components/saas/dashboard/ChatView.vue'
import ChatInput from '~/components/saas/dashboard/ChatInput.vue'
import CalendarView from '~/components/saas/dashboard/CalendarView.vue'
import Header from '~/components/saas/layout/Header.vue';
import SideBar from "~/components/saas/layout/SideBar.vue";
import type {Slot} from "~/types/Slot";
import {useAuthStore} from "~/store/AuthStore";

definePageMeta({
  middleware: [
    'auth',
  ],
});

interface Message { id: string; role: 'user' | 'assistant'; content: string }
const messages = ref<Message[]>([])

const runtimeConfig = useRuntimeConfig()
const apiUrl = runtimeConfig.public.apiBase
const loading = ref(false)
const slots = ref(<Slot[]>[])
const authStore = useAuthStore()

async function addMessage(text: string) {
  messages.value.push({ id: Date.now().toString(), role: 'user', content: text })

  const token = useCookie('token').value
  if(!token){
    messages.value.push({ id: Date.now().toString(), role: 'assistant', content: "Please login to continue." })
    await authStore.logout()
  }

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
  <div class="flex max-h-screen bg-background overflow-y-hidden">
    <div class="flex">
      <SideBar class="w-16 shrink-0" />
    </div>

    <div class="flex-1 ">
      <Header />

      <section class="rounded-lg grid grid-cols-4 bg-white mr-2">
        <div class="col-span-1 border-r border-r-[0.5px] border-grey-calendar/20 h-screen p-4 flex flex-col h-full min-h-0">
          <!-- Zone scrollable des messages -->
          <div class="grow min-h-0">
            <ChatView :messages="messages" :loading="loading" />
          </div>

          <!-- Zone du champ de saisie -->
          <ChatInput class="mt-5 mb-10" @send="addMessage" :loading="loading" />
        </div>

        <div class="col-span-3 h-screen flex-1">
          <CalendarView :slot="slots" class="sm:col-span-3 col-span-1 row-span-2" />
        </div>
      </section>

    </div>
  </div>
</template>
