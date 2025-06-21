<script setup lang="ts">

import Navbar from "~/components/landing/Navbar.vue";
import VideoBackground from "~/components/VideoBackground.vue";
import Footer from "~/components/landing/Footer.vue";

</script>
<template>
  <section class="flex flex-col min-h-screen background-image text-white sm:px-0 px-4">

    <VideoBackground />
    <Navbar />

    <main class="flex-grow">
      <h1 class="space-grotesk mt-20 text-4xl md:text-5xl font-bold mb-6">What is Arises?</h1>

      <div class="max-w-2xl text-lg md:text-xl text-gray-300 flex flex-col gap-4 pb-16">
        <p>
          Arises is your ally in the battle against distraction and procrastination.
        </p>

        <p>
          It starts with a <strong>browser extension</strong> that blocks distractions and turns your tasks into a gamified experience —
          rewarding focus and daily consistency.
        </p>

        <p>
          Then comes the <strong>real game-changer</strong> — the productivity companion.
          An assistant you can actually chat with.
          You say what you want to achieve — it builds the plan, schedules your sessions, and adapts in real time to keep you moving forward.
        </p>

        <p>
          Arises is not just a tool. It’s a system to help you act, evolve, and rise.
          <br>
          Ask - Accept - Achieve.
        </p>
      </div>
    </main>

    <Footer />
  </section>
</template>



<style>

</style>
