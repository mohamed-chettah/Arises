<script setup lang="ts">
import * as z from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

const schema = z.object({
  email: z.string().email('Invalid email'),
  name: z.string().min(2, 'Must be at least 2 characters')
})

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  email: undefined,
  name: undefined
})

const toast = useToast()

async function onSubmit(event: FormSubmitEvent<Schema>) {
  try {
      const { data, status, error, refresh, clear } = await useFetch('https://backend.arises.app/api/waitlist', {
      method: 'POST',
      body: {
        email: state.email,
        name: state.name
      }
    })

    if (error && status.value !== 200 || status.value !== 201) {
      toast.add({
        title: 'Error',
        description: 'Something went wrong. Please try again later.',
        color: 'error'
      })
      return
    }

    toast.add({
      title: 'Success',
      description: 'You have been added to the waitlist!',
      color: 'success'
    })

    // Optionnel : reset le formulaire
    state.email = ''
    state.name = ''

  } catch (error) {
    console.error(error)
    toast.add({
      title: 'Error',
      description: 'Something went wrong. Please try again later.',
      color: 'error'
    })
  }

}
</script>


<template>
  <UForm :schema="schema" :state="state" class="w-full flex flex-col gap-2 mt-5" @submit="onSubmit">

    <UInput  icon="i-lucide-mail" v-model="state.email" class="input " type="email" placeholder="Email Address"/>

    <UInput icon="i-lucide-user"  v-model="state.name" type="text" class="input" placeholder="Name"/>

    <UButton type="submit" class="bg-[#A480F2] hover:bg-[#A480F2]/70 cta w-full inter">
      Get Notified ->
    </UButton>
  </UForm>
</template>

<style>


</style>

