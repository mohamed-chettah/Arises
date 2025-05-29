<script setup lang="ts">
import type { DropdownMenuItem } from '@nuxt/ui'

const items = ref<DropdownMenuItem[]>([
  {
    label: 'Profile',
    icon: 'i-lucide-user'
  },
  {
    label: 'Logout',
    icon: 'i-lucide-cog',
    onSelect() {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      navigateTo('/login')
    }
  }
])
</script>

<template>
  <div class="border-b border-b-[1px] border-grey-calendar py-3">

    <div class="flex flex-row items-center justify-between">

      <div class="flex flex-row items-center gap-3 ml-5">
        <UDropdownMenu
            :items="items"
            :content="{
              align: 'start',
              side: 'bottom',
              sideOffset: 8
              }"
            :ui="{
                content: 'w-48'
              }"
        >
          <UButton class="cursor-pointer w-9 h-8 py-2.5 bg-purple/20 rounded-lg inline-flex flex-col justify-center items-center gap-2.5" >
            MC
          </UButton>
        </UDropdownMenu>
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