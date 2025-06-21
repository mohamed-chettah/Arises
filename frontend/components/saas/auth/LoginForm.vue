<script setup lang="ts">

const runtimeConfig = useRuntimeConfig()
const apiUrl = runtimeConfig.public.apiBase
const toast = useToast()

// **🔐 PROTECTION TEMPORAIRE**
const devPassword = ref('')
const isUnlocked = ref(false)
const DEV_ACCESS_CODE = 'arises2025' // Mot de passe temporaire

const checkDevAccess = () => {
  if (devPassword.value === DEV_ACCESS_CODE) {
    isUnlocked.value = true
    toast.add({
      title: 'Access granted!',
      description: 'Welcome to Arises Beta.',
      color: 'success',
      duration: 3000
    })
  } else {
    toast.add({
      title: '❌ Invalid access code',
      description: 'Please contact the development team.',
      color: 'error',
      duration: 3000
    })
  }
}

/**
 * Redirect the user to Google OAuth.
 */
const loginWithGoogle = async () => {
  const { data, error } = await useFetch<{ url: string }>(`${apiUrl}/app/auth/google`)

  if (error.value) {
    toast.add({
      title: 'Error',
      description: 'Failed to connect to Google. Please try again later.',
      color: 'error',
      duration: 5000
    })
    error.value = null
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
  <div class="w-full flex items-center justify-center px-4 ">

    <!-- Card container -->
    <div class="w-full bg-background max-w-6xl grid md:grid-cols-2 rounded-[20px] shadow-xl overflow-hidden border border-grey-calendar">
      <!-- ☁️ Left column : Google Auth only -->
      <div class="flex flex-col justify-between h-full gap-6 py-6 px-10">

        <NuxtLink to="/">
          <NuxtImg
              src="/logo_without_text.svg"
              alt="Logo"
              class="w-10 h-10"
          />
        </NuxtLink>
        <!-- Logo -->

        <!-- Heading -->
        <div class="space-y-2">
          <h1 class="text-2xl font-semibold text-white space-grotesk">Welcome Back to Arises</h1>
          <p class="text-sm text-grey">
            Elevate yourself — gain clarity, master your time, and grow into the person you’re meant to be.
          </p>
        </div>

        <!-- 🔐 Protection temporaire ou bouton Google -->
        <div v-if="!isUnlocked" class="space-y-4">
          <div class="space-y-2">
            <label class="text-sm text-gray-300">Beta Access</label>
            <input 
              v-model="devPassword"
              type="password"
              placeholder="Enter access code..."
              class="w-full mt-4 px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-purple-500"
              @keyup.enter="checkDevAccess"
            />
          </div>
          <UButton 
          class="text-xs bg-white border-t border-[#A480F2]/70 cursor-pointer text-center flex justify-center rounded-[8px] hover:text-white w-42 cursor-pointer text-black" 
            size="sm" 
            @click="checkDevAccess"
          >
            Access to the beta
          </UButton>
        </div>

        <!-- Google button (affiché seulement si déverrouillé) -->
        <UButton 
          v-else
          class="text-xs bg-white border-t border-[#A480F2]/70 cursor-pointer text-center flex justify-center rounded-[8px] hover:text-white w-42 cursor-pointer text-black" 
          size="sm" 
          icon="i-mdi-google" 
          @click="loginWithGoogle"
        >
          Continue with Google
        </UButton>

        <!-- Spacing to push footer to bottom -->
        <div class="flex-grow" />

        <!-- Optional small footer -->
        <p class="text-xs text-gray-400">
          © 2025 Arises. All rights reserved.
        </p>
      </div>

      <!-- 🎬 Right column : hero image -->
      <div class="relative hidden md:block m-2 h-full">
        <!-- Product image -->
        <NuxtImg src="./images/horizon.jpg" alt="Product image" preload  class="object-cover w-full xl:max-h-[550px] rounded-xl" densities="x1 x2" />

        <!-- Gradient overlay -->

        <!-- Headline overlay -->
        <div class="absolute bottom-8 left-8 right-8 text-white">
          <h2 class="text-2xl font-semibold leading-snug space-grotesk">
            Manage your life like never before
          </h2>
          <p class="text-sm mt-4 inter">
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
