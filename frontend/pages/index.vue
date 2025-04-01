<script setup lang="ts">

import Navbar from "~/components/Navbar.vue";
</script>

<template>
  <section  class="background pt-5 px-40 bg-[#000000] h-full">

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
