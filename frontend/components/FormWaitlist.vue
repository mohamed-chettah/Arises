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
const loading = ref(false)

async function onSubmit(event: FormSubmitEvent<Schema>) {
  try {
    loading.value = true
    const { data, status, error, refresh, clear } = await useFetch(apiUrl +'/waitlist', {
      method: 'POST',
      body: {
        email: state.email,
        name: state.name
      }
    })

    loading.value = false
    if (error.value) {
      // if(error.value?.errors.email) {
      //   toast.add({
      //     title: 'Error',
      //     description: error.value?.errors.email[0],
      //     color: 'warning'
      //   })
      // }

      toast.add({
        title: 'Error',
        description: 'Something went wrong. Please try again later.',
        color: 'error'
      })
      return
    }

    toast.add({
      title: 'Success',
      description: 'Verification email sent. Check your inbox to confirm your subscription to the waitlist',
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

    <UInput icon="i-heroicons-envelope" v-model="state.email" class="input" type="email" required placeholder="Email Address" autocomplete="email"/>

    <UInput icon="i-heroicons-user"  v-model="state.name" type="text" class="input" required placeholder="Name" autocomplete="name"/>

    <UButton type="submit" loading-icon="" trailing-icon="i-heroicons-arrow-long-right-16-solid" class="bg-gradient-to-r from-[#A480F2] via-[#9977E2] to-[#5F4A8C] hover:bg-[#A480F2]/70 cta w-full inter" :loading="loading">
      Get into the waitlist
    </UButton>
    <p class="text-xs text-white text-center">The first 50 users will gain an access to the beta</p>
  </UForm>
</template>

<style>


</style>

