<script setup lang="ts">

import ChromeButton from "~/components/ChromeButton.vue";
</script>

<template>

  <div class="flex justify-between items-center">
    <NuxtImg @click="navigateTo('/')" class="w-20 cursor-pointer" src="/images/Arise.svg" />

    <div class="flex gap-5 flex-row items-center">
      <div class="text-white text-[16px] inter">
        <NuxtLink active-class="active" to="/" class="hover:text-[#A480F2] px-4 py-2">Home</NuxtLink>
        <NuxtLink active-class="active" to="/ranking" class="hover:text-[#A480F2] px-4 py-2">Extension</NuxtLink>
        <NuxtLink active-class="active" to="/what" class="hover:text-[#A480F2] px-4 py-2">What</NuxtLink>
      </div>

      <ChromeButton text="Chrome Extension" class-t="bg-linear-to-r from-[#445EF2] to-[#27368C] cursor-pointer inter"/>

    </div>
    </div>

</template>

<style scoped>
.active{
  color: #A480F2;
}
</style>