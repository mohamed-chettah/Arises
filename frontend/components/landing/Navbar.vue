<script setup lang="ts">
import { ref } from 'vue';
import ChromeButton from "~/components/landing/ChromeButton.vue";

const isMenuOpen = ref(false);

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

const closeMenu = () => {
  isMenuOpen.value = false;
};
</script>

<template>
  <!-- Desktop Navigation -->
  <div class="md:flex hidden justify-between items-center">
    <NuxtImg @click="navigateTo('/')" class="w-20 cursor-pointer" src="/images/Arise.svg" />

    <div class="flex gap-5 flex-row items-center">
      <div class="text-white text-[14px] inter">
        <NuxtLink active-class="active" to="/" class="hover:text-[#A480F2] px-4 py-2">Home</NuxtLink>
        <NuxtLink active-class="active" to="/ranking" class="hover:text-[#A480F2] px-4 py-2">Extension</NuxtLink>
        <NuxtLink active-class="active" to="/what" class="hover:text-[#A480F2] px-4 py-2">What</NuxtLink>
      </div>

      <NuxtLink @click="closeMenu" active-class="active" to="/login" class="text-white hover:text-[#A480F2] text-[14px] py-2 inter cta shadow-xl bg-gradient-to-r from-[#A480F2] via-[#9977E2] to-[#5F4A8C] hover:bg-[#A480F2]/70 px-4 rounded-lg">Login</NuxtLink>
    </div>
  </div>

  <!-- Mobile Navigation -->
  <div class="flex md:hidden justify-between items-center sm:mx-0 mx-4">
    <NuxtImg @click="navigateTo('/')" class="w-16 cursor-pointer" src="/images/Arise.svg" />

    <!-- Hamburger Menu Button -->
    <button @click="toggleMenu" class="text-white p-2 cursor-pointer hover:text-[#A480F2]" aria-label="Toggle menu">
      <svg v-if="!isMenuOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
      </svg>
      <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </div>

  <!-- Mobile Menu Dropdown -->
  <div v-if="isMenuOpen" class="md:hidden bg-[#121212]  absolute inset-x-0 top-[60px] z-50 shadow-lg transition-all duration-300 ease-in-out mt-5 mx-5 rounded-xl bg-[#1D1340]">
    <div class="flex flex-col p-4 space-y-2">
      <NuxtLink @click="closeMenu" active-class="active" to="/" class="text-white hover:text-[#A480F2] text-[14px] py-2 inter">Home</NuxtLink>
      <NuxtLink @click="closeMenu" active-class="active" to="/ranking" class="text-white hover:text-[#A480F2] text-[14px] py-2 inter">Extension</NuxtLink>
      <NuxtLink @click="closeMenu" active-class="active" to="/what" class="text-white hover:text-[#A480F2] text-[14px] py-2 inter">What</NuxtLink>

      <NuxtLink @click="closeMenu" active-class="active" to="/login" class="text-white hover:text-[#A480F2] text-[14px] py-2 inter">Login</NuxtLink>
    </div>
  </div>
</template>

<style scoped>
.active {
  color: #A480F2;
}
</style>