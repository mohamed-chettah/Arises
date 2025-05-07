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
    <div class="padding inline-flex justify-start items-center gap-3" :class="active ? 'bg-purple/20 rounded-[8px]' : ''">
      <UIcon :name="icon" :class="active ? 'text-white'  : ''" class="text-grey w-[18px] h-[18px]"></UIcon>
      <p :class="active ? 'text-white'  : ''" class="text-center text-[14px] justify-start text-grey inter">{{ title }}</p>
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