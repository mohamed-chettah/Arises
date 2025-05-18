<script setup lang="ts">

</script>

<template>
<footer class="pb-5 mt-40 bg-[#A480F2]/20 p-10 rounded-xl">


  <div class="flex items-center justify-between">
    <UButton type="submit" href="#form-waitlist" class="bg-gradient-to-r from-[#A480F2] via-[#9977E2] to-[#5F4A8C] hover:bg-[#A480F2]/70 cta inter" >
      Join the Waitlist
    </UButton>
    <a href="https://mc-studio.eu" target="_blank" class="text-sm inter text-white">Made By MC Studio</a>
  </div>

  <USeparator class="mb-5 mt-10 text-[#A480F2] "  />

  <div class="flex justify-between">
    <p class="inter text-white text-sm">© Arises 2025</p>
    <div class="flex justify-center text-sm gap-4 underline underline-offset-2">
      <NuxtLink to="/terms" class="link inter text-white">Terms</NuxtLink>

      <span class="text-white"> | </span>

      <NuxtLink to="/privacy" class="link inter text-white">Privacy</NuxtLink>
    </div>
  </div>
</footer>
</template>

<style scoped>
.link{
  @apply hover:text-[#A480F2]
}
</style>