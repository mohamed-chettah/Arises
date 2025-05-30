<script setup lang="ts">
import LoginForm from "~/components/saas/auth/LoginForm.vue";

definePageMeta({
  middleware: [
    'auth',
  ],
})
</script>

<template>
  <div class="flex min-h-screen bg-background">

    <LoginForm class="w-64 shrink-0" />

  </div>
</template>

<style scoped>

</style>