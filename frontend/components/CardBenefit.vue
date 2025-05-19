<script setup lang="ts">

const props = defineProps({
  title: {
    type: String,
    default: 'Chat AI'
  },
  description: {
    type: String,
    default: 'Think, organize, and turn thoughts into clear action plans.'
  },
  image: {
    type: String,
  }
})

</script>

<template>
  <div class="bg-[#1D1340]/70 p-4 rounded-[8px]">
    <NuxtImg class="sm:w-[350px] w-[320px]" :src="image" />

    <h3 class="space-grotesk text-[#A480F2] sm:text-[20px] text-[16px] font-bold mt-4">{{ title }}</h3>
    <p class="inter text-white 2xl:text-[14px] sm:text-[12px] text-[10px] mt-2 2xl:w-62 md:w-56 w-42">{{ description }}</p>
  </div>
</template>

<style scoped>

</style>