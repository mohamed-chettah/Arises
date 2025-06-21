<script setup lang="ts">
import { CalendarDate, DateFormatter, getLocalTimeZone } from '@internationalized/date'

import * as z from 'zod'
import {hours} from "~/types/GoogleCalendar";
import {useCalendarStore} from "~/store/CalendarStore";

const schema = z.object({
  description:  z.string().optional(),
  title: z.string().min(4, 'Must be at least 4 characters').max(20, 'Must be at most 20 characters'),
})

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
})
const calendarStore = useCalendarStore()
const items = ref(hours)
const start = ref('08:00')
const end = ref('09:00')
const title = ref('')
const description = ref('')

const df = new DateFormatter('en-US', {
  dateStyle: 'medium'
})
const open = ref(false)
const toast = useToast()
const modelValue = shallowRef(new CalendarDate(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate()))

async function onSubmit() {
  if(modelValue.value === null || start.value === null || end.value === null || title.value === '' || title.value.length < 4) {
    toast.add({
      title: 'Error',
      description: 'Please fill in all required fields correctly.',
      color: 'error',
      duration: 3000,
    })
    return
  }

  await calendarStore.createEvent(modelValue.value, start.value, end.value, title.value, description.value)

  // close the modal after creation
  open.value = false
}
</script>

<template>
  <UModal
      v-model:open="open"
      title="New Event"
      :close="{
      color: 'primary',
      class: 'cursor-pointer hover:text-purple'
    }"
  >
    <UButton  variant="outline"
              class="rounded-lg hover:bg-purple/20 hover:text-primary cursor-pointer inter text-gray-500"
              size="sm" label="New Event" icon="i-heroicons-plus" color="neutral"/>

    <template #body>
      <UForm :schema="schema" :state="state" class="space-y-4">
        <div class="flex flex-col gap-5">

          <UInput placeholder="Titre" v-model="title" required/>
          <UPopover>
            <UButton color="neutral" variant="subtle" icon="i-lucide-calendar">
              {{ modelValue ? df.format(modelValue.toDate(getLocalTimeZone())) : 'Select a date' }}
            </UButton>

            <template #content>
              <UCalendar v-model="modelValue" class="p-2" color="neutral" required/>
            </template>
          </UPopover>

          <div class="flex items-center gap-5">
            <USelect v-model="start" :items="items" class="w-48 text-sm" required />
            <UIcon name="i-heroicons-arrow-small-right" class="text-gray-500" />
            <USelect v-model="end" :items="items" class="w-48" required/>
          </div>

          <UTextarea placeholder="Description" v-model="description"/>

        </div>

        <UButton @click="onSubmit" :loading="calendarStore.loadingCreation"  variant="outline"
                 color="neutral"
                 icon="i-heroicons-plus"
                 active-color="primary"
                 class="rounded-lg hover:bg-purple/20 hover:text-primary
                 rounded-lg text-gray-500 cursor-pointer inter"
                 size="sm">
          Create Event
        </UButton>
      </UForm>
    </template>
  </UModal>
</template>

<style scoped>

</style>