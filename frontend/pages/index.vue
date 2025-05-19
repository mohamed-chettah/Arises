<script setup lang="ts">

import Navbar from "~/components/Navbar.vue";
import VideoShowcase from "~/components/VideoShowcase.vue";
</script>

<template>
  <section class="background-image">

    <video autoplay muted loop class="back-video">
      <source src="/video/aurora.mp4" type="video/mp4" />
      Your browser does not support the video tag.
    </video>

    <Navbar />

    <div class="flex flex-col gap-[70px]">
      <HeroSection  />

      <VideoShowcase url="/video/video.mp4" />

    </div>

    <BenefitApp/>


    <Footer />
  </section>

</template>


<style>

</style>
