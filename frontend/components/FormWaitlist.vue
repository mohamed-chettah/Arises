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
const runtimeConfig = useRuntimeConfig()

const apiUrl = runtimeConfig.public.apiBase

async function onSubmit(event: FormSubmitEvent<Schema>) {
  try {
      const { data, status, error, refresh, clear } = await useFetch(apiUrl +'/waitlist', {
      method: 'POST',
      body: {
        email: state.email,
        name: state.name
      }
    })

    if (error.value) {
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

    <UInput  icon="i-heroicons-envelope" v-model="state.email" class="input" type="email" required placeholder="Email Address"/>

    <UInput icon="i-heroicons-user"  v-model="state.name" type="text" class="input" required placeholder="Name"/>

    <UButton type="submit" trailing-icon="i-heroicons-arrow-long-right-16-solid" class="bg-[#A480F2] hover:bg-[#A480F2]/70 cta w-full inter">
      Get Notified
    </UButton>
  </UForm>
</template>

<style>


</style>

