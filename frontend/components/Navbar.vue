<script setup lang="ts">

import ChromeButton from "~/components/ChromeButton.vue";
</script>

<template>

  <div class="md:flex hidden justify-between items-center">
    <NuxtImg @click="navigateTo('/')" class="w-20 cursor-pointer" src="/images/Arise.svg" />

    <div class="flex gap-5 flex-row items-center">
      <div class="text-white text-[14px] inter">
        <NuxtLink active-class="active" to="/" class="hover:text-[#A480F2] px-4 py-2">Home</NuxtLink>
        <NuxtLink active-class="active" to="/ranking" class="hover:text-[#A480F2] px-4 py-2">Extension</NuxtLink>
        <NuxtLink active-class="active" to="/what" class="hover:text-[#A480F2] px-4 py-2">What</NuxtLink>
      </div>

      <ChromeButton text="Chrome Extension" class-t="text-[14px] bg-linear-to-r from-[#445EF2] to-[#27368C] cursor-pointer inter"/>

    </div>
  </div>


<!--  Mobile -->

  <div class="md:hidden block absolute bottom-0 left-0 right-0 bg-primary">
    <NuxtImg @click="navigateTo('/')" class="w-20 cursor-pointer" src="/images/Arise.svg" />

  </div>
</template>

<style scoped>
.active{
  color: #A480F2;
}
</style>