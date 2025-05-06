<script setup lang="ts">

const props = defineProps({
  title: {
    type: String,
    default: "Link"
  },
  icon: {
    type: String,
    default: "i-heroicons-link"
  },
  link: {
    type: String,
    default: "/"
  },
  active : {
    type: Boolean,
    default: false
  }
})

</script>

<template>
  <div class="flex flex-col gap-2">
    <div class="padding inline-flex justify-start items-center gap-3.5" :class="active ? 'bg-purple/20 rounded-[8px]' : ''">
      <UIcon :name="icon" class="text-grey w-[22px] h-[22px]"></UIcon>
      <div class="text-center justify-start text-grey inter">{{ title }}</div>
    </div>
  </div>

</template>

<style scoped>
.padding {
  display: flex;
  width: 240px;
  height: 38px;
  padding: 8px 8px 8px 8px;
  align-items: center;
  flex-shrink: 0;
}
</style>