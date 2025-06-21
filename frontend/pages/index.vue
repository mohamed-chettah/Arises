<script setup lang="ts">

import Navbar from "~/components/landing/Navbar.vue";
import VideoShowcase from "~/components/landing/VideoShowcase.vue";
import VideoBackground from "~/components/VideoBackground.vue";
import HeroSection from "~/components/landing/HeroSection.vue";
import Footer from "~/components/landing/Footer.vue";

</script>

<template>
  <section class="background-image">

    <VideoBackground />

    <Navbar />

    <div class="flex flex-col gap-[70px]">
      <HeroSection  />

      <VideoShowcase url="/video/arises-mvpp.mp4" />

    </div>

    <BenefitApp/>


    <Footer />
  </section>

</template>


<style>

</style>
