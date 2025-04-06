<script setup lang="ts">

import Navbar from "~/components/Navbar.vue";
</script>

<template>
  <section class="background pt-5 2xl:px-80 xl:px-40 lg:px-32 sm:px-10 bg-[#000000] min-h-screen h-full">

    <Navbar />

    <HeroSection />
  </section>

</template>


<style>
.background {
  background: url('public/images/img.png') 80% / cover no-repeat;
  box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
}

</style>
