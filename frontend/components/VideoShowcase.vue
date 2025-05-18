<script setup lang="ts">

const props = defineProps({
  url: {
    type: String,
    default: '/video/arises-extension-v2.mp4'
  }
})

</script>

<template>
  <div class=" 2xl:w-[90%] mx-auto w-full border-6 bg-transparent border-[#A480F2]/30 rounded-xl focus:outline-none focus:border-[#A480F2] backdrop-blur-xl backdrop-blur-xl">
    <!-- Video showcase -->
    <div class="bg-[#16213e] px-4 py-2 flex items-center ">
      <div class="flex gap-2">
        <div class="w-3 h-3 rounded-full bg-red-500"></div>
        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
        <div class="w-3 h-3 rounded-full bg-green-500"></div>
      </div>
    </div>

    <!-- Video -->
    <video controls autoplay muted loop class="w-full">
      <source :src="url" type="video/mp4" />
    </video>
  </div>
</template>

<style scoped>

</style>