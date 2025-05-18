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
  <div class="bg-[#1D1340] p-4 rounded-[8px]">
    <NuxtImg class="w-[350px]" :src="image" />

    <h3 class="space-grotesk text-[#A480F2] text-[20px] font-bold mt-4">{{ title }}</h3>
    <p class="inter text-white 2xl:text-[14px] text-[12px] mt-2 2xl:w-62 w-56">{{ description }}</p>
  </div>
</template>

<style scoped>

</style>