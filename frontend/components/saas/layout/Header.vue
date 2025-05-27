<script setup lang="ts">

</script>

<template>
  <div class="border-b border-b-[1px] border-grey-calendar py-3">

    <div class="flex flex-row items-center justify-between">

      <div class="flex flex-row items-center gap-3 ml-5">
        <UIcon name="i-lucide-panel-left" class="text-grey w-[18px] h-[18px]"/>
        <NuxtImg
            src="/logo_without_text.svg"
            alt="Logo"
            class="w-8 h-8"
        />
      </div>

      <div>
        <div class="flex flex-row items-center gap-3 mr-5">
          <UIcon name="i-lucide-bell" class="text-grey w-[18px] h-[18px]"/>
          <UIcon name="i-lucide-info" class="text-grey w-[18px] h-[18px]"/>
        </div>
      </div>

    </div>




  </div>
</template>

<style scoped>

</style>