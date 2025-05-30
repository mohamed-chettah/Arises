<script setup lang="ts">
const runtimeConfig = useRuntimeConfig()
const apiUrl = runtimeConfig.public.apiBase

/**
 * Redirect the user to Google OAuth.
 */
const loginWithGoogle = async () => {
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
  <!-- Page wrapper -->
  <div class="w-full min-h-screen flex items-center justify-center bg-background px-4">
    <!-- Card container -->
    <div class="max-w-6xl w-full min-h-[90%]] grid md:grid-cols-2 rounded-[20px] shadow-xl overflow-hidden shadow-lg">
      <!-- ☁️ Left column : Google Auth only -->
      <div class="flex flex-col gap-6 py-6 px-10">
        <!-- Logo -->
        <NuxtImg
            src="/logo_without_text.svg"
            alt="Logo"
            class="w-8 h-8"
        />

        <!-- Heading -->
        <div class="space-y-2">
          <h1 class="text-2xl font-semibold text-white">Welcome Back to Arises</h1>
          <p class="text-sm text-gray-500">
            Elevate yourself — gain clarity, master your time, and grow into the person you’re meant to be.
          </p>
        </div>

        <!-- Google button -->
        <UButton class="bg-white border border-[#A480F2]/70 cursor-pointer text-center flex justify-center rounded-[8px] hover:text-white w-48 cursor-pointer text-black" size="lg" icon="i-mdi-google" @click="loginWithGoogle">
          Login With Google
        </UButton>

        <!-- Spacing to push footer to bottom -->
        <div class="flex-grow" />

        <!-- Optional small footer -->
        <p class="text-xs text-gray-400">
          © 2025 Arises. All rights reserved.
        </p>
      </div>

      <!-- 🎬 Right column : hero image -->
      <div class="relative hidden md:block">
        <!-- Product image -->
        <NuxtImg src="/images/img.png" alt="Product image" class="object-cover w-full h-full" densities="x1 x2" />

        <!-- Gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />

        <!-- Headline overlay -->
        <div class="absolute bottom-8 left-8 right-8 text-white">
          <h2 class="text-2xl font-semibold leading-snug">
            Manage your life like never before
          </h2>
          <p class="text-sm mt-4">
            Arises is more than a tool — it's your personal system to take back control of your time, your focus, and your growth.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Aucune règle supplémentaire nécessaire thanks to Tailwind */
</style>
