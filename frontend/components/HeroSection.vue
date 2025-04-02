<script setup lang="ts">
import FormWaitlist from "~/components/FormWaitlist.vue";
</script>

<template>


  <div class="flex items-center justify-center flex-col gap-12 mt-20">

    <h1 class="bank-gothic-bold text-white text-[40px] leading-none text-center ">Your smart AI calendar <br> to plan better and level up faster.</h1>


    <div class="w-1/3 mt-2 ">
      <h2 class="inter text-white text-xl text-center">The Saas is <span  class="bg-[#A480F2]/50 p-2 rounded-lg">Coming  Soon </span></h2>

      <FormWaitlist />
    </div>

    <USeparator color="primary" class="w-1/2 text-[#A480F2]" />

    <NuxtLink to="/chrome-extension" class="w-1/4">
      <UButton type="submit" class="bg-[#445EF2] hover:bg-[#445EF2]/70 cta w-full inter">
        Install the chrome extension
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

<style scoped>

</style>