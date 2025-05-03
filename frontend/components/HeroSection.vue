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
  <div class="flex items-center justify-center flex-col gap-12 mt-14 px-4">
    <!-- Hero -->
    <div class="flex flex-col justify-center gap-5 items-center">
      <h1
          class="inter leading-[1.8] text-white text-2xl sm:text-[32px] lg:text-[38px] xl:text-[50px] 2xl:text-[60px] sm:leading-[1.5] text-center heading-animate"
      >
        Your smart AI calendar helps <br />
        to
        <span
            :class="`${variants[currentIndex].bg} px-2 text-center rounded-lg transition-all duration-700 ease-in-out fade-text`"
        >
        {{ variants[currentIndex].text }}
      </span>
      </h1>

      <!-- Subtitle -->
      <p
          class="inter text-white/80 text-sm sm:text-lg text-center max-w-[30rem] mt-5 fade-in-animate"
      >
        Ask. Accept. Achieve. Arises schedules the right tasks so every day moves you up the ladder.
      </p>
    </div>

    <!-- Waitlist -->
    <div class="w-full xl:w-1/3 mt-2 fade-in-animate">
      <h2 class="inter text-white text-lg sm:text-xl text-center pulse-animate">
        Coming soon
      </h2>

      <FormWaitlist />
    </div>

    <USeparator
        color="primary"
        class="w-full text-[#A480F2] separator-animate"
    />

    <p
        class="inter text-white text-sm sm:text-lg text-center max-w-[30rem] fade-in-animate"
    >
      Try the Chrome extension
    </p>

    <div class="size-6 animate-bounce ...">
      <UIcon
          class="text-[#A480F2] sm:text-4xl text-2xl animate-bounce"
          name="i-heroicons-arrow-down" />
    </div>

    <VideoShowcase />

    <ChromeButton />
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
