<script setup lang="ts">
import { useRuntimeConfig, useRoute, useFetch, useToast } from '#imports';

const route = useRoute();
const toast = useToast();
const { public: { apiBase } } = useRuntimeConfig();

const loading = ref(true);

// 👉 fetch côté SSR, mais pas de toast ici
const { data, error } = await useFetch('/verify-mail', {
  baseURL: apiBase,
  method: 'POST',
  body: {
    token: route.query.token,
    mail: route.query.mail,
  },
  server: true,
  lazy: true,
  watch: false,
});

onMounted(() => {
  loading.value = false;

  if (error.value) {
    toast.add({
      title: 'Error',
      description: 'An error occurred. Please try again later.',
      color: 'error'
    });
  } else if (data.value?.already_verified) {
    toast.add({
      title: 'Already Verified',
      description: 'Your email is already verified.',
      color: 'warning'
    });
  } else {
    toast.add({
      title: 'Success',
      description: 'You are officially on the waiting list!',
      color: 'success'
    });
  }
});
</script>


<template>
  <section class="background-image ">
    <Navbar />

    <div class="flex min-h-screen items-center justify-center" v-if="loading">
      <Icon name="svg-spinners:180-ring-with-bg" class="h-10 w-10" />
    </div>
    <div class="flex items-center justify-center flex-col gap-12 mt-10 px-4" v-else>

      <div class="text-white text-center flex flex-col gap-5">
        <h1 class="space-grotesk text-white text-[28px] sm:text-[40px] 2xl:text-[48px] xl:leading-tight leading-wide text-center heading-animate">
          Welcome to the Arises waitlist
        </h1>

        <p class="inter">We’ll email you as soon as the app is ready.</p>

        <p class="inter">In the meantime, try our Chrome extension:</p>
      </div>

      <VideoShowcase />

      <ChromeButton />

      <Footer />
    </div>

  </section>
</template>
