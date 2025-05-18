<script setup lang="ts">
import FormWaitlist from "~/components/FormWaitlist.vue";
import VideoShowcase from "~/components/VideoShowcase.vue";
import ChromeButton from "~/components/ChromeButton.vue";

import { ref, onMounted } from 'vue'




const variants = [
  {
    text: 'Plan better.',
    bg: 'bg-[#435EF2]/30'
  },
  {
    text: 'Level up faster.',
    bg: 'bg-[#A480F2]/50'
  },
  {
    text: 'Rise with purpose',
    bg: 'bg-[#435EF2]/30'
  },
]

const currentIndex = ref(0)

onMounted(() => {
  setInterval(() => {
    currentIndex.value = (currentIndex.value + 1) % variants.length
  }, 3000)
})
</script>

<template>
  <div class="flex-grow flex items-center justify-center flex-col gap-12 mt-14 px-4">
    <!-- Hero -->
    <div id="form-waitlist"  class="flex flex-col justify-center gap-5 items-center">
      <h1
          class="space-grotesk leading-[1.8] text-white text-3xl sm:text-[32px] lg:text-[38px] xl:text-[50px] 2xl:text-[60px] sm:leading-[1.5] text-center heading-animate"
      >
        Ask. Accept. Achieve.
        <br>
        Plan better, Level up faster
      </h1>

      <!-- Subtitle -->
      <p
          class="inter text-white/80 text-sm sm:text-lg text-center md:max-w-[32rem] max-w-[22rem]"
      >
        Arises AI schedules the right tasks so every day moves you up the ladder.
      </p>
    </div>

    <!-- Waitlist -->
    <div class="w-full xl:w-1/3 mt-2 fade-in-animate">
      <h2 class="inter text-white text-lg sm:text-xl text-center">
        Coming soon
      </h2>

      <FormWaitlist />
    </div>

  </div>
</template>

<style scoped>
.fade-text {
  text-align: center;
  min-width: 240px; /* fixe la largeur pour éviter les "sauts" */
  display: inline-block;
  transition: all 0.6s ease;
}
</style>
