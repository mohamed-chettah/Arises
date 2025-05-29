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
  <div class="flex flex-col gap-2 ">

    <NuxtLink :to="link" class="w-full">
      <div class="padding inline-flex justify-start items-center gap-3 cursor-pointer hover:bg-purple/20 " :class="active ? 'bg-purple/20 rounded-[8px]' : ''">
        <UIcon :name="icon" :class="active ? 'text-white'  : ''" class="text-grey w-[20px] h-[20px]"></UIcon>
      </div>
    </NuxtLink>

  </div>

</template>

<style scoped>
.padding {
  display: flex;
  height: 38px;
  padding: 8px 8px 8px 8px;
  align-items: center;
  flex-shrink: 0;
}
</style>