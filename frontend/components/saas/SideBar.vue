<script setup lang="ts">
import LinkSideBar from "~/components/ui/LinkSideBar.vue";
</script>

<template>
  <section class="min-h-screen bg-background border-r border-r-[1px] border-grey-calendar flex flex-col">

    <!-- Logo / nom -->
    <div class="flex flex-col items-center py-3">
      <div class="w-[240px] inline-flex justify-between items-center">
        <div class="flex justify-start items-center gap-3.5">
          <div class="w-9 h-8 py-2.5 bg-purple/20 rounded-lg inline-flex flex-col justify-center items-center gap-2.5">
            <div class="text-center justify-start text-white text-sm font-bold bank-gothic">MC</div>
          </div>
          <div class="text-center justify-start text-[14px] text-white font-bold bank-gothic">Mohamed C</div>
        </div>
        <div class="w-5 h-5 relative overflow-hidden">
          <UIcon name="i-heroicons-chevron-down" class="text-grey"></UIcon>
        </div>
      </div>
    </div>

    <!-- Partie centrale + footer -->
    <div class="flex flex-col justify-between flex-1 border-t border-t-[1px] border-grey-calendar pt-2">

      <!-- Liens -->
      <div class="flex flex-col items-center gap-1">
        <LinkSideBar title="Home" icon="i-lucide-house" link="/" :active="true" />
        <LinkSideBar title="Tasks" icon="i-lucide-gallery-horizontal-end" link="/" />
        <LinkSideBar title="Integrations" icon="i-lucide-puzzle" link="/" />
        <LinkSideBar title="Usage" icon="i-lucide-signal-high" link="/" />
        <LinkSideBar title="Learn" icon="i-lucide-lightbulb" link="/" />
      </div>

      <!-- Footer en bas -->
      <div class="border-t border-t-[1px] border-grey-calendar py-5">
        <a href="https://chrome.google.com/webstore" target="_blank" class="hover:underline">
          <span class="flex items-center flex-row justify-center gap-2 text-grey inter">
             <LinkSideBar title="Try Chrome Extension" icon="i-lucide-chrome" link="/" />
          </span>
        </a>
      </div>

    </div>
  </section>
</template>

<style scoped>
</style>
