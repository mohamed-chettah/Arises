<script setup lang="ts">
defineProps({
  classT: {
    type: String,
    default: ''
  },
  text: {
    type: String,
    default: ''
  }
})

</script>

<template>
  <a target="_blank" href="https://chromewebstore.google.com/detail/arises-pomodoro-timer-dis/aheohjodpllofjdihniljfkppcacpeib">
    <UButton type="submit" :class="classT ? classT : 'bg-[#445EF2] hover:bg-[#445EF2]/80 cta w-full inter'" >
      <NuxtImg src="/images/devicon_chrome.svg" width="16" height="16" class="mr-2" />
      {{ text ? text : 'Add to Chrome — free'}}
    </UButton>
  </a>
</template>

<style scoped>

</style>