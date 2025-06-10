<script setup lang="ts">

useHead({
  title:"Arises - The AI Scheduler",
  script: [
    {
      src: 'https://www.googletagmanager.com/gtag/js?id=G-NR5YSZPY2Q',
      async: true
    },
    {
      children: `
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-NR5YSZPY2Q');
      `,
      type: 'text/javascript'
    }
  ],
  link: [
    {
      rel: 'preload',
      as: 'image',
      href: '/images/img.png'
    }
  ]
});


</script>

<template>
  <UApp>
    <NuxtPage />
  </UApp>
</template>
