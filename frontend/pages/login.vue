<script setup lang="ts">
import LoginForm from "~/components/saas/auth/LoginForm.vue";

definePageMeta({
  middleware: [
    'login',
  ],
})
</script>

<template>
  <div class="flex min-h-screen">

    <VideoBackground />

    <LoginForm class="w-64 shrink-0" />

  </div>
</template>

<style scoped>

</style>