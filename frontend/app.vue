<script setup lang="ts">

useHead({
  title:"Arises",
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

        gtag('config', 'G-NR5YSZPY2Q'');
      `,
      type: 'text/javascript'
    }
  ],
  htmlAttrs: {
    class: 'nuxt-ui-scrollbars',
  },
});


</script>

<template>
  <UApp>
    <NuxtPage />
  </UApp>
</template>
