<script setup lang="ts">
const runtimeConfig = useRuntimeConfig()
const apiUrl = runtimeConfig.public.apiBase

async function loginWithGoogle(){
  const { data, error } = await useFetch<{ url: string }>(`${apiUrl}/app/auth/google`)

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
</script>

<template>

  <div class="w-full flex flex-col items-center justify-center min-h-screen bg-background">
    <div class="w-96 p-6 rounded-lg bg-primary shadow-md">
      <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
      <UButton @click="loginWithGoogle" class="cursor-pointer" > Login With Google</UButton>
      <UButton @click="loginWithGoogle" class="cursor-pointer" > Sign In With Google</UButton>
    </div>
  </div>

</template>

<style scoped>

</style>