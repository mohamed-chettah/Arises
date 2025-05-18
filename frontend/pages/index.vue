<script setup lang="ts">

import Navbar from "~/components/Navbar.vue";
import VideoShowcase from "~/components/VideoShowcase.vue";
</script>

<template>
  <section class="background-image">

    <Navbar />

    <div class="flex flex-col gap-[70px]">
      <HeroSection  />

      <VideoShowcase url="/video/arises-mvp.mp4" />

    </div>

    <BenefitApp/>


    <Footer />
  </section>

</template>


<style>

</style>
