<script setup lang="ts">
import FormWaitlist from "~/components/FormWaitlist.vue";
import VideoShowcase from "~/components/VideoShowcase.vue";
import ChromeButton from "~/components/ChromeButton.vue";
</script>

<template>
  <div class="flex items-center justify-center flex-col gap-12 mt-10 px-4">
    <!-- Hero -->
    <div class="flex flex-col justify-center gap-5 items-center">
      <h1
          class="bank-gothic leading-[1.6] text-white text-2xl sm:text-[32px] lg:text-[38px] xl:text-[50px] 2xl:text-[60px] sm:leading-[1.5] text-center heading-animate"
      >
        Your smart AI calendar<br />
        <span class="bg-[#435EF2]/30 px-3 py-1 rounded-lg highlight-animate"
        >Plan better.</span
        >
        and
        <br class="lg:hidden sm:block" />
        <span class="bg-[#A480F2]/50 px-3 py-1 rounded-lg highlight-animate"
        >Level up faster.</span
        >
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

    <div class="size-6 animate-bounce ...">
      <UIcon
          class="text-[#A480F2] sm:text-4xl text-2xl animate-bounce"
          name="i-heroicons-arrow-down" />
    </div>

    <VideoShowcase />

    <ChromeButton />
  </div>

  <Footer />
</template>

<style scoped>

</style>
