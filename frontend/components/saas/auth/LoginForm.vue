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

  <div class="w-full grid grid-cols-2 gap-4 justify-center items-center min-h-screen bg-background px-42">
    <div class="w-96 p-6 rounded-lg bg-primary shadow-md col-span-1">
      <h2 class="text-2xl font-bold text-center mb-6">Welcome in Arises</h2>
      <UButton @click="loginWithGoogle" class="cursor-pointer" icon="i-mdi-google">
        Login With Google
      </UButton>
    </div>

    <div class="w-96 p-6 rounded-lg bg-primary shadow-md col-span-1">
      <NuxtImg src="./images/img.png" alt="Login Image" class="w-full h-auto rounded-lg mb-6" />
    </div>
  </div>

</template>

<style scoped>

</style>