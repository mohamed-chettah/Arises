<script setup lang="ts">
import type {FormSubmitEvent} from "@nuxt/ui";
import z from "zod";

const runtimeConfig = useRuntimeConfig()
const apiUrl = runtimeConfig.public.apiBase

const schema = z.object({
  email: z.string().email('Invalid email'),
  password: z.string().min(8, 'Must be at least 8 characters')
})

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  email: undefined,
  password: undefined
})

const toast = useToast()

async function loginWithGoogle(){
  const { data, error } = await useFetch<{ url: string }>(`${apiUrl}/auth/google`)

  if (error.value) {
    console.error('Error connecting to Google:', error.value)
    return
  }

  const redirectUrl = data.value?.url
  if (redirectUrl) {
    window.location.href = redirectUrl
  } else {
    console.warn('No redirect URL provided by Google.')
  }
}

async function onSubmit(event: FormSubmitEvent<Schema>){
  const { data, error } = await useFetch<{ token: string }>(`${apiUrl}/login`, {
    method: 'POST',
    body: {
      email: state.email,
      password: state.password
    }
  })

  if (error.value) {
    toast.add({ title: 'Error', description: error.value.message, color: 'error' })
    return
  }

  if (data.value?.token) {
    localStorage.setItem('token', data.value.token)
    toast.add({ title: 'Success', description: 'Logged in successfully!', color: 'success' })
    window.location.href = '/dashboard'
  } else {
    toast.add({ title: 'Error', description: 'Login failed. Please try again.', color: 'error' })
  }
}
</script>

<template>

  <div class="w-full flex flex-col items-center justify-center min-h-screen bg-background">
    <div class="w-96 p-6 rounded-lg bg-primary shadow-md">
      <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
      <UButton @click="loginWithGoogle" class="cursor-pointer" > Login With Google</UButton>
      ____
      <p class="text-center text-gray-500 mt-4">or</p>
      <h1>Login With Email</h1>
      <UForm :schema="schema" :state="state" class="space-y-4" @submit="onSubmit">
        <UFormField label="Email" name="email">
          <UInput v-model="state.email" />
        </UFormField>

        <UFormField label="Password" name="password">
          <UInput v-model="state.password" type="password" />
        </UFormField>

        <UButton type="submit">
          Submit
        </UButton>
      </UForm>
      <p class="text-center text-gray-500 mt-4">Don't have an account? <a href="/auth/register" class="text-blue-500 hover:underline">Register</a></p>
    </div>
  </div>

</template>

<style scoped>

</style>