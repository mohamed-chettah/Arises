<script setup lang="ts">
import FormWaitlist from "~/components/FormWaitlist.vue";
</script>

<template>
  <div class="flex items-center justify-center flex-col gap-12 mt-10 px-4">


    <div class="flex flex-col justify-center gap-3 items-center">
      <UBadge
          icon="i-lucide-zap"
          size="sm"
          class="border border-primary/40 bg-white/10 backdrop-blur-sm text-white px-4 py-1 rounded-xl font-medium tracking-wide"
          color="primary"
          variant="solid"
      >
        Inspired by Solo Leveling
      </UBadge>
      <h1 class="bank-gothic-bold text-white text-[28px] sm:text-[40px] leading-tight text-center">
        Your smart AI calendar<br> to plan better and level up faster.
      </h1>
    </div>



    <div class="w-full sm:w-1/3 mt-2">
      <h2 class="inter text-white text-lg sm:text-xl text-center">
        The SaaS is <span class="bg-[#A480F2]/50 px-3 py-1 rounded-lg">Coming Soon</span>
      </h2>

      <FormWaitlist />
    </div>

    <USeparator color="primary" class="w-full sm:w-1/2 text-[#A480F2]" />

    <NuxtLink to="/chrome-extension" class="w-full sm:w-1/4">
      <UButton type="submit" class="bg-[#445EF2] hover:bg-[#445EF2]/70 cta w-full inter">
        Install the Chrome Extension
      </UButton>
    </NuxtLink>

    <UCard class="w-full" variant="subtle">
      <template #header>
        <Placeholder class="h-8" />
      </template>

      <Placeholder class="h-32" />

      <template #footer>
        <Placeholder class="h-8" />
      </template>
    </UCard>
  </div>

  <Footer />
</template>
