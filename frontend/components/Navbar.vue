<script setup lang="ts">

</script>

<template>

  <div class="flex justify-between items-center">
    <NuxtImg @click="navigateTo('/')" class="w-20 cursor-pointer" src="/images/Arise.svg" />

    <div class="text-white text-sm inter">
      <NuxtLink active-class="active" to="/" class="hover:text-secondary px-4 py-2">Home</NuxtLink>
      <NuxtLink active-class="active" to="/ranking" class="hover:text-gray-900 px-4 py-2">Ranking</NuxtLink>
      <NuxtLink active-class="active" to="/what" class="hover:text-gray-900 px-4 py-2">What</NuxtLink>
    </div>

    <ChromeButton class-w="w-[18%]" text=" " class-t="bg-[#445EF2] hover:bg-[#445EF2]/80 cta w-[70%]inter"/>
  </div>

</template>

<style scoped>
.active{
  color: #A480F2;
}
</style>