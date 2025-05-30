<script setup lang="ts">
import { ref, onMounted } from 'vue'

const isLoaded = ref(false)

const onVideoLoad = () => {
  isLoaded.value = true
}
</script>

<template>
  <video
    autoplay
    muted
    loop
    playsinline
    class="video"
    poster="/images/aurora-poster.png"
  >
    <source src="/video/aurora.mp4" type="video/mp4" />
  </video>
</template>

<style scoped>
.video {
  position: fixed;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: -1;
}
</style>