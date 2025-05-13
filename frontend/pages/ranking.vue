<script setup lang="ts">

import Navbar from "~/components/Navbar.vue";
import type { TableColumn } from '@nuxt/ui'

const data = ref([
  {
    position: 1 ,
    username: '2024-03-11T15:30:00',
    rank: '4597',
    focusTime: 594
  },
  {
    rank: '4599',
    position: 1 ,
    username: '2024-03-11T15:30:00',
    focusTime: 594
  },
  {
    position: 1 ,
    username: '2024-03-11T15:30:00',
    rank: '4597',
    focusTime: 594
  },
  {
    position: 1 ,
    username: '2024-03-11T15:30:00',
    rank: '4597',
    focusTime: 594
  },
  {
    position: 1 ,
    username: '2024-03-11T15:30:00',
    rank: '4597',
    focusTime: 594
  },
  {
    position: 1 ,
    username: '2024-03-11T15:30:00',
    rank: '4597',
    focusTime: 594
  },
  {
    position: 1 ,
    username: '2024-03-11T15:30:00',
    rank: '4597',
    focusTime: 594
  },
  {
    position: 1 ,
    username: '2024-03-11T15:30:00',
    rank: '4597',
    focusTime: 594
  },
  {
    position: 1 ,
    username: '2024-03-11T15:30:00',
    rank: '4597',
    focusTime: 594
  }
])
</script>

<template>
  <section class="background-image">

    <Navbar />


    <div class="flex gap-8 flex-col items-center justify-center ">

      <h1 class="text-center bank-gothic text-white mt-16 text-4xl font-bold mb-6"> Daily Ranking </h1>

      <UTable  :ui="{
    base: 'text-white rounded-lg ',
    th: 'text-white bg-[#A480F2]/60 text-[16px] font-bold',
    td: 'text-white'
  }"
              loading loading-color="primary" loading-animation="carousel"  :data="data" class="text-white w-[70%] " />
    </div>

    <div class="flex flex-col justify-center items-center mt-5 gap-8">
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


    <Footer />
  </section>

</template>


<style>

</style>
