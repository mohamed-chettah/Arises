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
  await toast.add({title: 'Success', description: 'The form has been submitted.', color: 'success'})
  // TODO CALL API TO SUBMIT FORM AND PENSER A VERIFIER L'origine dans le back (arises.app)
}
</script>

<template>
  <UForm :schema="schema" :state="state" class="w-full flex flex-col gap-2 mt-5" @submit="onSubmit">

    <UInput v-model="state.email"  placeholder="Email Adress"/>

    <UInput v-model="state.name" placeholder="Name"/>

    <UButton type="submit" class="bg-[#A480F2] hover:bg-[#A480F2]/70 cta w-full inter">
      Get Notified
    </UButton>
  </UForm>
</template>

