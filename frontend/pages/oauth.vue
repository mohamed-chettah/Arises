<script setup lang="ts">
import Navbar from "~/components/Navbar.vue";
const route = useRoute();
const token = route.query.token;

onMounted(() => {
  setTimeout(() => {
    const opened = window.open("chrome-extension://aheohjodpllofjdihniljfkppcacpeib/views/oauth.html?token=" + token);
    if (!opened) {
      console.warn("Popup bloquée. Tu peux l'ouvrir manuellement.");
    }
  }, 100); // <= petit délai permet parfois de bypass le blocage (pas 100% fiable)
});

</script>

<template>
  <section class="background pt-5 2xl:px-80 xl:px-40 lg:px-32 sm:px-10 bg-[#000000] min-h-screen h-full text-white">

    <Navbar />

    <div class="mt-20 flex flex-col items-center text-center px-4">
      <h1 class="text-3xl font-bold mt-6 mb-3">Welcome</h1>
      <p class="text-base mb-2">
        You are now connected to <strong>Arises</strong> — your space for deep focus and progress.
      </p>
      <p class="text-sm text-gray-300">
        Minimize distractions, stay on track, and let the extension keep you aligned with your goals. ✨
      </p>

    </div>

  </section>
</template>

<style>
.background {
  background: url('/images/img.png') center/cover no-repeat;
  box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
}
</style>
